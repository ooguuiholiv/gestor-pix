<?php

namespace App\Http\Controllers\apps;

use Illuminate\Http\Request;
use App\Models\CompanyDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\PlanoRenovacao;
use Illuminate\Support\Facades\Log;

class EcommerceSettingsDetails extends Controller
{
    public function __construct()
    {
        // Aplicar middleware de autenticação
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $companyDetails = CompanyDetail::where('user_id', $user->id)->first();

        $planos_revenda = PlanoRenovacao::all();
        $current_plan_id = $user->plano_id;
        return view('content.apps.configuracoes', compact('companyDetails', 'planos_revenda', 'current_plan_id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'company_whatsapp' => 'required|string|max:20',
            'access_token' => 'nullable|string|max:255',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png|max:1024', // Validação para favicon
            'pix_manual' => 'nullable|string|max:255',
            'referral_balance' => 'nullable|numeric',
            'api_session' => 'nullable|string|max:255',
            'public_key' => 'nullable|string|max:255',
            'site_id' => 'nullable|string|max:255',
            'evolution_api_url' => 'nullable|string|max:255',
            'evolution_api_key' => 'nullable|string|max:255',
            'not_gateway' => 'nullable|boolean',
            'notification_url' => 'nullable|string|max:255',
        ]);
    
        $data = [
            'user_id' => Auth::id(),
            'company_name' => $request->company_name,
            'company_whatsapp' => $request->company_whatsapp,
            'access_token' => $request->access_token,
            'pix_manual' => $request->pix_manual,
            'api_session' => $request->api_session,
            'not_gateway' => $request->not_gateway,
        ];
    
        if ($request->hasFile('company_logo')) {
            // Define permissões 777 na pasta
            $directory = public_path('assets/img/logos');
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }
            chmod($directory, 0777);
    
            // Store new company logo
            $fileName = $request->file('company_logo')->getClientOriginalName();
            $path = $request->file('company_logo')->move($directory, $fileName);
            if ($path) {
                $data['company_logo'] = '/assets/img/logos/' . $fileName; // Salva o caminho relativo no banco de dados
            }
        }
    
        if ($request->hasFile('favicon')) {
            // Define permissões 777 na pasta
            $directory = public_path('assets/img/favicons');
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }
            chmod($directory, 0777);
    
