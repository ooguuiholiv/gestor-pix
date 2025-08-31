<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cliente;
use App\Models\Template;
use App\Models\Plano;
use App\Models\User;
use App\Models\CompanyDetail;
use App\Models\ScheduleSetting;
use App\Models\Pagamento;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\SendMessageController;
use Illuminate\Http\Request;
use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;

class VerificarClientesVencidos extends Command
{
    protected $signature = 'clientes:verificar-vencidos';
    protected $description = 'Verifica os clientes vencidos e envia notificações de cobrança';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $settings = ScheduleSetting::all();
        $hoje = Carbon::now();
        $horaAtual = $hoje->format('H:i');

        $tresDiasAtras = $hoje->copy()->subDays(3);
        $cincoDiasAtras = $hoje->copy()->subDays(5);
        $tresDiasFuturo = $hoje->copy()->addDays(3);

        foreach ($settings as $setting) {
            $executionTime = Carbon::createFromFormat('H:i:s', $setting->execution_time)->format('H:i');

            if ($horaAtual !== $executionTime) {
                continue;
            }

            $userId = $setting->user_id;
            $finalidade = $setting->finalidade;

            switch ($finalidade) {
                case 'cobranca_3_dias_atras':
                    $clientes = Cliente::where('user_id', $userId)->whereDate('vencimento', $tresDiasAtras->toDateString())->get(); 
                    break;
                case 'cobranca_5_dias_atras':
                    $clientes = Cliente::where('user_id', $userId)->whereDate('vencimento', $cincoDiasAtras->toDateString())->get();
                    break;
                case 'cobranca_hoje':
                    $clientes = Cliente::where('user_id', $userId)->whereDate('vencimento', $hoje->toDateString())->get();
                    break;
                case 'cobranca_3_dias_futuro':
                    $clientes = Cliente::where('user_id', $userId)->whereDate('vencimento', $tresDiasFuturo->toDateString())->get();
                    break;
                default:
                    $clientes = collect();
                    break;
            }

            $this->processarClientes($clientes, $finalidade, $setting);

            $this->info('Verificação de clientes vencidos concluída para user_id: ' . $userId . ' com finalidade: ' . $finalidade);
        }
    }

    protected function processarClientes($clientes, $finalidade, $setting)
    {
        foreach ($clientes as $cliente) {
            $this->notifyClient($cliente, $finalidade, $setting);
            Log::info('Notificação enviada para cliente_id: ' . $cliente->id);
        }
    }

        
  
       
  
    private function notifyClient($cliente, $finalidade, $setting)
    {
        $template = Template::where('finalidade', $finalidade)
            ->where('user_id', $cliente->user_id)
            ->first();
    
        if (!$template) {
            $template = Template::where('finalidade', $finalidade)
                ->whereNull('user_id')
                ->firstOrFail();
        }
    
        $company = CompanyDetail::where('user_id', $cliente->user_id)->first();
        $nomeEmpresa = $company ? $company->company_name : '{nome_empresa}';
        $whatsappEmpresa = $company ? $company->company_whatsapp : '{whatsapp_empresa}';
    
        $plano = Plano::find($cliente->plano_id);
        $nomePlano = $plano ? $plano->nome : 'Nome do Plano';
        $valorPlano = $plano ? $plano->preco : 'Valor do Plano';
    
        $saudacao = $this->getSaudacao();
        $textExpirate = $this->getTextExpirate($cliente->vencimento);
    
        if ($company->not_gateway) {
            // Gerar um ID de pagamento aleatório de 11 números
            $mercadoPagoId = str_pad(mt_rand(0, 99999999999), 11, '0', STR_PAD_LEFT);
    
            // Criar o registro de pagamento
            $pagamento = Pagamento::create([
                'cliente_id' => $cliente->id,
                'user_id' => $cliente->user_id,
                'mercado_pago_id' => $mercadoPagoId,
                'valor' => $plano->preco,
                'status' => 'pending',
                'plano_id' => $cliente->plano_id,
                'isAnual' => false,
            ]);
    
            $dadosCliente = [
                '{nome_cliente}' => $cliente->nome ?? 'Nome do Cliente',
                '{telefone_cliente}' => $cliente->whatsapp ?? '(11) 99999-9999',
                '{notas}' => $cliente->notas ?? 'Notas',
                '{vencimento_cliente}' => Carbon::parse($cliente->vencimento)->format('d/m/Y') ?? 'Vencimento do Cliente',
                '{plano_nome}' => $nomePlano,
                '{plano_valor}' => $valorPlano,
                '{data_atual}' => Carbon::now()->format('d/m/Y'),
                '{plano_link}' => '',
                '{text_expirate}' => $textExpirate,
                '{saudacao}' => $saudacao,
                '{whatsapp_empresa}' => $whatsappEmpresa,
                '{status_pagamento}' => 'Pendente',
                '{nome_empresa}' => $nomeEmpresa,
            ];
    
            $conteudoCliente = $this->substituirPlaceholders($template->conteudo, $dadosCliente);
            $this->sendMessage($cliente->whatsapp, $conteudoCliente, $cliente->user_id);
    
            // Enviar mensagem separada com o código Pix
            $this->sendMessage($cliente->whatsapp, "Use a seguinte chave Pix para pagamento:\n" . $company->pix_manual, $cliente->user_id);
    
            $this->atualizarStatus($setting, 'enviado');
    
        } else {
            $pagamentoData = $this->processarPagamento($cliente, $plano);
    
            $dadosCliente = [
                '{nome_cliente}' => $cliente->nome ?? 'Nome do Cliente',
                '{telefone_cliente}' => $cliente->whatsapp ?? '(11) 99999-9999',
                '{notas}' => $cliente->notas ?? 'Notas',
                '{vencimento_cliente}' => Carbon::parse($cliente->vencimento)->format('d/m/Y') ?? 'Vencimento do Cliente',
                '{plano_nome}' => $nomePlano,
                '{plano_valor}' => $valorPlano,
                '{data_atual}' => Carbon::now()->format('d/m/Y'),
                '{plano_link}' => $pagamentoData['payment_link'] ?? 'Link de Pagamento',
                '{text_expirate}' => $textExpirate,
                '{saudacao}' => $saudacao,
                '{whatsapp_empresa}' => $whatsappEmpresa,
                '{status_pagamento}' => 'Pendente',
                '{nome_empresa}' => $nomeEmpresa,
            ];
    
            $conteudoCliente = $this->substituirPlaceholders($template->conteudo, $dadosCliente);
            $this->sendMessage($cliente->whatsapp, $conteudoCliente, $cliente->user_id);
    
            // Enviar mensagem separada com o código Pix
            if (isset($pagamentoData['payload_pix'])) {
                $this->sendMessage($cliente->whatsapp, "Copie e cole o seguinte código:\n" . $pagamentoData['payload_pix'], $cliente->user_id);
            }
    
            $this->atualizarStatus($setting, 'enviado');
        }
    }
    
    private function processarPagamento($cliente, $plano)
    {
        $companyDetail = CompanyDetail::where('user_id', $cliente->user_id)->first();
    
        if (!$companyDetail) {
            Log::error('Detalhes da empresa não encontrados para user_id: ' . $cliente->user_id);
            throw new \Exception('Detalhes da empresa não encontrados.');
        }
    
        // Nova lógica para obter o notification_url de um administrador
        $adminUser = User::where('role_id', 1)->first();
    
        if (!$adminUser) {
            Log::error('Administrador não encontrado.');
            throw new \Exception('Administrador não encontrado.');
        }
    
        $adminCompanyDetail = CompanyDetail::where('user_id', $adminUser->id)->first();
    
        if (!$adminCompanyDetail) {
            Log::error('Detalhes da empresa não encontrados para o administrador com user_id: ' . $adminUser->id);
            throw new \Exception('Detalhes da empresa não encontrados para o administrador.');
        }
    
        $accessToken = $companyDetail->access_token;
        $url_notification = $adminCompanyDetail->notification_url;
    
        if (!$accessToken) {
            Log::error('Access Token não encontrado. Verifique se a variável MERCADO_PAGO_ACCESS_TOKEN está definida no arquivo .env.');
            throw new \Exception('Access Token não encontrado.');
        }
    
        MercadoPagoConfig::setAccessToken($accessToken);
        MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);
    
        $paymentClient = new PaymentClient();
    
        $valorPlano = (float) $plano->preco;
        if ($valorPlano <= 0) {
            Log::error('Valor do plano deve ser positivo. Valor encontrado: ' . $valorPlano);
            return ['payment_link' => null, 'payload_pix' => null];
        }
    
        $preference = [
            'transaction_amount' => $valorPlano,
            'description' => $plano->nome,
            'payment_method_id' => 'pix',
            'notification_url' => $url_notification,
            'payer' => [
                'email' => 'cliente@cliente.com',
                'first_name' => $cliente->nome,
                'identification' => [
                    'type' => 'CPF',
                    'number' => '12345678909' // Substitua pelo CPF real do cliente
                ]
            ]
        ];
    
        $requestOptions = new RequestOptions();
        $requestOptions->setCustomHeaders(["X-Idempotency-Key: " . uniqid()]);
    
        try {
            $response = $paymentClient->create($preference, $requestOptions);
            $paymentLink = $response->point_of_interaction->transaction_data->ticket_url;
            $payloadPix = $response->point_of_interaction->transaction_data->qr_code;
    
            Pagamento::create([
                'cliente_id' => $cliente->id,
                'user_id' => $cliente->user_id,
                'mercado_pago_id' => $response->id,
                'valor' => $valorPlano,
                'status' => 'pending',
                'plano_id' => $cliente->plano_id,
                'isAnual' => false,
            ]);
    
            return [
                'payment_link' => $paymentLink,
                'payload_pix' => $payloadPix,
            ];
        } catch (MPApiException $e) {
            Log::error('Erro ao criar preferência de pagamento: ' . $e->getApiResponse()->getStatusCode());
            Log::error('Conteúdo: ' . json_encode($e->getApiResponse()->getContent()));
            return ['payment_link' => null, 'payload_pix' => null];
        } catch (\Exception $e) {
            Log::error('Erro: ' . $e->getMessage());
            return ['payment_link' => null, 'payload_pix' => null];
        }
    }
     
    private function substituirPlaceholders($conteudo, $dados)
    {
        $placeholders = [
            '{nome_cliente}' => $dados['{nome_cliente}'] ?? 'Nome do Cliente',
            '{telefone_cliente}' => $dados['{telefone_cliente}'] ?? '(11) 99999-9999',
            '{notas}' => $dados['{notas}'] ?? 'Notas do cliente',
            '{vencimento_cliente}' => $dados['{vencimento_cliente}'] ?? '01/01/2023',
            '{plano_nome}' => $dados['{plano_nome}'] ?? 'Plano Básico',
            '{plano_valor}' => $dados['{plano_valor}'] ?? 'R$ 99,90',
            '{data_atual}' => $dados['{data_atual}'] ?? date('d/m/Y'),
            '{plano_link}' => $dados['{plano_link}'] ?? 'http://linkdopagamento.com',
            '{text_expirate}' => $dados['{text_expirate}'] ?? '',
            '{saudacao}' => $dados['{saudacao}'] ?? $this->getSaudacao(),
            '{status_pagamento}' => $dados['{status_pagamento}'] ?? 'Status do Pagamento',
            '{nome_empresa}' => $dados['{nome_empresa}'] ?? 'Nome da Empresa',
            '{whatsapp_empresa}' => $dados['{whatsapp_empresa}'] ?? '(11) 99999-9999',
            '{nome_dono}' => $dados['{nome_dono}'] ?? 'Nome do Dono',
            '{whatsapp_dono}' => $dados['{whatsapp_dono}'] ?? '(11) 99999-9999',
        ];
    
        foreach ($placeholders as $placeholder => $valor) {
            $conteudo = str_replace($placeholder, $valor, $conteudo);
        }
    
        return $conteudo;
    }

    private function getSaudacao()
    {
        $hora = Carbon::now()->format('H');
        if ($hora >= 6 && $hora < 12) {
            return 'Bom dia';
        } elseif ($hora >= 12 && $hora < 18) {
            return 'Boa tarde';
        } else {
            return 'Boa noite';
        }
    }

    private function getTextExpirate($vencimento)
    {
        $dataVencimento = Carbon::parse($vencimento);
        $diasRestantes = $dataVencimento->diffInDays(Carbon::now());

        if ($diasRestantes <= 0) {
            return 'Seu pagamento está vencido!';
        } elseif ($diasRestantes <= 3) {
            return 'Seu pagamento está próximo do vencimento!';
        } else {
            return 'Seu pagamento está em dia.';
        }
    }

    private function sendMessage($phone, $message, $user_id)
    {
        Log::info('Enviando mensagem para ' . $phone . ': ' . $message);
        $sendMessageController = new SendMessageController();
        $request = new Request([
            'phone' => $phone,
            'message' => $message,
            'user_id' => $user_id,
        ]);
        $sendMessageController->sendMessageWithoutAuth($request);
    }

    private function atualizarStatus($setting, $status)
    {
        $setting->status = $status;
        $setting->save();
    }
}
