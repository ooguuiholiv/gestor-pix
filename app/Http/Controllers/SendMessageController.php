<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\Conexao;
use App\Models\CompanyDetail;
use App\Models\User;

class SendMessageController extends Controller
{
  private $apikey;
  private $urlapi;

  public function __construct()
  {
      // Obter o usuário administrador
      $adminUser = User::where('role_id', 1)->first();
      if ($adminUser) {
          // Obter os detalhes da empresa para o usuário administrador
          $companyDetail = CompanyDetail::where('user_id', $adminUser->id)->first();
          if ($companyDetail) {
              $this->apikey = $companyDetail->evolution_api_key;
              $this->urlapi = $companyDetail->evolution_api_url;
          }
      }
  }


    public function sendMessageWithoutAuth(Request $request)
    {
        // Log::info('sendMessageWithoutAuth chamada com dados: ' . json_encode($request->all()));

        $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string',
            'user_id' => 'required|integer',
        ]);

        $phone = $request->input('phone');
        $message = $request->input('message');
        $user_id = $request->input('user_id');

        // Buscar o tokenid da tabela conexoes
        $conexao = Conexao::where('user_id', $user_id)->first();
        if (!$conexao) {
            Log::error('Conexão não encontrada para o user_id: ' . $user_id);
            return response()->json(['error' => 'Conexão não encontrada.'], 404);
        }

        $tokenid = $conexao->tokenid;
        if (!preg_match('/^\+?\d{1,3}/', $phone)) {
            // Adicionar o código do país 55 para números brasileiros
            if (preg_match('/^\d{10,11}$/', $phone)) {
                $phone = '55' . $phone;
            } else {
                return response()->json(['error' => 'Número de telefone inválido.'], 400);
            }
        }

        // Remover quaisquer caracteres não numéricos
        $celular = preg_replace('/\D/', '', $phone);

        // Verificar se o número é brasileiro e adicionar o código do país se necessário
        if (strlen($celular) == 10 || strlen($celular) == 11) {
            $celular = '55' . $celular;
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'apikey' => $this->apikey,
            ])->withOptions([
                'verify' => false,
            ])->post($this->urlapi . '/message/sendText/HostShopBR' . $tokenid, [
                'number' => $celular,
                'options' => [
                    'delay' => 10000,
                    'presence' => 'composing',
                ],
                'textMessage' => [
                    'text' => $message,
                ],
            ]);

            $result = $response->json();
            // Log::info('Resposta da API: ' . json_encode($result));

            if (isset($result['status']) && in_array($result['status'], ['200', 'PENDING'])) {
                // Log::info('Mensagem enviada com sucesso.');
                return response()->json(['success' => 'Mensagem enviada com sucesso.']);
            } else {
                // Log::error('Erro ao enviar mensagem:', $result);
                return response()->json(['error' => 'Erro ao enviar mensagem.'], 500);
            }
        } catch (\Exception $e) {
            // Log::error('Exceção ao enviar mensagem: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao enviar mensagem.'], 500);
        }
    }


public function sendMedia(Request $request)
{
    $request->validate([
        'phone' => 'required|string',
        'media' => 'required|string',
        'user_id' => 'required|integer',
        'caption' => 'nullable|string',
        'fileName' => 'required|string',
    ]);

    $phone = $request->input('phone');
    $media = $request->input('media');
    $user_id = $request->input('user_id');
    $caption = $request->input('caption', '');
    $fileName = $request->input('fileName');

    Log::info('Iniciando envio de mídia para o telefone: ' . $phone);

    $conexao = Conexao::where('user_id', $user_id)->first();
    if (!$conexao) {
        Log::error('Conexão não encontrada para o user_id: ' . $user_id);
        return response()->json(['error' => 'Conexão não encontrada.'], 404);
    }

    $tokenid = $conexao->tokenid;
    // $celular = "55" . str_replace(['.', '/', '-', ' ', '(', ')'], '', $phone);


    if (!preg_match('/^\+?\d{1,3}/', $phone)) {
        // Adicionar o código do país 55 para números brasileiros
        if (preg_match('/^\d{10,11}$/', $phone)) {
            $phone = '55' . $phone;
        } else {
            return response()->json(['error' => 'Número de telefone inválido.'], 400);
        }
    }

    // Remover quaisquer caracteres não numéricos
    $celular = preg_replace('/\D/', '', $phone);

    // Verificar se o número é brasileiro e adicionar o código do país se necessário
    if (strlen($celular) == 10 || strlen($celular) == 11) {
        $celular = '55' . $celular;
    }

    $fullPath = public_path($media);
    Log::info('Verificando o caminho absoluto do arquivo: ' . $fullPath);
    if (!file_exists($fullPath)) {
        // Log::error('Arquivo não encontrado: ' . $fullPath);
        return response()->json(['error' => 'Arquivo não encontrado.'], 404);
    }

    $fileContent = file_get_contents($fullPath);
    if ($fileContent === false) {
        // Log::error('Erro ao ler o conteúdo do arquivo: ' . $fullPath);
        return response()->json(['error' => 'Erro ao ler o conteúdo do arquivo.'], 500);
    }
    $base64Content = base64_encode($fileContent);

    // Determinar o tipo de mídia com base na extensão do arquivo
    $fileExtension = pathinfo($fullPath, PATHINFO_EXTENSION);
    $mimeType = '';
    $mediaType = '';

    switch (strtolower($fileExtension)) {
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif':
            $mimeType = 'image/' . $fileExtension;
            $mediaType = 'image';
            break;
        case 'mp4':
        case 'avi':
        case 'mov':
        case 'wmv':
            $mimeType = 'video/' . $fileExtension;
            $mediaType = 'video';
            break;
        default:
            // Log::error('Tipo de arquivo não suportado: ' . $fileExtension);
            return response()->json(['error' => 'Tipo de arquivo não suportado.'], 400);
    }

    // Log::info('Enviando mídia para ' . $phone . ': ' . $base64Content);

    $response = Http::withHeaders([
        'Content-Type' => 'application/json',
        'apikey' => $this->apikey,
    ])->withOptions([
        'verify' => false,
    ])->post($this->urlapi . '/message/sendMedia/HostShopBR' . $tokenid, [
        'number' => $celular,
        'options' => [
            'delay' => 10000,
            'presence' => 'composing',
        ],
        'mediaMessage' => [
            'mediatype' => $mediaType,
            'fileName' => $fileName,
            'caption' => $caption,
            'media' => $base64Content, // Removido 'data:' . $mimeType . ';base64,'
            'mimetype' => $mimeType,
        ],
    ]);

    $result = $response->json();
    // Log::info('Resposta da API:', $result);

    if (isset($result['status']) && $result['status'] == 200) {
        Log::info('Mensagem enviada com sucesso para ' . $phone);
        return response()->json(['success' => 'Mensagem enviada com sucesso.']);
    } else {
        // Log::error('Erro ao enviar mensagem:', $result);
        return response()->json(['error' => 'Erro ao enviar mensagem.'], 500);
    }
}



