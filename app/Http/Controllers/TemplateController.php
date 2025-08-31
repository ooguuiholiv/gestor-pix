<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Template;
use App\Models\Cliente;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ConexaoController;
use App\Models\PlanoRenovacao;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;

class TemplateController extends Controller
{

  public function index(Request $request, $user_id = null)
  {
      if (Auth::check()) {
          // Usuário está autenticado
          $user = Auth::user();
          $userId = $user->id;
          $userRole = $user->role->name;

          // Verificar se o usuário é administrador
          if ($userRole === 'admin') {
              $filter = $request->input('filter', 'all');

              if ($filter == 'mine') {
                  // Mostrar apenas os templates do administrador
                  $templates = DB::table('templates')
                      ->join('users', 'templates.user_id', '=', 'users.id')
                      ->select('templates.*', 'users.name as user_name')
                      ->where('templates.user_id', $userId)
                      ->get();
              } elseif ($filter == 'others') {
                  // Mostrar apenas os templates de outros usuários
                  $templates = DB::table('templates')
                      ->join('users', 'templates.user_id', '=', 'users.id')
                      ->select('templates.*', 'users.name as user_name')
                      ->where('templates.user_id', '!=', $userId)
                      ->get();
              } else {
                  // Mostrar todos os templates
                  $templates = DB::table('templates')
                      ->join('users', 'templates.user_id', '=', 'users.id')
                      ->select('templates.*', 'users.name as user_name')
                      ->get();
              }
          } else {
              // Se não for administrador, mostrar apenas os templates do usuário autenticado
              $templates = DB::table('templates')
                  ->join('users', 'templates.user_id', '=', 'users.id')
                  ->select('templates.*', 'users.name as user_name')
                  ->where('templates.user_id', $userId)
                  ->get();
          }

          $current_plan_id = $user->plano_id;
          $planos_revenda = PlanoRenovacao::all();

          return view('templates.index', compact('templates', 'current_plan_id', 'planos_revenda', 'user'));
      }
    }



public function list(Request $request)
{
    Log::info('Acessando a listagem de templates com paginação e busca.');

    try {
        if (Auth::check()) {
            $user = Auth::user();
            $userRole = $user->role->name;

            $search = $request->input('search');
            $sort = $request->input('sort', 'id');
            $order = $request->input('order', 'DESC');

            // Todos os usuários, incluindo administradores, veem apenas seus próprios dados
            $templates = Template::where('user_id', $user->id);

            if ($search) {
                $templates = $templates->where(function ($query) use ($search) {
                    $query->where('nome', 'like', '%' . $search . '%')
                          ->orWhere('finalidade', 'like', '%' . $search . '%');
                });
            }

            $totalTemplates = $templates->count();
            $canEdit = true; // Defina a lógica para verificar se o usuário pode editar
            $canDelete = true; // Defina a lógica para verificar se o usuário pode deletar

            $templates = $templates->orderBy($sort, $order)
                                   ->paginate($request->input('limit', 10))
                                   ->through(function ($template) use ($canEdit, $canDelete) {
                                       $actions = '<div class="d-grid gap-3">
                                                       <div class="row g-3">
                                                           <div class="col-6 mb-2">
                                                               <button class="btn btn-sm btn-primary w-100" data-bs-toggle="modal" data-bs-target="#editTemplate' . $template->id . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Editar">
                                                                   <i class="fas fa-edit"></i>
                                                               </button>
                                                           </div>
                                                           <div class="col-6 mb-2">
                                                               <form action="' . route('templates.destroy', $template->id) . '" method="POST" style="display:inline;">
                                                                   ' . csrf_field() . '
                                                                   ' . method_field('DELETE') . '
                                                                   <button type="submit" class="btn btn-sm btn-danger w-100" data-bs-toggle="tooltip" data-bs-placement="top" title="Deletar">
                                                                       <i class="fas fa-trash-alt"></i>
                                                                   </button>
                                                               </form>
                                                           </div>
                                                       </div>
                                                   </div>';

                                       return [
                                           'id' => $template->id,
                                           'nome' => $template->nome,
                                           'finalidade' => $template->finalidade,
                                           'conteudo' => $template->conteudo,
                                           'user_name' => $template->user ? $template->user->name : 'N/A',
                                           'actions' => $actions
                                       ];
                                   });

            // Fetch user preferences for visible columns
            $userId = $user->id;
            $preferences = DB::table('user_client_preferences')
                ->where('user_id', $userId)
                ->where('table_name', 'templates')
                ->value('visible_columns');

            $visibleColumns = json_decode($preferences, true) ?: [
                'id',
                'nome',
                'finalidade',
                'conteudo',
                'user_name',
                'actions'
            ];

            // Filter the columns based on user preferences
            $filteredTemplates = $templates->map(function ($template) use ($visibleColumns) {
                return array_filter($template, function ($key) use ($visibleColumns) {
                    return in_array($key, $visibleColumns);
                }, ARRAY_FILTER_USE_KEY);
            });

            return response()->json([
                'rows' => $filteredTemplates,
                'total' => $totalTemplates,
            ]);
        } else {
            return response()->json(['error' => 'Usuário não autenticado'], 401);
        }
    } catch (\Exception $e) {
        Log::error('Erro ao acessar a listagem de templates: ' . $e->getMessage());
        return response()->json(['error' => 'Erro ao acessar a listagem de templates'], 500);
    }
}

public function show($id)
{
    // Método vazio para evitar erro
}


    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string',
            'finalidade' => 'required|string',
            'conteudo' => 'required|string',
        ]);

        Template::create([
            'user_id' => Auth::id(),
            'nome' => $request->nome,
            'finalidade' => $request->finalidade,
            'conteudo' => $request->conteudo,
        ]);

        return redirect()->route('templates.index')->with('success', 'Template criado com sucesso.');
    }

 
    public function update(Request $request, $id)
    {
        $request->validate([
            'nome' => 'required|string',
            'finalidade' => 'required|string',
            'conteudo' => 'required|string',
        ]);

        $template = Template::findOrFail($id);
        $template->update($request->all());

        return redirect()->route('templates.index')->with('success', 'Template atualizado com sucesso.');
    }

    public function destroy($id)
    {
        $template = Template::findOrFail($id);
        $template->delete();

        return redirect()->route('templates.index')->with('success', 'Template deletado com sucesso.');
    }

     public function destroy_multiple(Request $request)
    {
        $ids = $request->input('ids');
        if (is_array($ids)) {
            Template::whereIn('id', $ids)->delete();
            return response()->json(['error' => false, 'message' => 'Templates excluídos com sucesso.']);
        } else {
            return response()->json(['error' => true, 'message' => 'Nenhum template selecionado.']);
        }
    }



    public function enviarMensagem($templateId, $clienteId, $tipoMensagem)
    {
        $template = Template::findOrFail($templateId);
        $cliente = Cliente::findOrFail($clienteId);

        $dadosCliente = [
            'nome' => $cliente->nome,
            'primeiro_nome' => $cliente->primeiro_nome,
            'telefone' => $cliente->whatsapp,
            'notas' => $cliente->notas,
            'vencimento' => $cliente->vencimento,
            'plano_nome' => $cliente->plano->nome,
            'plano_valor' => $cliente->plano->valor,
            'plano_link' => $cliente->plano_link,
            'text_expirate' => $this->getTextExpirate($cliente->vencimento),
        ];

        $conteudo = $this->substituirPlaceholders($template->conteudo, $dadosCliente);

        $conexaoController = new ConexaoController();

        if ($tipoMensagem === 'media') {
            $response = $conexaoController->sendMediaMessage(new Request([
                'phone' => $cliente->whatsapp,
                'qrcode_image' => $conteudo, // Supondo que o conteúdo seja a imagem do QRCode
            ]));
        } else {
            $response = $conexaoController->sendMessage(new Request([
                'phone' => $cliente->whatsapp,
                'message' => $conteudo,
            ]));
        }

        return $response;
    }

    public function cobrancaManual($clienteId)
    {
        $cliente = Cliente::findOrFail($clienteId);

        // Supondo que você tenha um template específico para cobrança manual
        $template = Template::where('finalidade', 'cobranca_manual')->firstOrFail();

        $dadosCliente = [
            'nome' => $cliente->nome,
            'telefone' => $cliente->whatsapp,
            'notas' => $cliente->notas,
            'vencimento' => $cliente->vencimento,
            'plano_nome' => $cliente->plano->nome,
            'plano_valor' => $cliente->plano->valor,
            'plano_link' => $cliente->plano_link,
            'text_expirate' => $this->getTextExpirate($cliente->vencimento),
        ];

        $conteudo = $this->substituirPlaceholders($template->conteudo, $dadosCliente);

        $conexaoController = new ConexaoController();
        $response = $conexaoController->sendMessage(new Request([
            'phone' => $cliente->whatsapp,
            'message' => $conteudo,
        ]));

        return redirect()->route('app-ecommerce-customer-all')->with('success', 'Cobrança manual enviada com sucesso.');
    }

    private function substituirPlaceholders($conteudo, $dadosCliente)
    {
        $placeholders = [
            '{nome_cliente}' => $dadosCliente['nome'] ?? 'Nome do Cliente',
            '{telefone_cliente}' => $dadosCliente['telefone'] ?? '(11) 99999-9999',
            '{notas}' => $dadosCliente['notas'] ?? 'Notas do cliente',
            '{vencimento_cliente}' => $dadosCliente['vencimento'] ?? '01/01/2023',
            '{plano_nome}' => $dadosCliente['plano_nome'] ?? 'Plano Básico',
            '{plano_valor}' => $dadosCliente['plano_valor'] ?? 'R$ 99,90',
            '{data_atual}' => date('d/m/Y'),
            '{plano_link}' => $dadosCliente['plano_link'] ?? 'http://linkdopagamento.com',
            '{text_expirate}' => $this->getTextExpirate($dadosCliente['vencimento']),
            '{saudacao}' => $this->getSaudacao(),
            '{payload_pix}' => 'Pix copia e cola Mercado Pago',
        ];

        foreach ($placeholders as $placeholder => $valor) {
            $conteudo = str_replace($placeholder, $valor, $conteudo);
        }

        return $conteudo;
    }

    private function getTextExpirate($vencimento)
    {
        $dataVencimento = new \DateTime($vencimento);
        $dataAtual = new \DateTime();
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
}
