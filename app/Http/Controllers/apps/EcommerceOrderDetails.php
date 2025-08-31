<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Pagamento;
use App\Models\Cliente;
use Illuminate\Http\Request;
use App\Models\PlanoRenovacao;
use Illuminate\Support\Facades\Auth;
use App\Models\CompanyDetail;
use App\Http\Controllers\SendMessageController;
use App\Models\Plano;
use Illuminate\Support\Facades\Log;
use App\Models\Template;
use App\Models\User;
use Carbon\Carbon;

class EcommerceOrderDetails extends Controller
{
  public function __construct()
  {
    // Aplicar middleware de autenticação
    $this->middleware('auth');
  }

  public function index(Request $request)
  {
      $user = Auth::user();
  
      // Buscar a cobrança específica
      $paymentId = $request->query('order_id');
  
      $payment = Pagamento::find($paymentId);
  
      // Verificar se o pagamento foi encontrado
      if (!$payment) {
          return redirect()->back()->with('error', 'Pagamento não encontrado.');
      }
  
    
      $cliente = Cliente::find($payment->cliente_id);
      if (!$cliente) {
          return redirect()->back()->with('error', 'Nenhum Pagamento encontrado para este Cliente.');
      }
  
      $empresa = CompanyDetail::where('user_id', $payment->user_id)->first();
      if (!$empresa) {
          return redirect()->back()->with('error', 'Empresa não encontrada.');
      }
  
      $plano = Plano::find($cliente->plano_id);
      if (!$plano) {
          return redirect()->back()->with('error', 'Plano não encontrado.');
      }
    
      $planos_revenda = PlanoRenovacao::all();
   
  
      $current_plan_id = $user->plano_id;
      return view('content.apps.detalhes', compact('payment', 'cliente', 'planos_revenda', 'current_plan_id', 'empresa', 'plano'));
  }

  public function addPayment(Request $request)
  {
      $request->validate([
          'payment_id' => 'required|exists:pagamentos,id',
          'invoiceAmount' => 'required|numeric',
          'payment_date' => 'required|date',
          'payment_status' => 'required|string|in:pending,approved',
      ]);

      $payment = Pagamento::findOrFail($request->payment_id);
      $payment->valor = $request->invoiceAmount;
      $payment->status = $request->payment_status;
      $payment->payment_date = $request->payment_date;
      $payment->updated_at = now();
      $payment->save();

      if ($payment->status === 'approved') {
          // Atualizar a data de vencimento do cliente com base na duração do plano
          $cliente = Cliente::find($payment->cliente_id);
          if ($cliente) {
              $plano = Plano::find($cliente->plano_id);
              if ($plano) {
                  $cliente->vencimento = Carbon::parse($cliente->vencimento)->addDays($plano->duracao);
                  $cliente->save();
              }
          }

          // Notificar o cliente e o dono do cliente
          $this->notifyClientAndOwner($payment);
      }

      return redirect()->back()->with('success', 'Pagamento atualizado com sucesso.');
  }

