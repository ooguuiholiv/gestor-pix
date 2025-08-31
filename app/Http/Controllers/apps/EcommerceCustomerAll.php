<?php

namespace App\Http\Controllers\apps;

require_once '../vendor/autoload.php';

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\User;
use App\Models\Servidor;
use Illuminate\Support\Facades\Auth;
use App\Models\Plano;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\ConexaoController;
use App\Models\Template;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;
use App\Models\Pagamento;
use App\Models\CompanyDetail;
use App\Models\Conexao;
use App\Models\PlanoRenovacao;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
class EcommerceCustomerAll extends Controller
{

    private $apikey;
    private $urlapi;
    public function __construct()
    {
        // Aplicar middleware de autenticação
        $this->middleware('auth');
    }


    public function index()
    {
        Log::info('Acessando a página de listagem de clientes.');

        $user = Auth::user();
        $planos = Plano::all();
        $current_plan_id = $user->plano_id;

        // Adicione a linha abaixo para obter os clientes
        $clientes = Cliente::all();


        $planos_revenda = PlanoRenovacao::all();
        $current_plan_id = $user->plano_id;

        $loginUrl = route('client.login.form');

        // Acessar dados da sessão
        $sessionData = Session::all();


        return view('content.apps.app-ecommerce-customer-all', compact('planos', 'current_plan_id', 'clientes', 'planos_revenda', 'current_plan_id', 'sessionData', 'user', 'loginUrl'));
    }