public function sendMediaWithCurl(Request $request)
{
    $request->validate([
        'phone' => 'required|string',
        'media' => 'required|string',
        'user_id' => 'required|integer',
        'caption' => 'nullable|string',
        'fileName' => 'required|string',
    ]);

    $phone = $request->input('phone');
    $media = $request->input('media');
    $user_id = $request->input('user_id');
    $caption = $request->input('caption', '');
    $fileName = $request->input('fileName');

    $conexao = Conexao::where('user_id', $user_id)->first();
    if (!$conexao) {
        // Log::error('Conexão não encontrada para o user_id: ' . $user_id);
        return response()->json(['error' => 'Conexão não encontrada.'], 404);
    }

    $tokenid = $conexao->tokenid;
    // $celular = "55" . str_replace(['.', '/', '-', ' ', '(', ')'], '', $phone);

    if (!preg_match('/^\+?\d{1,3}/', $phone)) {
        // Adicionar o código do país 55 para números brasileiros
        if (preg_match('/^\d{10,11}$/', $phone)) {
            $phone = '55' . $phone;
        } else {
            return response()->json(['error' => 'Número de telefone inválido.'], 400);
        }
    }

    // Remover quaisquer caracteres não numéricos
    $celular = preg_replace('/\D/', '', $phone);

    // Verificar se o número é brasileiro e adicionar o código do país se necessário
    if (strlen($celular) == 10 || strlen($celular) == 11) {
        $celular = '55' . $celular;
    };

    $fullPath = public_path($media);
    Log::info('Verificando o caminho absoluto do arquivo: ' . $fullPath);
    if (!file_exists($fullPath)) {
        // Log::error('Arquivo não encontrado: ' . $fullPath);
        return response()->json(['error' => 'Arquivo não encontrado.'], 404);
    }

    $fileContent = file_get_contents($fullPath);
    if ($fileContent === false) {
        // Log::error('Erro ao ler o conteúdo do arquivo: ' . $fullPath);
        return response()->json(['error' => 'Erro ao ler o conteúdo do arquivo.'], 500);
    }
    $base64Content = base64_encode($fileContent);

    // Log::info('Enviando mídia para ' . $phone . ': ' . $base64Content);

    $response = Http::withHeaders([
        'Content-Type' => 'application/json',
        'apikey' => $this->apikey,
    ])->withOptions([
        'verify' => false,
    ])->post($this->urlapi . '/message/sendMedia/HostShopBR' . $tokenid, [
        'number' => $celular,
        'options' => [
            'delay' => 10000,
            'presence' => 'composing',
        ],
        'mediaMessage' => [
            'mediatype' => 'document',
            'fileName' => $fileName,
            'caption' => $caption,
            'media' => $base64Content,
            'mimetype' => 'application/pdf',
        ],
    ]);

    $result = $response->json();
    // Log::info('Resposta da API:', $result);

    if (isset($result['status']) && $result['status'] == 200) {
        return response()->json(['success' => 'Mensagem enviada com sucesso.']);
    } else {
        Log::error('Erro ao enviar mensagem:', $result);
        return response()->json(['error' => 'Erro ao enviar mensagem.'], 500);
    }
}
}