            // Store new favicon
            $fileName = $request->file('favicon')->getClientOriginalName();
            $path = $request->file('favicon')->move($directory, $fileName);
            if ($path) {
                $data['favicon'] = '/assets/img/favicons/' . $fileName; // Salva o caminho relativo no banco de dados
            }
        }
    
        // Verificar se o usuário é administrador
        if (Auth::user()->role_id === 1) {
            $data['referral_balance'] = $request->referral_balance;
            $data['api_session'] = $request->api_session;
            $data['public_key'] = $request->public_key;
            $data['site_id'] = $request->site_id;
            $data['evolution_api_url'] = $request->evolution_api_url;
            $data['evolution_api_key'] = $request->evolution_api_key;
            $data['notification_url'] = $request->notification_url;
        }
    
        try {
            CompanyDetail::create($data);
        } catch (\Exception $e) {
            Log::error('Erro ao salvar os detalhes da empresa: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao salvar os detalhes da empresa.');
        }
    
        return redirect()->back()->with('success', 'Detalhes da empresa salvos com sucesso.');
    }

       
    public function update(Request $request, $id)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'company_whatsapp' => 'required|string|max:20',
            'access_token' => 'nullable|string|max:255',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png|max:1024', // Validação para favicon
            'pix_manual' => 'nullable|string|max:255',
            'referral_balance' => 'nullable|numeric',
            'api_session' => 'nullable|string|max:255',
            'public_key' => 'nullable|string|max:255',
            'site_id' => 'nullable|string|max:255',
            'evolution_api_url' => 'nullable|string|max:255',
            'evolution_api_key' => 'nullable|string|max:255',
            'not_gateway' => 'nullable|boolean',
            'notification_url' => 'nullable|string|max:255',
        ]);
    
        try {
            $companyDetail = CompanyDetail::findOrFail($id);
        } catch (\Exception $e) {
            Log::error('Detalhes da empresa não encontrados: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Detalhes da empresa não encontrados.');
        }
    
        $companyDetail->user_id = Auth::id();
        $companyDetail->company_name = $request->company_name;
        $companyDetail->company_whatsapp = $request->company_whatsapp;
        $companyDetail->access_token = $request->access_token;
        $companyDetail->pix_manual = $request->pix_manual;
        $companyDetail->not_gateway = $request->not_gateway;
    
        // Verificar se o usuário é administrador antes de atualizar o saldo de indicações e a sessão da API
        if (Auth::user()->role_id === 1) {
            $companyDetail->referral_balance = $request->referral_balance;
            $companyDetail->api_session = $request->api_session;
            $companyDetail->public_key = $request->public_key;
            $companyDetail->site_id = $request->site_id;
            $companyDetail->evolution_api_url = $request->evolution_api_url;
            $companyDetail->evolution_api_key = $request->evolution_api_key;
            $companyDetail->notification_url = $request->notification_url;
        }
    
        if ($request->hasFile('company_logo')) {
            // Define permissões 777 na pasta
            $directory = public_path('assets/img/logos');
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }
            chmod($directory, 0777);
    
            // Delete old company logo if exists
            if ($companyDetail->company_logo) {
                $oldLogoPath = public_path($companyDetail->company_logo);
                if (file_exists($oldLogoPath)) {
                    unlink($oldLogoPath);
                }
            }
    
            // Store new company logo
            $fileName = $request->file('company_logo')->getClientOriginalName();
            $path = $request->file('company_logo')->move($directory, $fileName);
            if ($path) {
                $companyDetail->company_logo = '/assets/img/logos/' . $fileName; // Salva o caminho relativo no banco de dados
            }
        }
    
        if ($request->hasFile('favicon')) {
            // Define permissões 777 na pasta
            $directory = public_path('assets/img/favicons');
            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }
            chmod($directory, 0777);
    
            // Delete old favicon if exists
            if ($companyDetail->favicon) {
                $oldFaviconPath = public_path($companyDetail->favicon);
                if (file_exists($oldFaviconPath)) {
                    unlink($oldFaviconPath);
                }
            }
    
            // Store new favicon
            $fileName = $request->file('favicon')->getClientOriginalName();
            $path = $request->file('favicon')->move($directory, $fileName);
            if ($path) {
                $companyDetail->favicon = '/assets/img/favicons/' . $fileName; // Salva o caminho relativo no banco de dados
            }
        }
    
        try {
            $companyDetail->save();
        } catch (\Exception $e) {
            Log::error('Erro ao salvar os detalhes da empresa: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao salvar os detalhes da empresa.');
        }
    
        return redirect()->back()->with('success', 'Detalhes da empresa atualizados com sucesso.');
    }

  
    public function destroy($id)
    {
        try {
            $companyDetail = CompanyDetail::findOrFail($id);
        } catch (\Exception $e) {
            Log::error('Detalhes da empresa não encontrados: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Detalhes da empresa não encontrados.');
        }
    
        if ($companyDetail->company_logo) {
            $oldLogoPath = public_path($companyDetail->company_logo);
            if (file_exists($oldLogoPath)) {
                unlink($oldLogoPath);
            }
        }
        if ($companyDetail->favicon) {
            $oldFaviconPath = public_path($companyDetail->favicon);
            if (file_exists($oldFaviconPath)) {
                unlink($oldFaviconPath);
            }
        }
    
        try {
            $companyDetail->delete();
        } catch (\Exception $e) {
            Log::error('Erro ao deletar os detalhes da empresa: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao deletar os detalhes da empresa.');
        }
    
        return redirect()->back()->with('success', 'Detalhes da empresa deletados com sucesso.');
    }
}