      public function store(Request $request)
    {
        try {
            $request->validate([
                'nome' => 'required|string|unique:clientes,nome',
                'iptv_nome' => 'nullable|string|max:255', // Tornado opcional
                'iptv_senha' => 'nullable|string|max:255',
                'whatsapp' => 'required|string|unique:clientes,whatsapp',
                'password' => 'required|string',
                'vencimento' => 'required|date',
                'servidor_id' => 'required|exists:servidores,id',
                'mac' => 'nullable|string',
                'notificacoes' => 'required|boolean',
                'plano_id' => 'nullable|exists:planos,id',
                'numero_de_telas' => 'required|integer',
                'notas' => 'nullable|string',
            ]);
    
            $user = Auth::user();
    
            // Buscar o plano do usuário
            $planoUsuario = PlanoRenovacao::find($user->plano_id);
    
            // Verificar se o plano tem limite e se o limite foi atingido, exceto para role_id 1
            if ($user->role_id != 1 && $planoUsuario && $planoUsuario->limite > 0 && $user->limite <= 0) {
                return redirect()->route('app-ecommerce-customer-all')->with('error', 'Você atingiu o limite máximo de clientes permitidos pelo seu plano.');
            }
    
            Cliente::create([
                'user_id' => $user->id,
                'nome' => $request->nome,
                'iptv_nome' => $request->iptv_nome,
                'iptv_senha' => $request->iptv_senha,
                'whatsapp' => $request->whatsapp,
                'password' => $request->password, // Armazenando a senha sem criptografia
                'vencimento' => $request->vencimento,
                'servidor_id' => $request->servidor_id,
                'mac' => $request->mac,
                'notificacoes' => $request->notificacoes,
                'plano_id' => $request->plano_id,
                'numero_de_telas' => $request->numero_de_telas,
                'notas' => $request->notas,
                'role_id' => 3,
            ]);
    
            if ($planoUsuario && $planoUsuario->limite > 0) {
                $user->limite -= 1;
                $user->save();
            }
    
            return redirect()->route('app-ecommerce-customer-all')->with('success', 'Cliente cadastrado com sucesso.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('app-ecommerce-customer-all')->with('error', 'Nome ou WhatsApp já estão em uso.');
        }
    }



    public function update(Request $request, $id)
    {
        $cliente = Cliente::findOrFail($id);

        try {
            $request->validate([
                'nome' => 'required|string|unique:clientes,nome,' . $cliente->id,
                'iptv_nome' => 'nullable|string|max:255', // Tornado opcional
                'iptv_senha' => 'nullable|string|max:255',
                'whatsapp' => 'required|string|unique:clientes,whatsapp,' . $cliente->id,
                'password' => 'required|string',
                'vencimento' => 'required|date',
                'servidor_id' => 'required|exists:servidores,id',
                'mac' => 'nullable|string',
                'notificacoes' => 'required|boolean',
                'plano_id' => 'nullable|exists:planos,id',
                'numero_de_telas' => 'required|integer',
                'notas' => 'nullable|string',
            ]);

            $cliente->update([
                'nome' => $request->nome,
                'iptv_nome' => $request->iptv_nome,
                'iptv_senha' => $request->iptv_senha,
                'whatsapp' => $request->whatsapp,
                'password' => $request->password, // Armazenando a senha sem criptografia
                'vencimento' => $request->vencimento,
                'servidor_id' => $request->servidor_id,
                'mac' => $request->mac,
                'notificacoes' => $request->notificacoes,
                'plano_id' => $request->plano_id,
                'numero_de_telas' => $request->numero_de_telas,
                'notas' => $request->notas,
            ]);

            return redirect()->route('app-ecommerce-customer-all')->with('success', 'Cliente atualizado com sucesso.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('app-ecommerce-customer-all')->with('error', 'Nome ou WhatsApp já estão em uso.');
        }
    }








    
       
    // public function list(Request $request)
    // {
    //     Log::info('Acessando a listagem de clientes com paginação e busca.');
    
    //     try {
    //         if (Auth::check()) {
    //             $user = Auth::user();
    //             $userRole = $user->role->name;
    
    //             $search = $request->input('search');
    //             $sort = $request->input('sort', 'id');
    //             $order = $request->input('order', 'DESC');
    //             $vencimento = $request->input('vencimento');
    
    //             // Verifica se o usuário é um administrador
    //             if ($userRole === 'admin') {
    //                 $filter = $request->input('filter', 'all');
    
    //                 if ($filter == 'mine') {
    //                     // Mostrar apenas os clientes do administrador
    //                     $clientes = Cliente::where('user_id', $user->id)->with('plano', 'servidor');
    //                     $planos = Plano::where('user_id', $user->id)->get();
    //                     $servidores = Servidor::where('user_id', $user->id)->get();
    //                 } else {
    //                     // Mostrar todos os clientes
    //                     $clientes = Cliente::with('plano', 'servidor');
    //                     $planos = Plano::all();
    //                     $servidores = Servidor::all();
    //                 }
    //             } else {
    //                 // Retorna apenas os dados do usuário logado se não for administrador
    //                 $clientes = Cliente::where('user_id', $user->id)->with('plano', 'servidor');
    //                 $planos = Plano::where('user_id', $user->id)->get();
    //                 $servidores = Servidor::where('user_id', $user->id)->get();
    //             }
    
    //             $planos_revenda = PlanoRenovacao::all();
    //             $current_plan_id = $user->plano_id;
    
    //             if ($search) {
    //                 $clientes = $clientes->where('nome', 'like', '%' . $search . '%');
    //             }
    //             if ($vencimento) {
    //                 Log::info('Aplicando filtro de vencimento', ['vencimento' => $vencimento]);
    //                 switch ($vencimento) {
    //                     case 'vencido':
    //                         $clientes = $clientes->whereDate('vencimento', '<', Carbon::today());
    //                         Log::info('Filtro aplicado: Vencido', ['query' => $clientes->toSql(), 'bindings' => $clientes->getBindings()]);
    //                         break;
    //                     case 'ainda_vai_vencer':
    //                         $clientes = $clientes->whereDate('vencimento', '>', Carbon::today());
    //                         Log::info('Filtro aplicado: Ainda vai vencer', ['query' => $clientes->toSql(), 'bindings' => $clientes->getBindings()]);
    //                         break;
    //                     case 'hoje':
    //                         $clientes = $clientes->whereDate('vencimento', Carbon::today());
    //                         Log::info('Filtro aplicado: Vence hoje', ['query' => $clientes->toSql(), 'bindings' => $clientes->getBindings()]);
    //                         break;
    //                     case 'todos':
    //                     default:
    //                         // Não aplica nenhum filtro de data
    //                         Log::info('Filtro aplicado: Todos', ['query' => $clientes->toSql(), 'bindings' => $clientes->getBindings()]);
    //                         break;
    //                 }
    //             }
    //             $totalClientes = $clientes->count();
    //             $canEdit = true; // Defina a lógica para verificar se o usuário pode editar
    //             $canDelete = true; // Defina a lógica para verificar se o usuário pode deletar
    
    //             $clientes = $clientes->orderBy($sort, $order)
    //                 ->paginate($request->input('limit', 10))
    //                 ->through(function ($cliente) use ($canEdit, $canDelete, $planos, $servidores) {
    //                     $actions = '<div class="d-grid gap-3">
    //                                     <div class="row g-3">
    //                                         <div class="col-6 mb-2">
    //                                             <form action="' . route('app-ecommerce-customer-destroy', $cliente->id) . '" method="POST" style="display:inline;">
    //                                                 ' . csrf_field() . '
    //                                                 ' . method_field('DELETE') . '
    //                                                 <button type="submit" class="btn btn-sm btn-danger w-100" data-bs-toggle="tooltip" data-bs-placement="top" title="Deletar">
    //                                                     <i class="fas fa-trash-alt"></i>
    //                                                 </button>
    //                                             </form>
    //                                         </div>
    //                                         <div class="col-6 mb-2">
    //                                             <button class="btn btn-sm btn-primary w-100" data-bs-toggle="modal" data-bs-target="#editClient' . $cliente->id . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Editar">
    //                                                 <i class="fas fa-edit"></i>
    //                                             </button>
    //                                         </div>
    //                                         <div class="col-6 mt-2">
    //                                             <form action="' . route('app-ecommerce-customer-charge', $cliente->id) . '" method="POST" style="display:inline;">
    //                                                 ' . csrf_field() . '
    //                                                 ' . method_field('POST') . '
    //                                                 <button type="submit" class="btn btn-sm btn-warning w-100" data-bs-toggle="tooltip" data-bs-placement="top" title="Cobrança Manual">
    //                                                     <i class="fas fa-dollar-sign"></i>
    //                                                 </button>
    //                                             </form>
    //                                         </div>
    //                                         <div class="col-6 mt-2">
    //                                             <a href="' . route('app-ecommerce-order-list', ['order_id' => $cliente->id]) . '" class="btn btn-sm btn-success w-100" data-bs-toggle="tooltip" data-bs-placement="top" title="Detalhes da Cobrança">
    //                                                 <i class="fas fa-thumbs-up"></i>
    //                                             </a>
    //                                         </div>
    //                                         <div class="col-6 mt-2">
    //                                             <button class="btn btn-sm btn-info w-100" data-bs-toggle="tooltip" data-bs-placement="top" title="Enviar Dados de Login" onclick="sendLoginDetails(\'' . $cliente->id . '\')">
    //                                                 <i class="fab fa-whatsapp"></i>
    //                                             </button>
    //                                         </div>
    //                                     </div>
    //                                 </div>';
    
    //                     $modal = '<div class="modal fade" id="editClient' . $cliente->id . '" tabindex="-1" aria-hidden="true">
    //                                     <div class="modal-dialog modal-md modal-simple modal-edit-client">
    //                                         <div class="modal-content p-3 p-md-5">
    //                                             <div class="modal-body">
    //                                                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    //                                                 <div class="text-center mb-4">
    //                                                     <h3 class="mb-2">Editar Cliente</h3>
    //                                                     <p class="text-muted">Atualize os detalhes do cliente.</p>
    //                                                 </div>
    //                                                 <form id="editClientForm' . $cliente->id . '" class="row g-3" action="' . route('app-ecommerce-customer-update', $cliente->id) . '" method="POST">
    //                                                     ' . csrf_field() . '
    //                                                     ' . method_field('PUT') . '
    //                                                     <div class="col-12">
    //                                                         <label class="form-label" for="editClientNome' . $cliente->id . '">Nome</label>
    //                                                         <input type="text" id="editClientNome' . $cliente->id . '" name="nome" class="form-control" value="' . $cliente->nome . '" required />
    //                                                     </div>
    //                                                     <div class="col-12">
    //                                                         <label class="form-label" for="editClientPassword' . $cliente->id . '">Senha</label>
    //                                                         <div class="input-group">
    //                                                             <input type="password" id="editClientPassword' . $cliente->id . '" name="password" class="form-control" value="' . $cliente->password . '" required />
    //                                                             <button type="button" class="btn btn-outline-secondary" onclick="generatePassword(\'editClientPassword' . $cliente->id . '\')">
    //                                                                 <i class="fas fa-random"></i>
    //                                                             </button>
    //                                                             <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVisibility(\'editClientPassword' . $cliente->id . '\')">
    //                                                                 <i class="fas fa-eye" id="togglePasswordIcon' . $cliente->id . '"></i>
    //                                                             </button>
    //                                                         </div>
    //                                                     </div>
    //                                                     <div class="col-12">
    //                                                         <label class="form-label" for="editClientIPTVNome' . $cliente->id . '">Usuário IPTV</label>
    //                                                         <input type="text" id="editClientIPTVNome' . $cliente->id . '" name="iptv_nome" class="form-control" value="' . $cliente->iptv_nome . '" />
    //                                                     </div>
    //                                                     <div class="col-12">
    //                                                         <label class="form-label" for="editClientIPTVSenha' . $cliente->id . '">Senha IPTV</label>
    //                                                         <div class="input-group">
    //                                                             <input type="text" id="editClientIPTVSenha' . $cliente->id . '" name="iptv_senha" class="form-control" value="' . $cliente->iptv_senha . '" />
    //                                                             <button type="button" class="btn btn-outline-secondary" onclick="generatePassword(\'editClientIPTVSenha' . $cliente->id . '\')">
    //                                                                 <i class="fas fa-random"></i>
    //                                                             </button>
    //                                                         </div>
    //                                                     </div>
    //                                                     <div class="col-12">
    //                                                         <label class="form-label" for="editClientWhatsApp' . $cliente->id . '">WhatsApp</label>
    //                                                         <input type="text" id="editClientWhatsApp' . $cliente->id . '" name="whatsapp" class="form-control" value="' . $cliente->whatsapp . '" required />
    //                                                     </div>
    //                                                     <div class="col-12">
    //                                                         <label class="form-label" for="editClientVencimento' . $cliente->id . '">Vencimento</label>
    //                                                         <input type="date" id="editClientVencimento' . $cliente->id . '" name="vencimento" class="form-control" value="' . $cliente->vencimento . '" required />
    //                                                     </div>
    //                                                     <div class="col-12">
    //                                                         <label class="form-label" for="editClientServidor' . $cliente->id . '">Servidor</label>
    //                                                         <select id="editClientServidor' . $cliente->id . '" name="servidor_id" class="form-select" required>';
    //                     foreach ($servidores as $servidor) {
    //                         $modal .= '<option value="' . $servidor->id . '" ' . ($cliente->servidor_id == $servidor->id ? 'selected' : '') . '>' . $servidor->nome . '</option>';
    //                     }
    //                     $modal .= '</select>
    //                                                     </div>
    //                                                     <div class="col-12">
    //                                                         <label class="form-label" for="editClientMac' . $cliente->id . '">MAC</label>
    //                                                         <input type="text" id="editClientMac' . $cliente->id . '" name="mac" class="form-control" value="' . $cliente->mac . '" />
    //                                                     </div>
    //                                                     <div class="col-12">
    //                                                         <label class="form-label" for="editClientNotificacoes' . $cliente->id . '">Notificações</label>
    //                                                         <select id="editClientNotificacoes' . $cliente->id . '" name="notificacoes" class="form-select" required>
    //                                                             <option value="1" ' . ($cliente->notificacoes ? 'selected' : '') . '>Sim</option>
    //                                                             <option value="0" ' . (!$cliente->notificacoes ? 'selected' : '') . '>Não</option>
    //                                                         </select>
    //                                                     </div>
    //                                                     <div class="col-12">
    //                                                         <label class="form-label" for="editClientPlano' . $cliente->id . '">Plano</label>
    //                                                         <select id="editClientPlano' . $cliente->id . '" name="plano_id" class="form-select" required>';
    //                     foreach ($planos as $plano) {
    //                         $modal .= '<option value="' . $plano->id . '" ' . ($cliente->plano_id == $plano->id ? 'selected' : '') . '>' . $plano->nome . '</option>';
    //                     }
    //                     $modal .= '</select>
    //                                                     </div>
    //                                                     <div class="col-12">
    //                                                         <label class="form-label" for="editClientNumeroDeTelas' . $cliente->id . '">Número de Telas</label>
    //                                                         <input type="number" id="editClientNumeroDeTelas' . $cliente->id . '" name="numero_de_telas" class="form-control" value="' . $cliente->numero_de_telas . '" required />
    //                                                     </div>
    //                                                     <div class="col-12">
    //                                                         <label class="form-label" for="editClientNotas' . $cliente->id . '">Notas</label>
    //                                                         <textarea id="editClientNotas' . $cliente->id . '" name="notas" class="form-control">' . $cliente->notas . '</textarea>
    //                                                     </div>
    //                                                     <div class="col-12 text-center">
    //                                                         <button type="submit" class="btn btn-primary me-sm-3 me-1">Salvar</button>
    //                                                         <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancelar</button>
    //                                                     </form>
    //                                                 </div>
    //                                             </div>
    //                                         </div>
    //                                     </div>';
    
    //                     // Calcular a diferença entre a data de vencimento e a data atual
    //                     $vencimento = Carbon::parse($cliente->vencimento);
    //                     $hoje = Carbon::today();
    //                     $diasDiferenca = $hoje->diffInDays($vencimento, false);
    
    //                     if ($diasDiferenca == 0) {
    //                         $vencimentoTexto = '<span class="badge bg-warning">Vence hoje</span>';
    //                     } elseif ($diasDiferenca == 1) {
    //                         $vencimentoTexto = '<span class="badge bg-info">Vence amanhã</span>';
    //                     } elseif ($diasDiferenca == -1) {
    //                         $vencimentoTexto = '<span class="badge bg-danger">Venceu ontem</span>';
    //                     } elseif ($diasDiferenca > 1) {
    //                         $vencimentoTexto = '<span class="badge bg-success">Vence em ' . $diasDiferenca . ' dias</span>';
    //                     } elseif ($diasDiferenca < -1) {
    //                         $vencimentoTexto = '<span class="badge bg-danger">Venceu há ' . abs($diasDiferenca) . ' dias</span>';
    //                     } else {
    //                         $vencimentoTexto = '<span class="badge bg-danger">Vencido</span>';
    //                     }
    
    //                     return [
    //                         'id' => $cliente->id,
    //                         'nome' => $cliente->nome,
    //                         'iptv_nome' => $cliente->iptv_nome,
    //                         'whatsapp' => $cliente->whatsapp,
    //                         'vencimento' => $vencimentoTexto,
    //                         'servidor' => $cliente->servidor ? $cliente->servidor->nome : 'N/A',
    //                         'mac' => $cliente->mac,
    //                         'notificacoes' => $cliente->notificacoes ? 'Sim' : 'Não',
    //                         'plano' => $cliente->plano ? $cliente->plano->nome : 'N/A',
    //                         'valor' => $cliente->plano ? 'R$ ' . number_format($cliente->plano->preco, 2, ',', '.') : 'N/A',
    //                         'numero_de_telas' => $cliente->numero_de_telas,
    //                         'notas' => $cliente->notas,
    //                         'actions' => $actions . $modal
    //                     ];
    //                 });
    
    //             // Fetch user preferences for visible columns
    //             $userId = getAuthenticatedUser(true);
    //             $preferences = DB::table('user_client_preferences')
    //                 ->where('user_id', $userId)
    //                 ->where('table_name', 'clientes')
    //                 ->value('visible_columns');
    
    //             $visibleColumns = json_decode($preferences, true) ?: [
    //                 'id',
    //                 'nome',
    //                 'iptv_nome',
    //                 'whatsapp',
    //                 'vencimento',
    //                 'servidor',
    //                 'mac',
    //                 'notificacoes',
    //                 'plano',
    //                 'valor',
    //                 'numero_de_telas',
    //                 'notas',
    //                 'actions'
    //             ];
    
    //             // Filter the columns based on user preferences
    //             $filteredClientes = $clientes->map(function ($cliente) use ($visibleColumns) {
    //                 return array_filter($cliente, function ($key) use ($visibleColumns) {
    //                     return in_array($key, $visibleColumns);
    //                 }, ARRAY_FILTER_USE_KEY);
    //             });
    
    //             return response()->json([
    //                 'rows' => $filteredClientes,
    //                 'total' => $totalClientes,
    //                 'planos' => $planos,
    //                 'servidores' => $servidores,
    //                 'planos_revenda' => $planos_revenda,
    //                 'current_plan_id' => $current_plan_id,
    //                 'sessionData' => Session::all(),
    //                 'user' => $user,
    //                 'loginUrl' => route('client.login.form')
    //             ]);
    //         } else {
    //             // Usuário não está autenticado
    //             return response()->json(['error' => 'Usuário não autenticado'], 401);
    //         }
    //     } catch (\Exception $e) {
    //         Log::error('Erro ao acessar a listagem de clientes: ' . $e->getMessage());
    //         return response()->json(['error' => 'Erro ao acessar a listagem de clientes'], 500);
    //     }
    // }

        public function list(Request $request)
    {
        Log::info('Acessando a listagem de clientes com paginação e busca.');
    
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $userRole = $user->role->name;
    
                $search = $request->input('search');
                $sort = $request->input('sort', 'id');
                $order = $request->input('order', 'DESC');
                $vencimento = $request->input('vencimento');
    
                // Mostrar apenas os clientes do usuário logado
                $clientes = Cliente::where('user_id', $user->id)->with('plano', 'servidor');
                $planos = Plano::where('user_id', $user->id)->get();
                $servidores = Servidor::where('user_id', $user->id)->get();
    
                $planos_revenda = PlanoRenovacao::all();
                $current_plan_id = $user->plano_id;
    
                if ($search) {
                    $clientes = $clientes->where('nome', 'like', '%' . $search . '%');
                }
                if ($vencimento) {
                    Log::info('Aplicando filtro de vencimento', ['vencimento' => $vencimento]);
                    switch ($vencimento) {
                        case 'vencido':
                            $clientes = $clientes->whereDate('vencimento', '<', Carbon::today());
                            Log::info('Filtro aplicado: Vencido', ['query' => $clientes->toSql(), 'bindings' => $clientes->getBindings()]);
                            break;
                        case 'ainda_vai_vencer':
                            $clientes = $clientes->whereDate('vencimento', '>', Carbon::today());
                            Log::info('Filtro aplicado: Ainda vai vencer', ['query' => $clientes->toSql(), 'bindings' => $clientes->getBindings()]);
                            break;
                        case 'hoje':
                            $clientes = $clientes->whereDate('vencimento', Carbon::today());
                            Log::info('Filtro aplicado: Vence hoje', ['query' => $clientes->toSql(), 'bindings' => $clientes->getBindings()]);
                            break;
                        case 'todos':
                        default:
                            // Não aplica nenhum filtro de data
                            Log::info('Filtro aplicado: Todos', ['query' => $clientes->toSql(), 'bindings' => $clientes->getBindings()]);
                            break;
                    }
                }
                $totalClientes = $clientes->count();
                $canEdit = true; // Defina a lógica para verificar se o usuário pode editar
                $canDelete = true; // Defina a lógica para verificar se o usuário pode deletar
    
                $clientes = $clientes->orderBy($sort, $order)
                    ->paginate($request->input('limit', 10))
                    ->through(function ($cliente) use ($canEdit, $canDelete, $planos, $servidores) {
                        $actions = '<div class="d-grid gap-3">
                                        <div class="row g-3">
                                            <div class="col-6 mb-2">
                                                <form action="' . route('app-ecommerce-customer-destroy', $cliente->id) . '" method="POST" style="display:inline;">
                                                    ' . csrf_field() . '
                                                    ' . method_field('DELETE') . '
                                                    <button type="submit" class="btn btn-sm btn-danger w-100" data-bs-toggle="tooltip" data-bs-placement="top" title="Deletar">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            <div class="col-6 mb-2">
                                                <button class="btn btn-sm btn-primary w-100" data-bs-toggle="modal" data-bs-target="#editClient' . $cliente->id . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </div>
                                            <div class="col-6 mt-2">
                                                <form action="' . route('app-ecommerce-customer-charge', $cliente->id) . '" method="POST" style="display:inline;">
                                                    ' . csrf_field() . '
                                                    ' . method_field('POST') . '
                                                    <button type="submit" class="btn btn-sm btn-warning w-100" data-bs-toggle="tooltip" data-bs-placement="top" title="Cobrança Manual">
                                                        <i class="fas fa-dollar-sign"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            <div class="col-6 mt-2">
                                                <a href="' . route('app-ecommerce-order-list', ['order_id' => $cliente->id]) . '" class="btn btn-sm btn-success w-100" data-bs-toggle="tooltip" data-bs-placement="top" title="Detalhes da Cobrança">
                                                    <i class="fas fa-thumbs-up"></i>
                                                </a>
                                            </div>
                                            <div class="col-6 mt-2">
                                                <button class="btn btn-sm btn-info w-100" data-bs-toggle="tooltip" data-bs-placement="top" title="Enviar Dados de Login" onclick="sendLoginDetails(\'' . $cliente->id . '\')">
                                                    <i class="fab fa-whatsapp"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>';
    
                        $modal = '<div class="modal fade" id="editClient' . $cliente->id . '" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-md modal-simple modal-edit-client">
                                            <div class="modal-content p-3 p-md-5">
                                                <div class="modal-body">
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    <div class="text-center mb-4">
                                                        <h3 class="mb-2">Editar Cliente</h3>
                                                        <p class="text-muted">Atualize os detalhes do cliente.</p>
                                                    </div>
                                                    <form id="editClientForm' . $cliente->id . '" class="row g-3" action="' . route('app-ecommerce-customer-update', $cliente->id) . '" method="POST">
                                                        ' . csrf_field() . '
                                                        ' . method_field('PUT') . '
                                                        <div class="col-12">
                                                            <label class="form-label" for="editClientNome' . $cliente->id . '">Nome</label>
                                                            <input type="text" id="editClientNome' . $cliente->id . '" name="nome" class="form-control" value="' . $cliente->nome . '" required />
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label" for="editClientPassword' . $cliente->id . '">Senha</label>
                                                            <div class="input-group">
                                                                <input type="password" id="editClientPassword' . $cliente->id . '" name="password" class="form-control" value="' . $cliente->password . '" required />
                                                                <button type="button" class="btn btn-outline-secondary" onclick="generatePassword(\'editClientPassword' . $cliente->id . '\')">
                                                                    <i class="fas fa-random"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVisibility(\'editClientPassword' . $cliente->id . '\')">
                                                                    <i class="fas fa-eye" id="togglePasswordIcon' . $cliente->id . '"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label" for="editClientIPTVNome' . $cliente->id . '">Usuário IPTV</label>
                                                            <input type="text" id="editClientIPTVNome' . $cliente->id . '" name="iptv_nome" class="form-control" value="' . $cliente->iptv_nome . '" />
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label" for="editClientIPTVSenha' . $cliente->id . '">Senha IPTV</label>
                                                            <div class="input-group">
                                                                <input type="text" id="editClientIPTVSenha' . $cliente->id . '" name="iptv_senha" class="form-control" value="' . $cliente->iptv_senha . '" />
                                                                <button type="button" class="btn btn-outline-secondary" onclick="generatePassword(\'editClientIPTVSenha' . $cliente->id . '\')">
                                                                    <i class="fas fa-random"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label" for="editClientWhatsApp' . $cliente->id . '">WhatsApp</label>
                                                            <input type="text" id="editClientWhatsApp' . $cliente->id . '" name="whatsapp" class="form-control" value="' . $cliente->whatsapp . '" required />
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label" for="editClientVencimento' . $cliente->id . '">Vencimento</label>
                                                            <input type="date" id="editClientVencimento' . $cliente->id . '" name="vencimento" class="form-control" value="' . $cliente->vencimento . '" required />
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label" for="editClientServidor' . $cliente->id . '">Servidor</label>
                                                            <select id="editClientServidor' . $cliente->id . '" name="servidor_id" class="form-select" required>';
                        foreach ($servidores as $servidor) {
                            $modal .= '<option value="' . $servidor->id . '" ' . ($cliente->servidor_id == $servidor->id ? 'selected' : '') . '>' . $servidor->nome . '</option>';
                        }
                        $modal .= '</select>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label" for="editClientMac' . $cliente->id . '">MAC</label>
                                                            <input type="text" id="editClientMac' . $cliente->id . '" name="mac" class="form-control" value="' . $cliente->mac . '" />
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label" for="editClientNotificacoes' . $cliente->id . '">Notificações</label>
                                                            <select id="editClientNotificacoes' . $cliente->id . '" name="notificacoes" class="form-select" required>
                                                                <option value="1" ' . ($cliente->notificacoes ? 'selected' : '') . '>Sim</option>
                                                                <option value="0" ' . (!$cliente->notificacoes ? 'selected' : '') . '>Não</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label" for="editClientPlano' . $cliente->id . '">Plano</label>
                                                            <select id="editClientPlano' . $cliente->id . '" name="plano_id" class="form-select" required>';
                        foreach ($planos as $plano) {
                            $modal .= '<option value="' . $plano->id . '" ' . ($cliente->plano_id == $plano->id ? 'selected' : '') . '>' . $plano->nome . '</option>';
                        }
                        $modal .= '</select>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label" for="editClientNumeroDeTelas' . $cliente->id . '">Número de Telas</label>
                                                            <input type="number" id="editClientNumeroDeTelas' . $cliente->id . '" name="numero_de_telas" class="form-control" value="' . $cliente->numero_de_telas . '" required />
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label" for="editClientNotas' . $cliente->id . '">Notas</label>
                                                            <textarea id="editClientNotas' . $cliente->id . '" name="notas" class="form-control">' . $cliente->notas . '</textarea>
                                                        </div>
                                                        <div class="col-12 text-center">
                                                            <button type="submit" class="btn btn-primary me-sm-3 me-1">Salvar</button>
                                                            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancelar</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>';
    
                        // Calcular a diferença entre a data de vencimento e a data atual
                        $vencimento = Carbon::parse($cliente->vencimento);
                        $hoje = Carbon::today();
                        $diasDiferenca = $hoje->diffInDays($vencimento, false);
    
                        if ($diasDiferenca == 0) {
                            $vencimentoTexto = '<span class="badge bg-warning">Vence hoje</span>';
                        } elseif ($diasDiferenca == 1) {
                            $vencimentoTexto = '<span class="badge bg-info">Vence amanhã</span>';
                        } elseif ($diasDiferenca == -1) {
                            $vencimentoTexto = '<span class="badge bg-danger">Venceu ontem</span>';
                        } elseif ($diasDiferenca > 1) {
                            $vencimentoTexto = '<span class="badge bg-success">Vence em ' . $diasDiferenca . ' dias</span>';
                        } elseif ($diasDiferenca < -1) {
                            $vencimentoTexto = '<span class="badge bg-danger">Venceu há ' . abs($diasDiferenca) . ' dias</span>';
                        } else {
                            $vencimentoTexto = '<span class="badge bg-danger">Vencido</span>';
                        }
    
                        return [
                            'id' => $cliente->id,
                            'nome' => $cliente->nome,
                            'iptv_nome' => $cliente->iptv_nome,
                            'whatsapp' => $cliente->whatsapp,
                            'vencimento' => $vencimentoTexto,
                            'servidor' => $cliente->servidor ? $cliente->servidor->nome : 'N/A',
                            'mac' => $cliente->mac,
                            'notificacoes' => $cliente->notificacoes ? 'Sim' : 'Não',
                            'plano' => $cliente->plano ? $cliente->plano->nome : 'N/A',
                            'valor' => $cliente->plano ? 'R$ ' . number_format($cliente->plano->preco, 2, ',', '.') : 'N/A',
                            'numero_de_telas' => $cliente->numero_de_telas,
                            'notas' => $cliente->notas,
                            'actions' => $actions . $modal
                        ];
                    });
    
                // Fetch user preferences for visible columns
                $userId = getAuthenticatedUser(true);
                $preferences = DB::table('user_client_preferences')
                    ->where('user_id', $userId)
                    ->where('table_name', 'clientes')
                    ->value('visible_columns');
    
                $visibleColumns = json_decode($preferences, true) ?: [
                    'id',
                    'nome',
                    'iptv_nome',
                    'whatsapp',
                    'vencimento',
                    'servidor',
                    'mac',
                    'notificacoes',
                    'plano',
                    'valor',
                    'numero_de_telas',
                    'notas',
                    'actions'
                ];
    
                // Filter the columns based on user preferences
                $filteredClientes = $clientes->map(function ($cliente) use ($visibleColumns) {
                    return array_filter($cliente, function ($key) use ($visibleColumns) {
                        return in_array($key, $visibleColumns);
                    }, ARRAY_FILTER_USE_KEY);
                });
    
                return response()->json([
                    'rows' => $filteredClientes,
                    'total' => $totalClientes,
                    'planos' => $planos,
                    'servidores' => $servidores,
                    'planos_revenda' => $planos_revenda,
                    'current_plan_id' => $current_plan_id,
                    'sessionData' => Session::all(),
                    'user' => $user,
                    'loginUrl' => route('client.login.form')
                ]);
            } else {
                // Usuário não está autenticado
                return response()->json(['error' => 'Usuário não autenticado'], 401);
            }
        } catch (\Exception $e) {
            Log::error('Erro ao acessar a listagem de clientes: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao acessar a listagem de clientes'], 500);
        }
    }
    public function destroy($id)
    {

        $cliente = Cliente::findOrFail($id);

        //verifica se o cliente existe
        if (!$cliente) {
            return redirect()->route('app-ecommerce-customer-all')->with('error', 'Cliente não encontrado.');
        }
        // Encontra o usuário correspondente na tabela users
        $user = User::findOrFail($cliente->user_id);

        //verifica se o usuário existe
        if (!$user) {
            return redirect()->route('app-ecommerce-customer-all')->with('error', 'Usuário não encontrado.');
        }
        // Verifica se o limite do usuário não é -1 (usuário não é ilimitado)
        if ($user->limite !== -1) {
            // Incrementa o limite do usuário
            $user->limite += 1;
            $user->save();
        }
        $cliente->delete();

        return redirect()->route('app-ecommerce-customer-all')->with('success', 'Cliente deletado com sucesso.');
    }