  private function notifyClientAndOwner($paymentRecord)
  {

      // Implementar a lógica para notificar o cliente e o dono do cliente
      $cliente = Cliente::find($paymentRecord->cliente_id);
      if ($cliente) {

          // Buscar o template para notificações de pagamento
          $template = Template::where('finalidade', 'pagamentos')
          ->where('user_id', $cliente->user_id)
          ->first();

          if (!$template) {
            $template = Template::where('finalidade', 'pagamentos')
                                ->whereNull('user_id')
                                ->firstOrFail();
        }


          $statusPagamentoMap = [
              'paid' => 'Pago',
              'pending' => 'Pendente',
              'failed' => 'Falhou',
              'in_process' => 'Em Processo',
              'approved' => 'Aprovado',

          ];

          // Obter o status de pagamento mapeado
          $statusPagamento = $statusPagamentoMap[$paymentRecord->status] ?? $paymentRecord->status ?? 'Status do Pagamento';

          // Buscar os dados da empresa
          $company = CompanyDetail::where('user_id', $cliente->user_id)->first();
          $nomeEmpresa = $company ? $company->company_name : '{nome_empresa}';
          $whatsappEmpresa = $company ? $company->company_whatsapp : '{whatsapp_empresa}';

          // Buscar os dados do dono do cliente
          $owner = User::find($cliente->user_id);
          $nomeDono = $owner ? $owner->name : '{nome_dono}';
          $whatsappDono = $owner ? $owner->whatsapp : '{whatsapp_dono}';

          // Buscar o plano na tabela planos
          $plano = Plano::find($paymentRecord->plano_id);
          $nomePlano = $plano ? $plano->nome : 'Nome do Plano';
          $valorPlano = $plano ? $plano->preco : 'Valor do Plano';

          // Obter a saudação e o texto de expiração
          $saudacao = $this->getSaudacao();
          $textExpirate = $this->getTextExpirate($cliente->vencimento);

          // Dados para substituir os placeholders no template
          $dadosCliente = [
              '{nome_cliente}' => $cliente->nome ?? 'Nome do Cliente',
              '{telefone_cliente}' => $cliente->whatsapp ?? '(11) 99999-9999',
              '{notas}' => $cliente->notas ?? 'Notas',
              '{vencimento_cliente}' => Carbon::parse($cliente->vencimento)->format('d/m/Y') ?? 'Vencimento do Cliente',
              '{plano_nome}' => $nomePlano,
              '{plano_valor}' => $valorPlano,
              '{data_atual}' => Carbon::now()->format('d/m/Y'),
              '{plano_link}' => $paymentRecord->link_pagamento ?? 'Link de Pagamento',
              '{text_expirate}' => $textExpirate,
              '{saudacao}' => $saudacao,
              '{payload_pix}' => $paymentRecord->payload_pix ?? 'Pix Copia e Cola',
              '{whatsap_empresa}' => $whatsappEmpresa,
              '{status_pagamento}' => $statusPagamento,
              '{nome_empresa}' => $nomeEmpresa,
              '{nome_dono}' => $nomeDono,
          ];

          // Substituir placeholders no template para o cliente
          $conteudoCliente = $this->substituirPlaceholders($template->conteudo, $dadosCliente);
           $this->sendMessage($cliente->whatsapp, $conteudoCliente, $cliente->user_id);

          // Notificar o dono do cliente
          if ($owner) {
            // Substituir '{nome_cliente}' por '{nome_dono}' no conteúdo para o dono
            $dadosDono = $dadosCliente;
            $dadosDono['{nome_cliente}'] = $nomeDono;
            $dadosDono['{data_atual}'] = Carbon::now()->format('d/m/Y');
        
            // Mensagem predefinida com quebras de linha
            $mensagemPredefinida = "Olá {$nomeDono},\n";
            $mensagemPredefinida .= "O cliente {$dadosCliente['{nome_cliente}']} fez o pagamento do {$nomePlano}.\n";
            $mensagemPredefinida .= "No Valor:R$ {$valorPlano}.\n";
            $mensagemPredefinida .= "Data: {$dadosDono['{data_atual}']}.";
        
            $this->sendMessage($owner->whatsapp, $mensagemPredefinida, $owner->id);
          } else {
             
          }
      } else {
      }
  }

  private function getTextExpirate($vencimento)
  {
      // Converte a data de yyyy-mm-dd para um objeto Carbon
      $dataVencimento = Carbon::parse($vencimento);
      $dataAtual = Carbon::now();
      $intervalo = $dataAtual->diff($dataVencimento);

      if ($intervalo->invert) {
          return 'expirou há ' . $intervalo->days . ' dias';
      } elseif ($intervalo->days == 0) {
          return 'expira hoje';
      } else {
          return 'expira em ' . $intervalo->days . ' dias';
      }
  }

  private function getSaudacao()
  {
      $hora = date('H');
      if ($hora < 12) {
          return 'Bom dia!';
      } elseif ($hora < 18) {
          return 'Boa tarde!';
      } else {
          return 'Boa noite!';
      }
  }

  private function substituirPlaceholders($conteudo, $dados)
  {
      foreach ($dados as $placeholder => $valor) {
          $conteudo = str_replace($placeholder, $valor, $conteudo);
      }
      return $conteudo;
  }

  private function sendMessage($phone, $message, $user_id)
  {
     
      $sendMessageController = new SendMessageController();
      $request = new Request([
          'phone' => $phone,
          'message' => $message,
          'user_id' => $user_id,
      ]);
      $sendMessageController->sendMessageWithoutAuth($request);
  }
}