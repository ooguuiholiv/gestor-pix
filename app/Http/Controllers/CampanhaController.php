<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campanha;
use App\Models\Cliente;
use Illuminate\Support\Facades\Auth;
use App\Models\PlanoRenovacao;
use App\Models\Plano;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use App\Models\Servidor;
use App\Services\LicenseService;

use Illuminate\Support\Facades\Log;

class CampanhaController extends Controller
{
    protected $licenseService;

    public function __construct(LicenseService $licenseService)
    {
        // Aplicar middleware de autenticação
        $this->middleware('auth');
        $this->licenseService = $licenseService;
    }

    public function index()
    {
        $user = Auth::user();
        $domain = request()->getHost();

        // Verificar licença e módulos ativos
        $activeModules = $this->licenseService->checkLicenseAndModules($domain);

        // Verifica se o módulo de campanhas está ativo
        $campanhasAtivo = isset($activeModules['campanhas']) && $activeModules['campanhas'];

        // Verifica se o usuário é um administrador
        if ($user->role->name === 'admin') {
            // Lógica para exibir todas as campanhas
            $campanhas = Campanha::all();
            // Lógica para exibir todos os clientes
            $clientes = Cliente::all();
            // Lógica para exibir todos os servidores
            $servidores = Servidor::withCount('clientes')->get();
        } else {
            // Lógica para exibir apenas as campanhas do usuário autenticado
            $campanhas = Campanha::where('user_id', $user->id)->get();
            // Lógica para exibir apenas os clientes do usuário autenticado
            $clientes = Cliente::where('user_id', $user->id)->get();
            // Lógica para exibir apenas os servidores do usuário autenticado
            $servidores = Servidor::where('user_id', $user->id)->withCount('clientes')->get();
        }

        // Filtrar clientes vencidos, que vencem hoje e ativos
        $hoje = now()->format('Y-m-d');
        $clientesVencidos = $clientes->filter(function ($cliente) use ($hoje) {
            return $cliente->vencimento < $hoje;
        });
        $clientesVencemHoje = $clientes->filter(function ($cliente) use ($hoje) {
            return $cliente->vencimento == $hoje;
        });
        $clientesAtivos = $clientes->filter(function ($cliente) use ($hoje) {
            return $cliente->vencimento > $hoje;
        });

        $planos = Plano::all();
        $planos_revenda = PlanoRenovacao::all();
        $current_plan_id = $user->plano_id;

        return view('campanhas.index', compact(
            'campanhas', 
            'planos', 
            'planos_revenda', 
            'current_plan_id', 
            'clientes', 
            'clientesVencidos', 
            'clientesVencemHoje', 
            'clientesAtivos', 
            'campanhasAtivo', 
            'domain',
            'servidores' // Passar os servidores para a view
        ));
    }

    public function create()
    {
        $user = Auth::user();
        $clientes = Cliente::where('user_id', $user->id)->get();
        return view('campanhas.create', compact('clientes'));
    }

  public function store(Request $request)
    {
        Log::info('Criando nova campanha' . json_encode($request->all()));
        $request->validate([
            'nome' => 'required|string|max:255',
            'horario' => 'required|date_format:H:i',
            'data' => 'nullable|date',
            'contatos' => 'nullable|array',
            'servidores' => 'nullable|array',
            'origem_contatos' => 'required|string',
            'ignorar_contatos' => 'required|boolean',
            'mensagem' => 'required|string',
            'arquivo' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,mp4,avi,mov,wmv|max:20480', // 20MB = 20480KB
            'enviar_diariamente' => 'required|boolean',
        ]);
    
        $user = Auth::user();
    
        $campanha = new Campanha();
        $campanha->user_id = $user->id;
        $campanha->nome = $request->nome;
        $campanha->horario = $request->horario;
        $campanha->data = $request->data;
        $campanha->origem_contatos = $request->origem_contatos;
        $campanha->ignorar_contatos = $request->ignorar_contatos;
        $campanha->mensagem = $request->mensagem;
        $campanha->enviar_diariamente = $request->enviar_diariamente;
    
        // Processar contatos e servidores
        if ($request->origem_contatos === 'servidores' && $request->has('servidores')) {
            $contatos = [];
            foreach ($request->servidores as $servidorId) {
                $servidor = Servidor::with('clientes')->find($servidorId);
                if ($servidor) {
                    foreach ($servidor->clientes as $cliente) {
                        $contatos[] = $cliente->id;
                    }
                }
            }
            $campanha->contatos = $contatos;
        } else {
            $campanha->contatos = $request->contatos;
        }
    
        if ($request->hasFile('arquivo')) {
            // Define permissões 777 na pasta
            $directory = public_path('assets/campanhas_arquivos');
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
                \Log::info('Diretório criado: ' . $directory);
            }
            chmod($directory, 0777);
            \Log::info('Permissões definidas para o diretório: ' . $directory);
    
            // Store new file
            $fileName = $request->file('arquivo')->getClientOriginalName();
            $path = $request->file('arquivo')->move($directory, $fileName);
            if ($path) {
                $campanha->arquivo = '/assets/campanhas_arquivos/' . $fileName; // Salva o caminho relativo no banco de dados
                \Log::info('Novo arquivo armazenado em: ' . $campanha->arquivo);
            } else {
                \Log::error('Falha ao armazenar o novo arquivo.');
            }
        }
    
