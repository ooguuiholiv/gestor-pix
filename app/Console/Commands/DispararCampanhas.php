<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Campanha;
use App\Models\Cliente;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\SendMessageController;
use Illuminate\Http\Request;

class DispararCampanhas extends Command
{
    protected $signature = 'campanhas:disparar';
    protected $description = 'Dispara campanhas de cobrança para clientes';

    public function __construct()
    {
        parent::__construct();
    }

        
  
  
   
    
    public function handle()
    {
        $hoje = Carbon::now();
        $dataAtual = $hoje->toDateString();
        $horaAtual = $hoje->format('H:i');
    
        Log::info('Disparando campanhas para ' . $hoje->toDateString() . ' às ' . $horaAtual);
    
        // Campanhas com data e horário específicos
        $campanhasComDataEHorario = Campanha::whereDate('data', $dataAtual)
            ->where('horario', $horaAtual)
            ->get();
    
        // Campanhas com horário apenas
        $campanhasComHorarioApenas = Campanha::whereNull('data')
            ->where('horario', $horaAtual)
            ->get();
    
        // Mesclar as campanhas
        $campanhas = $campanhasComDataEHorario->merge($campanhasComHorarioApenas);
    
        foreach ($campanhas as $campanha) {
            // Verificar se a campanha já foi executada hoje e se não deve ser enviada diariamente
            if ($campanha->ultima_execucao) {
                $ultimaExecucao = Carbon::parse($campanha->ultima_execucao);
                if ($ultimaExecucao->isToday() && !$campanha->enviar_diariamente) {
                    Log::info('Campanha já enviada hoje: ' . $campanha->nome);
                    continue;
                }
            }
    
            Log::info('Disparando campanha: ' . json_encode($campanha));
    
            // Obter clientes com base na origem dos contatos
            switch ($campanha->origem_contatos) {
                case 'todos':
                    Log::info('Obtendo todos os clientes para o usuário: ' . $campanha->user_id);
                    $clientes = Cliente::where('user_id', $campanha->user_id)->get();
                    break;
                case 'vencidos':
                    $hoje = now()->format('Y-m-d');
                    Log::info('Obtendo clientes vencidos para o usuário: ' . $campanha->user_id . ' até a data: ' . $hoje);
                    $clientes = Cliente::where('user_id', $campanha->user_id)
                                        ->where('vencimento', '<', $hoje)
                                        ->get();
                    break;
                case 'vencem_hoje':
                    $hoje = now()->format('Y-m-d');
                    Log::info('Obtendo clientes que vencem hoje para o usuário: ' . $campanha->user_id . ' na data: ' . $hoje);
                    $clientes = Cliente::where('user_id', $campanha->user_id)
                                        ->where('vencimento', $hoje)
                                        ->get();
                    break;
                case 'ativos':
                    Log::info('Obtendo todos os clientes ativos para o usuário: ' . $campanha->user_id);
                    $clientes = Cliente::where('user_id', $campanha->user_id)->get();
                    break;
                case 'servidores':
                    Log::info('Obtendo clientes dos servidores para o usuário: ' . $campanha->user_id);
                    $clientes = Cliente::whereIn('id', $campanha->contatos)->get();
                    break;
                case 'manual':
                    Log::info('Obtendo clientes específicos para a campanha: ' . $campanha->id);
                    $clientes = Cliente::whereIn('id', $campanha->contatos)->get();
                    break;
                default:
                    Log::info('Origem de contatos desconhecida: ' . $campanha->origem_contatos);
                    $clientes = collect(); // Coleção vazia
                    break;
            }
    
            if ($clientes->isEmpty()) {
                Log::info('Nenhum cliente encontrado para a campanha: ' . $campanha->nome);
                continue;
            }
    
            Log::info('Clientes obtidos: ' . json_encode($clientes));
    
            foreach ($clientes as $cliente) {
                Log::info('Disparando campanha para o cliente: ' . json_encode($cliente));
                $this->dispararCampanhaParaCliente($campanha, $cliente);
            }
    
            // Atualizar a última execução da campanha
            $campanha->ultima_execucao = Carbon::now();
            $campanha->save();
    
            $this->info('Campanha disparada: ' . $campanha->nome);
            Log::info('Campanha disparada: ' . $campanha->nome);
        }
    }

    protected function dispararCampanhaParaCliente($campanha, $cliente)
    {
        $dadosCliente = [
            '{nome_cliente}' => $cliente->nome ?? 'Nome do Cliente',
            '{telefone_cliente}' => $cliente->whatsapp ?? '(11) 99999-9999',
            '{notas}' => $cliente->notas ?? 'Notas',
            '{vencimento_cliente}' => Carbon::parse($cliente->vencimento)->format('d/m/Y') ?? 'Vencimento do Cliente',
            '{data_atual}' => Carbon::now()->format('d/m/Y'),
        ];

        $conteudoCliente = $this->substituirPlaceholders($campanha->mensagem, $dadosCliente);

        Log::info('Enviando mensagem para ' . $cliente->whatsapp);
        Log::info('Mensagem: ' . $conteudoCliente);

        $sendMessageController = new SendMessageController();
        if ($campanha->arquivo) {
            $fileExtension = pathinfo($campanha->arquivo, PATHINFO_EXTENSION);

            Log::info('Enviando arquivo: ' . $campanha->arquivo);
            if ($fileExtension === 'pdf') {
                $sendMessageController->sendMediaWithCurl(new Request([
                    'phone' => $cliente->whatsapp,
                    'media' => $campanha->arquivo,
                    'user_id' => $campanha->user_id,
                    'caption' => $conteudoCliente,
                    'fileName' => basename($campanha->arquivo),
                ]));
            } else {
                $sendMessageController->sendMedia(new Request([
                    'phone' => $cliente->whatsapp,
                    'media' => $campanha->arquivo,
                    'user_id' => $campanha->user_id,
                    'caption' => $conteudoCliente,
                    'fileName' => basename($campanha->arquivo),
                ]));
            }
        } else {
            $sendMessageController->sendMessageWithoutAuth(new Request([
                'phone' => $cliente->whatsapp,
                'message' => $conteudoCliente,
                'user_id' => $campanha->user_id,
            ]));
        }

        Log::info('Mensagem enviada para ' . $cliente->whatsapp);
    }

    private function substituirPlaceholders($conteudo, $dados)
    {
        foreach ($dados as $placeholder => $valor) {
            $conteudo = str_replace($placeholder, $valor, $conteudo);
        }

        return $conteudo;
    }
}