       public function destroy_multiple(Request $request)
    {
        Log::info('Tentando excluir múltiplos clientes.', ['ids' => $request->input('ids')]);
        $validatedData = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:clientes,id'
        ]);
    
        $ids = $validatedData['ids'];
        foreach ($ids as $id) {
            $cliente = Cliente::findOrFail($id);
    
            // Verifica se o cliente existe
            if (!$cliente) {
                return response()->json(['error' => true, 'message' => 'Cliente não encontrado.'], 404);
            }
    
            // Encontra o usuário correspondente na tabela users
            $user = User::findOrFail($cliente->user_id);
    
            // Verifica se o usuário existe
            if (!$user) {
                return response()->json(['error' => true, 'message' => 'Usuário não encontrado.'], 404);
            }
    
            // Verifica se o limite do usuário não é -1 (usuário não é ilimitado)
            if ($user->limite !== -1) {
                // Incrementa o limite do usuário
                $user->limite += 1;
                $user->save();
            }
    
            $cliente->delete();
        }
    
        return response()->json(['error' => false, 'message' => 'Clientes excluídos com sucesso.']);
    }

       public function cobrancaManual($clienteId)
    {
        Log::info('cobrancaManual chamada com clienteId: ' . $clienteId);
    
        try {
            $cliente = Cliente::findOrFail($clienteId);
    
            if (!$cliente) {
                return redirect()->route('app-ecommerce-customer-all')->with('warning', 'Cliente nao encontrado.');
            }
    
            if (!$cliente->notificacoes) {
                return redirect()->route('app-ecommerce-customer-all')->with('warning', 'Este cliente não pode receber notificações.');
            }
    
            $conexao = Conexao::where('user_id', $cliente->user_id)->first();
            if (!$conexao || $conexao->conn != 1) {
                return redirect()->route('app-ecommerce-customer-all')->with('warning', 'Você precisa conectar seu WhatsApp.');
            }
    
            $template = Template::where('finalidade', 'cobranca_manual')
                ->where('user_id', $cliente->user_id)
                ->first();
    
            if (!$template) {
                $template = Template::where('finalidade', 'cobranca_manual')
                    ->whereNull('user_id')
                    ->first();
            }
    
            if (!$template) {
                return redirect()->route('app-ecommerce-customer-all')->with('error', 'Você não tem um template configurado para esta finalidade. Por favor, configure um template para "cobranca_manual".');
            }
    
            $vencimentoFormatado = Carbon::parse($cliente->vencimento)->format('d/m/Y');
            $companyDetail = CompanyDetail::where('user_id', $cliente->user_id)->first();
    
            if (!$companyDetail) {
                return redirect()->route('app-ecommerce-customer-all')->with('error', 'Detalhes da empresa não encontrados. Você pode configurar esses dados em "Configurações".');
            }
    
            $plano = Plano::findOrFail($cliente->plano_id);
    
            $dadosCliente = [
                'nome' => $cliente->nome,
                'telefone' => $cliente->whatsapp,
                'notas' => $cliente->notas,
                'vencimento' => $vencimentoFormatado,
                'plano_nome' => $plano->nome,
                'plano_valor' => $plano->preco,
                'text_expirate' => $this->getTextExpirate(Carbon::parse($cliente->vencimento)->format('Y-m-d')),
                'data_pagamento' => 'Data do Pagamento',
                'status_pagamento' => 'Status do Pagamento',
                'nome_empresa' => $companyDetail->company_name,
                'whatsapp_empresa' => $companyDetail->company_whatsapp,
            ];
    
            $conexaoController = new ConexaoController();
    
            if ($companyDetail->not_gateway) {
                $conteudo = $this->substituirPlaceholders($template->conteudo, $dadosCliente);
                $conexaoController->sendMessage(new Request([
                    'phone' => $cliente->whatsapp,
                    'message' => $conteudo,
                ]));
    
                $conexaoController->sendMessage(new Request([
                    'phone' => $cliente->whatsapp,
                    'message' => "\n" . $companyDetail->pix_manual,
                ]));
    
                $mercadoPagoId = str_pad(mt_rand(0, 99999999999), 11, '0', STR_PAD_LEFT);
    
                Pagamento::create([
                    'cliente_id' => $cliente->id,
                    'user_id' => $cliente->user_id,
                    'mercado_pago_id' => $mercadoPagoId,
                    'valor' => $plano->preco,
                    'status' => 'pending',
                    'plano_id' => $cliente->plano_id,
                    'isAnual' => false,
                ]);
    
                return redirect()->route('app-ecommerce-customer-all')->with('success', 'Cobrança manual enviada com sucesso.');
            }
    
            $accessToken = $companyDetail->access_token;
            $adminUser = User::where('role_id', 1)->first();
    
            if (!$adminUser) {
                throw new \Exception('Administrador não encontrado.');
            }
    
            $adminCompanyDetail = CompanyDetail::where('user_id', $adminUser->id)->first();
    
            if (!$adminCompanyDetail) {
                throw new \Exception('Detalhes da empresa não encontrados para o administrador.');
            }
    
            $url_notification = $adminCompanyDetail->notification_url;
    
            if (!$accessToken) {
                throw new \Exception('Access Token não encontrado.');
            }
    
            MercadoPagoConfig::setAccessToken($accessToken);
            MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);
    
            $paymentClient = new PaymentClient();
            $valorPlano = (float) $plano->preco;
    
            if ($valorPlano <= 0) {
                return redirect()->route('app-ecommerce-customer-all')->with('error', 'Valor do plano deve ser positivo.');
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
                        'number' => '12345678909'
                    ]
                ]
            ];
    
            $requestOptions = new RequestOptions();
            $requestOptions->setCustomHeaders(["X-Idempotency-Key: " . uniqid()]);
    
            try {
                $response = $paymentClient->create($preference, $requestOptions);
                $paymentLink = $response->point_of_interaction->transaction_data->ticket_url;
                $payloadPix = $response->point_of_interaction->transaction_data->qr_code;
    
                $pagamento = Pagamento::create([
                    'cliente_id' => $cliente->id,
                    'user_id' => $cliente->user_id,
                    'mercado_pago_id' => $response->id,
                    'valor' => $valorPlano,
                    'status' => 'pending',
                    'plano_id' => $cliente->plano_id,
                    'isAnual' => false,
                ]);
    
                $dadosCliente['plano_link'] = $paymentLink;
                $dadosCliente['payload_pix'] = $payloadPix;
                $dadosCliente['data_pagamento'] = $pagamento->updated_at->format('d/m/Y') ?? 'Data do Pagamento';
                $dadosCliente['status_pagamento'] = $pagamento->status ?? 'Status do Pagamento';
    
                $conteudo = $this->substituirPlaceholders($template->conteudo, $dadosCliente);
                $conexaoController->sendMessage(new Request([
                    'phone' => $cliente->whatsapp,
                    'message' => $conteudo,
                ]));
    
                $conexaoController->sendMessage(new Request([
                    'phone' => $cliente->whatsapp,
                    'message' => "\n" . $payloadPix,
                ]));
    
                return redirect()->route('app-ecommerce-customer-all')->with('success', 'Cobrança manual enviada com sucesso.');
            } catch (MPApiException $e) {
                return redirect()->route('app-ecommerce-customer-all')->with('error', 'Erro ao criar preferência de pagamento.');
            } catch (\Exception $e) {
                return redirect()->route('app-ecommerce-customer-all')->with('error', 'Erro ao criar preferência de pagamento.');
            }
        } catch (\Exception $e) {
            return redirect()->route('app-ecommerce-customer-all')->with('error', 'Ocorreu um erro ao processar a cobrança manual.');
        }
    }

    public function getCustomerData(Request $request)
    {
        $user = Auth::user();

        if ($user->role->name === 'admin') {
            $clientes = Cliente::with('plano', 'servidor')->select('clientes.*');
        } else {
            $clientes = Cliente::where('user_id', $user->id)->with('plano', 'servidor')->select('clientes.*');
        }

        return DataTables::of($clientes)
            ->addColumn('servidor', function ($cliente) {
                return $cliente->servidor ? $cliente->servidor->nome : 'Sem Servidor';
            })
            ->addColumn('plano', function ($cliente) {
                return $cliente->plano ? $cliente->plano->nome : 'Sem Plano';
            })
            ->addColumn('valor', function ($cliente) {
                return $cliente->plano ? 'R$ ' . number_format($cliente->plano->preco, 2, ',', '.') : 'Sem Plano';
            })
            ->addColumn('acoes', function ($cliente) {
                return view('partials.actions', compact('cliente'))->render();
            })
            ->make(true);
    }

     
    public function sendLoginDetails($clienteId)
    {
        Log::info('sendLoginDetails chamada com clienteId: ' . $clienteId);
    
        try {
            $cliente = Cliente::findOrFail($clienteId);
    
            // Verifica se o cliente pode receber notificações
            if (!$cliente->notificacoes) {
                return redirect()->route('app-ecommerce-customer-all')->with('warning', 'Este cliente não pode receber notificações.');
            }
    
            // Verifica se o cliente está conectado ao WhatsApp
            $conexao = Conexao::where('user_id', $cliente->user_id)->first();
            if (!$conexao || $conexao->conn != 1) {
                return redirect()->route('app-ecommerce-customer-all')->with('warning', 'Você precisa conectar seu WhatsApp.');
            }
    
            // Buscar o template específico para o user_id do cliente
            $template = Template::where('finalidade', 'dados_iptv')
                ->where('user_id', $cliente->user_id)
                ->first();
    
            // Se não encontrar um template específico para o user_id, buscar um template padrão
            if (!$template) {
                $template = Template::where('finalidade', 'dados_iptv')
                    ->whereNull('user_id')
                    ->first();
            }
    
            // Verifique se o template foi encontrado
            if (!$template) {
                return redirect()->route('app-ecommerce-customer-all')->with('error', 'Você não tem um template configurado para esta finalidade. Por favor, configure um template para "dados_iptv".');
            }
    
            // Obter os detalhes da empresa
            $companyDetail = CompanyDetail::where('user_id', $cliente->user_id)->first();
    
            if (!$companyDetail) {
                return redirect()->route('app-ecommerce-customer-all')->with('error', 'Detalhes da empresa não encontrados. Você pode configurar esses dados em "Configurações".');
            }
    
            // Obter a URL de login
            $loginUrl = url('/client/login');
    
            $dadosCliente = [
                'nome' => $cliente->nome,
                'telefone' => $cliente->whatsapp,
                'iptv_nome' => $cliente->iptv_nome,
                'iptv_senha' => $cliente->iptv_senha,
                'password' => $cliente->password,
                'login_url' => $loginUrl,
                'nome_empresa' => $companyDetail->company_name,
                'whatsapp_empresa' => $companyDetail->company_whatsapp,
            ];
    
            $conteudo = $this->substituirPlaceholders($template->conteudo, $dadosCliente);
    
            $conexaoController = new ConexaoController();
    
            // Enviar mensagem para o cliente
            $responseCliente = $conexaoController->sendMessage(new Request([
                'phone' => $cliente->whatsapp,
                'message' => $conteudo,
            ]));
    
            return redirect()->route('app-ecommerce-customer-all')->with('success', 'Dados de login enviados com sucesso.');
        } catch (\Exception $e) {
            return redirect()->route('app-ecommerce-customer-all')->with('error', 'Ocorreu um erro ao enviar os dados de login.');
        }
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
            '{payload_pix}' => $dadosCliente['payload_pix'] ?? '',
            '{text_expirate}' => $dadosCliente['text_expirate'] ?? '',
            '{saudacao}' => $this->getSaudacao(),
            '{data_pagamento}' => $dadosCliente['data_pagamento'] ?? 'Data do Pagamento',
            '{status_pagamento}' => $dadosCliente['status_pagamento'] ?? 'Status do Pagamento',
            '{nome_empresa}' => $dadosCliente['nome_empresa'] ?? 'Nome da Empresa',
            '{whatsapp_empresa}' => $dadosCliente['whatsapp_empresa'] ?? '(11) 99999-9999',
            '{whatsap_empresa}' => $dadosCliente['whatsap_empresa'] ?? '(11) 99999-9999',
            '{nome_dono}' => $dadosCliente['nome_dono'] ?? 'Nome do Dono',
            '{whatsapp_dono}' => $dadosCliente['whatsapp_dono'] ?? '(11) 99999-9999',
            '{iptv_nome}' => $dadosCliente['iptv_nome'] ?? 'Nome de Usuário IPTV',
            '{iptv_senha}' => $dadosCliente['iptv_senha'] ?? 'Senha IPTV',
            '{password}' => $dadosCliente['password'] ?? 'Senha',
            '{login_url}' => $dadosCliente['login_url'] ?? 'URL de Login',
        ];

        foreach ($placeholders as $placeholder => $valor) {
            $conteudo = str_replace($placeholder, $valor, $conteudo);
        }

        return $conteudo;
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
}
