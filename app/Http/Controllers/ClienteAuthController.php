<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cliente;

class ClienteAuthController extends Controller
{
    public function showClientLoginForm()
    {
        $pageConfigs = ['myLayout' => 'blank'];
        return view('client.login', ['pageConfigs' => $pageConfigs]);
    }

    public function clientLogin(Request $request)
    {
        $request->validate([
            'whatsapp' => 'required',
            'password' => 'required',
        ]);
    
        $cliente = Cliente::where('whatsapp', $request->whatsapp)->first();
    
        if ($cliente && $cliente->password === $request->password) {
            Auth::guard('cliente')->login($cliente);
            return redirect()->intended('/client/dashboard');
        }
    
        return redirect()->back()->withErrors(['whatsapp' => 'Credenciais inválidas']);
    }
}