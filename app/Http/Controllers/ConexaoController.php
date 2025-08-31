<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conexao;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\PlanoRenovacao;
use App\Models\CompanyDetail;
use App\Models\User;

class ConexaoController extends Controller
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
    public function index()
    {
        $user = auth()->user();
        $conexoes = Conexao::where('user_id', $user->id)->get();
        $planos_revenda = PlanoRenovacao::all();
        $current_plan_id = $user->plano_id;

        return view('content.apps.app-whatsapp', compact('conexoes', 'planos_revenda', 'current_plan_id'));
    }

    public function createConnection(Request $request)
    {
        if ($request->has('phone')) {
            $phone = $request->input('phone');
            $user = auth()->user();

            // Log para verificar as variáveis de ambiente
            // Log::info('EVOLUTION_API_URL: ' . $this->urlapi);
            // Log::info('EVOLUTION_API_KEY: ' . $this->apikey);

            $conexaoExistente = Conexao::where('whatsapp', $phone)->first();
            if ($conexaoExistente) {
                return redirect()->route('app-whatsapp')->with('error', 'Já existe uma conexão para este número de WhatsApp.');
            }


            $tokenid = bin2hex(random_bytes(16));
            $celular = "55" . $phone;

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'apikey' => $this->apikey,
            ])->withOptions([
                        'verify' => false,
                    ])->post($this->urlapi . '/instance/create', [
                        'instanceName' => 'HostShopBR' . $tokenid,
                        'token' => $tokenid,
                        'qrcode' => true,
                        'number' => $celular,
                    ]);

            $res = $response->json();

            if (isset($res['instance']) && $res['instance']['status'] == 'created' && isset($res['qrcode']['base64'])) {
                $qrcodelink = $res['qrcode']['base64'];

                // Salvar a nova conexão no banco de dados
                Conexao::create([
                    'user_id' => $user->id,
                    'qrcode' => $qrcodelink,
                    'conn' => 0,
                    'whatsapp' => $phone,
                    'tokenid' => $tokenid,
                    'notifica' => 0,
                    'saudacao' => null,
                    'arquivo' => null,
                    'midia' => null,
                    'tipo' => null,
                ]);

                // Redirecionar para a rota app-whatsapp
                return redirect()->route('app-whatsapp');
            }
        }

        // Caso haja algum erro, redirecionar de volta com uma mensagem de erro (opcional)
        return redirect()->back()->withErrors(['error' => 'Erro ao criar a conexão.']);
    }

    public function updateConnection(Request $request)
    {
        if ($request->has('phone')) {
            $phone = $request->input('phone');
            $user = auth()->user();
            $conexao = Conexao::where('user_id', $user->id)->first(); // Verifica se já existe uma conexão

            // // Log para verificar as variáveis de ambiente
            // Log::info('EVOLUTION_API_URL: ' . $this->urlapi);
            // Log::info('EVOLUTION_API_KEY: ' . $this->apikey);

            try {
                if ($conexao) {
                    $tokenid = $conexao->tokenid;
                    $celular = "55" . $phone;

                    // Verificar o estado da conexão
                    $response = Http::withHeaders([
                        'apikey' => $this->apikey,
                    ])->withOptions([
                                'verify' => false,
                            ])->get($this->urlapi . '/instance/connectionState/HostShopBR' . $tokenid);

                    // Verificar se a resposta é JSON
                    $res = $response->json();
                    $conexaoo = $res['instance']['state'] ?? 'false';

                    if ($conexaoo == 'open') {
                        $conexao->update(['conn' => 1]);
                        $user->update(['start' => 1]);

                        // return redirect('app-whatsapp')->with('success', 'Conexão estabelecida com sucesso.');
                        return response()->json(['success' => 'Conexão estabelecida com sucesso.']);
                    }

                    // Apagar a conexão existente
                    Http::withHeaders([
                        'apikey' => $this->apikey,
                    ])->withOptions([
                                'verify' => false,
                            ])->delete($this->urlapi . '/instance/delete/HostShopBR' . $tokenid);

                    sleep(3);

                    // Gerar um novo token
                    $tokenid = bin2hex(random_bytes(16));
                    $conexao->update(['tokenid' => $tokenid, 'conn' => 0]);

                    // Criar uma nova conexão
                    $response = Http::withHeaders([
                        'Content-Type' => 'application/json',
                        'apikey' => $this->apikey,
                    ])->withOptions([
                                'verify' => false,
                            ])->post($this->urlapi . '/instance/create', [
                                'instanceName' => 'HostShopBR' . $tokenid,
                                'token' => $tokenid,
                                'qrcode' => true,
                                'number' => $celular,
                            ]);

                    // Verificar se a resposta é JSON
                    $res = $response->json();

                    if (isset($res['instance']['status']) && $res['instance']['status'] == 'created' && isset($res['qrcode']['base64'])) {
                        $qrcodelink = $res['qrcode']['base64'];
                        $conexao->update(['qrcode' => $qrcodelink]);

                        return response()->json(['qrcode' => $conexao->qrcode]);
                    } else {
                        // Caso a resposta não esteja no formato esperado
                        Log::error('Resposta inesperada ao criar nova conexão:', $res);
                        return response()->json(['error' => 'Erro ao criar nova conexão.'], 500);
                    }
                } else {
                    // Criar uma nova conexão se não existir nenhuma
                    $tokenid = bin2hex(random_bytes(16));
                    $celular = "55" . $phone;

                    $response = Http::withHeaders([
                        'Content-Type' => 'application/json',
                        'apikey' => $this->apikey,
                    ])->withOptions([
                                'verify' => false,
                            ])->post($this->urlapi . '/instance/create', [
                                'instanceName' => 'HostShopBR' . $tokenid,
                                'token' => $tokenid,
                                'qrcode' => true,
                                'number' => $celular,
                            ]);

                    // Verificar se a resposta é JSON
                    $res = $response->json();

                    if (isset($res['instance']['status']) && $res['instance']['status'] == 'created' && isset($res['qrcode']['base64'])) {
                        $qrcodelink = $res['qrcode']['base64'];

                        // Salvar a nova conexão no banco de dados
                        Conexao::create([
                            'user_id' => $user->id,
                            'qrcode' => $qrcodelink,
                            'conn' => 0,
                            'whatsapp' => $phone,
                            'tokenid' => $tokenid,
                            'notifica' => 0,
                        ]);

                        return response()->json(['qrcode' => $qrcodelink]);
                    } else {
                        // Caso a resposta não esteja no formato esperado
                        Log::error('Resposta inesperada ao criar nova conexão:', $res);
                        return response()->json(['error' => 'Erro ao criar nova conexão.'], 500);
                    }
                }
            } catch (\Exception $e) {
                // Capturar e registrar qualquer exceção inesperada
                Log::error('Erro ao atualizar conexão:', ['exception' => $e->getMessage()]);
                return response()->json(['error' => 'Erro interno do servidor.'], 500);
            }
        }

        // Caso o parâmetro phone não esteja presente
        return response()->json(['error' => 'Parâmetro telefone não fornecido.'], 400);
    }

    public function deleteConnection($id)
    {
        $conexao = Conexao::find($id);

        if ($conexao) {
            $conexao->delete();
            return redirect()->route('app-whatsapp')->with('success', 'Item deletado com sucesso.');
        } else {
            return redirect()->route('app-whatsapp')->with('error', 'Item não encontrado.');
        }
    }



    public function sendMessage(Request $request)
    {
        // Log::info('sendMessage chamada com dados: ' . json_encode($request->all()));

        $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string',
        ]);

        $phone = $request->input('phone');
        $message = $request->input('message');
        $user = auth()->user();
        // Log::info('Usuário autenticado: ' . json_encode($user));

        $conexao = Conexao::where('user_id', $user->id)->first();
        if (!$conexao) {
            Log::error('Conexão não encontrada para o usuário: ' . $user->id);
            return response()->json(['error' => 'Conexão não encontrada.'], 404);
        }

        $tokenid = $conexao->tokenid;

        // Verificar se o número já tem o código do país
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

        // Log::info('Enviando mensagem para o numero : ' . $celular);



        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'apikey' => $this->apikey,
            ])->withOptions([
                        'verify' => false,
                    ])->post($this->urlapi . '/message/sendText/HostShopBR' . $tokenid, [
                        'number' => $celular,
                        'options' => [
                            'delay' => 1200,
                            'presence' => 'composing',
                        ],
                        'textMessage' => [
                            'text' => $message,
                        ],
                    ]);

            $result = $response->json();
            // Log::info('Resposta da API: ' . json_encode($result));

            if (isset($result['status']) && $result['status'] == 200) {
                // Log::info('Mensagem enviada com sucesso.');
                return response()->json(['success' => 'Mensagem enviada com sucesso.']);
            } else {
                // Log::error('Erro ao enviar mensagem:', $result);
                return response()->json(['error' => 'Erro ao enviar mensagem.'], 500);
            }
        } catch (\Exception $e) {
            Log::error('Exceção ao enviar mensagem: ' . $e->getMessage());
            return response()->json(['error' => 'Erro ao enviar mensagem.'], 500);
        }
    }

    public function sendMediaMessage(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'qrcode_image' => 'required|string',
        ]);

        $phone = $request->input('phone');
        $qrcode_image = $request->input('qrcode_image');
        $user = auth()->user();
        $conexao = Conexao::where('user_id', $user->id)->first();

        if (!$conexao) {
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

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'apikey' => $this->apikey,
        ])->withOptions([
                    'verify' => false,
                ])->post($this->urlapi . '/message/sendMedia/HostShopBR' . $tokenid, [
                    'number' => $celular,
                    'options' => [
                        'delay' => 1200,
                        'presence' => 'composing',
                    ],
                    'mediaMessage' => [
                        'mediatype' => 'image',
                        'caption' => 'Pague agora via pix. Leia o QRCode',
                        'media' => $qrcode_image,
                    ],
                ]);

        $result = $response->json();

        if (isset($result['status']) && $result['status'] == 200) {
            return response()->json(['success' => 'Mensagem enviada com sucesso.']);
        } else {
            Log::error('Erro ao enviar mensagem:', $result);
            return response()->json(['error' => 'Erro ao enviar mensagem.'], 500);
        }
    }
}