        $campanha->save();
    
        return redirect()->route('campanhas.index')->with('success', 'Campanha criada com sucesso!');
    }
    

    public function show($id)
    {
        $campanha = Campanha::findOrFail($id);
        return view('campanhas.show', compact('campanha'));
    }

    public function edit($id)
    {
        $campanha = Campanha::findOrFail($id);
        $user = Auth::user();
        $clientes = Cliente::where('user_id', $user->id)->get();
        return view('campanhas.edit', compact('campanha', 'clientes'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'horario' => 'required|date_format:H:i',
            'data' => 'nullable|date',
            'contatos' => 'nullable|array',
            'servidores' => 'nullable|array',
            'origem_contatos' => 'required|string',
            'ignorar_contatos' => 'required|boolean',
            'mensagem' => 'required|string',
            'arquivo' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,mp4,avi,mov,wmv|max:20480', // 20MB = 20480KB
        ]);
    
        $campanha = Campanha::findOrFail($id);
        $campanha->nome = $request->nome;
        $campanha->horario = $request->horario;
        $campanha->data = $request->data;
        $campanha->origem_contatos = $request->origem_contatos;
        $campanha->ignorar_contatos = $request->ignorar_contatos;
        $campanha->mensagem = $request->mensagem;
    
        // Processar contatos e servidores
        if ($request->origem_contatos === 'servidores' && $request->has('servidores')) {
            $contatos = [];
            foreach ($request->servidores as $servidorId) {
                $servidor = Servidor::with('clientes')->find($servidorId);
                if ($servidor) {
                    foreach ($servidor->clientes as $cliente) {
                        $contatos[] = $cliente->id;
                    }
                }
            }
            $campanha->contatos = $contatos;
        } else {
            $campanha->contatos = $request->contatos;
        }
    
        if ($request->hasFile('arquivo')) {
            // Remove o arquivo antigo, se existir
            if ($campanha->arquivo) {
                Storage::disk('public')->delete($campanha->arquivo);
            }
    
            // Define permissões 777 na pasta
            $directory = public_path('assets/campanhas_arquivos');
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
                \Log::info('Diretório criado: ' . $directory);
            }
            chmod($directory, 0777);
            \Log::info('Permissões definidas para o diretório: ' . $directory);
    
            // Store new file
            $fileName = $request->file('arquivo')->getClientOriginalName();
            $path = $request->file('arquivo')->move($directory, $fileName);
            if ($path) {
                $campanha->arquivo = '/assets/campanhas_arquivos/' . $fileName; // Salva o caminho relativo no banco de dados
                \Log::info('Novo arquivo armazenado em: ' . $campanha->arquivo);
            } else {
                \Log::error('Falha ao armazenar o novo arquivo.');
            }
        }
    
        $campanha->save();
    
        return redirect()->route('campanhas.index')->with('success', 'Campanha atualizada com sucesso!');
    }
    public function destroy($id)
    {
        $campanha = Campanha::findOrFail($id);
        if ($campanha->arquivo) {
            Storage::disk('public')->delete($campanha->arquivo);
        }
        $campanha->delete();

        return redirect()->route('campanhas.index')->with('success', 'Campanha excluída com sucesso!');
    }
}
