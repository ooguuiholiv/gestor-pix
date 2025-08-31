@extends('layouts/layoutMaster')

@section('title', 'Revendedores')

@php
    $visibleColumns = getUserPreferences('revendedores');
    $type = 'revendedores';
@endphp

@section('page-script')
    <script>
        var loadDataUrl = '{{ route('revendedores.list') }}';
        var destroyMultipleUrl = '{{ route('revendedores.destroy_multiple') }}';
        var label_update = '{{ __('messages.update') }}';
        var label_delete = '{{ __('messages.delete') }}';
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('assets/js/pages/revendedores.js') }}"></script>
    <script src="{{ asset('assets/js/pages-pricing.js') }}"></script>
<script>
    function openPixPaymentModal(creditoId) {
        // Armazena o ID do crédito selecionado
        document.getElementById('creditoIdUnique').value = creditoId;
        // Abre o modal de seleção de pagamento
        $('#pixPaymentModalUnique').modal('show');
    }

    document.getElementById('pixPaymentFormUnique').addEventListener('submit', async function(event) {
        event.preventDefault();
        // Não fechar o modal de seleção de pagamento
        // $('#pixPaymentModalUnique').modal('hide');
        // Envia o formulário para processar o pagamento
        const creditoId = document.getElementById('creditoIdUnique').value;
        const userId = document.getElementById('userIdUnique').value;
        const form = document.getElementById('creditoForm' + creditoId);
        const formData = new FormData(form);
        formData.append('user_id', userId);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                // Exibir a seção do PIX
                document.getElementById('pix-section-unique').classList.remove('d-none');
                document.getElementById('pix-qrcode-unique').innerText = data.payload_pixx;
                var pixQrcodeBase = document.getElementById('pix-qrcodeBase-unique');
                if (pixQrcodeBase) {
                    pixQrcodeBase.src = 'data:image/png;base64,' + data.qr_code_base644;
                }
                document.getElementById('copy-pix-code-unique').classList.remove('d-none');

                // Verificar o status do pagamento periodicamente
                const paymentId = data.payment_id;
                const intervalId = setInterval(async () => {
                    const status = await checkPaymentStatus(paymentId);
                    if (status === 'approved') {
                        clearInterval(intervalId);
                        document.getElementById('paymentSuccessMessageUnique').classList.remove(
                            'd-none');
                        document.getElementById('paymentSuccessMessageUnique').innerText =
                            'Pagamento aprovado com sucesso.';
                        $('#pixPaymentModalUnique').modal('hide');
                    } else if (status === 'cancelled') {
                        clearInterval(intervalId);
                        alert('Pagamento cancelado.');
                    }
                }, 5000); // Verificar a cada 5 segundos
            } else {
                alert('Erro ao processar o pagamento: ' + data.message);
            }
        } catch (error) {
            console.error('Erro ao processar o pagamento:', error);
            alert('Erro ao processar o pagamento: ' + error.message);
        }
    });

    async function checkPaymentStatus(paymentId) {
        try {
            const response = await fetch(`/api/payment-status/${paymentId}`);
            const data = await response.json();
            if (data.success) {
                return data.status;
            } else {
                console.error('Erro ao verificar status do pagamento:', data.message);
                return null;
            }
        } catch (error) {
            console.error('Erro ao verificar status do pagamento:', error);
            return null;
        }
    }

    // Lógica para copiar o código PIX
    document.getElementById('copy-pix-code-unique').addEventListener('click', function() {
        var pixCodeElement = document.getElementById('pix-qrcode-unique');
        var range = document.createRange();
        range.selectNode(pixCodeElement);
        window.getSelection().removeAllRanges();
        window.getSelection().addRange(range);
        try {
            document.execCommand('copy');
            alert('Código PIX copiado para a área de transferência!');
        } catch (err) {
            alert('Erro ao copiar o código PIX.');
        }
        window.getSelection().removeAllRanges();
    });
</script>
<script>
    document.getElementById('trial_ends_at').addEventListener('change', function() {
        var meses = this.value;
        updateCreditosNecessarios(meses);
    });

    // Inicializar a informação de créditos ao carregar a página
    document.addEventListener('DOMContentLoaded', function() {
        var meses = document.getElementById('trial_ends_at').value;
        updateCreditosNecessarios(meses);
    });

    function updateCreditosNecessarios(meses) {
        document.getElementById('creditos_necessarios').value = meses;
        document.getElementById('creditoInfo').innerText = 'Créditos necessários para este plano: ' + meses;
    }

    function openEditModal(cliente) {
        document.getElementById('edit_name').value = cliente.name;
        document.getElementById('edit_whatsapp').value = cliente.whatsapp;
        document.getElementById('edit_password').value =
        ''; // Deixe o campo de senha vazio para não sobrescrever senhas existentes
        document.getElementById('edit_plano_id').value = cliente.plano_id;
        document.getElementById('edit_trial_ends_at').value = cliente.trial_ends_at ? cliente.trial_ends_at : '';
        document.getElementById('edit_creditos_necessarios').value = cliente.trial_ends_at ? cliente.trial_ends_at : '';
        document.getElementById('edit_plano_limite').value = cliente.plano ? cliente.plano.limite : '';
        updateEditCreditosNecessarios(cliente.trial_ends_at ? cliente.trial_ends_at : '');
    }

    function updateEditCreditosNecessarios(meses) {
        document.getElementById('edit_creditos_necessarios').value = meses;
        document.getElementById('edit_creditoInfo').innerText = 'Créditos necessários para este plano: ' + meses;
    }

    // Atualizar créditos necessários ao mudar a duração do período de teste no modal de edição
    document.getElementById('edit_trial_ends_at').addEventListener('change', function() {
        var meses = this.value;
        updateEditCreditosNecessarios(meses);
    });
</script>
@endsection



@section('content')
    <div class="container-fluid">
       

        <!-- Verificação de Mensagens de Sessão -->
        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- mensagens para erros -->
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- mensagens para sucesso -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Exibição de Erros de Validação -->
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Exibição dos Créditos Atuais -->
        <div class="alert alert-info" role="alert">
            Créditos Atuais: <strong>{{ Auth::user()->creditos }}</strong>
        </div>

        <h4 class="py-3 mb-2">
            <span class="text-muted fw-light">{{ config('variables.templateName', 'TemplateName') }} / </span> Revendedores
        </h4>

        <!-- Botão para abrir o modal de adicionar revendedor -->
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#createUserModal"><i
                class='bx bx-plus'></i>Criar Novo
                Usuário</button>


                 <!-- Botão para abrir o modal de compra de créditos -->
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#creditosModalUnique">Comprar
            Créditos</button>

        <!-- Botão para abrir o modal de criar usuário -->
        {{-- <button class="btn btn-secondary mb-3" data-bs-toggle="modal" data-bs-target="#addRevendedor">Criar Novo
            Usuário</button> --}}

        <!-- Tabela de Revendedores -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <input type="hidden" id="data_type" value="revendedores">
                    <input type="hidden" id="save_column_visibility" name="visible_columns">
                    <div class="fixed-table-toolbar"></div>
                    <table id="table" data-toggle="table" data-loading-template="loadingTemplate"
                        data-url="{{ route('revendedores.list') }}" data-icons-prefix="bx" data-icons="icons"
                        data-show-refresh="true" data-total-field="total" data-trim-on-search="false" data-data-field="rows"
                        data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true" data-side-pagination="server"
                        data-show-columns="true" data-pagination="true" data-sort-name="id" data-sort-order="desc"
                        data-mobile-responsive="true" data-query-params="queryParams"
                        data-route-prefix="{{ Route::getCurrentRoute()->getPrefix() }}">
                        <thead>
                            <tr>
                                <th data-checkbox="true"></th>
                                <th data-sortable="true" data-field="id">ID</th>
                                <th data-field="name"
                                    data-visible="{{ in_array('name', $visibleColumns) || empty($visibleColumns) ? 'true' : 'false' }}"
                                    data-sortable="true">Nome</th>
                                <th data-field="whatsapp"
                                    data-visible="{{ in_array('whatsapp', $visibleColumns) || empty($visibleColumns) ? 'true' : 'false' }}"
                                    data-sortable="true">WhatsApp</th>
                                <th data-field="profile_photo_url" data-formatter="profileFormatter"
                                    data-visible="{{ in_array('profile_photo_url', $visibleColumns) || empty($visibleColumns) ? 'true' : 'false' }}"
                                    data-sortable="true">Perfil</th>
                                <th data-field="status"
                                    data-visible="{{ in_array('status', $visibleColumns) || empty($visibleColumns) ? 'true' : 'false' }}"
                                    data-sortable="true">Status</th>
                                <th data-field="trial_ends_at"
                                    data-visible="{{ in_array('trial_ends_at', $visibleColumns) || empty($visibleColumns) ? 'true' : 'false' }}"
                                    data-sortable="true">Vencimento</th>
                                <th data-field="limite"
                                    data-visible="{{ in_array('limite', $visibleColumns) || empty($visibleColumns) ? 'true' : 'false' }}"
                                    data-sortable="true">Limite</th>
                                <th data-field="actions"
                                    data-visible="{{ in_array('actions', $visibleColumns) || empty($visibleColumns) ? 'true' : 'false' }}">
                                    Ações</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <script>
            function profileFormatter(value, row, index) {
                return `<img src="${value}" alt="Foto de Perfil" class="h-auto rounded-circle" style="width: 50px; height: 50px;">`;
            }

            function statusFormatter(value, row, index) {
                return value;
            }

            function trialEndsAtFormatter(value, row, index) {
                return value;
            }

            function limiteFormatter(value, row, index) {
                return value;
            }
        </script>

        

           <!-- Modal para criar novo usuário -->
    <div class="modal fade" id="createUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple modal-create-user">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-4">
                        <h3 class="mb-2">Criar Novo Usuário</h3>
                        <p class="text-muted">Preencha os detalhes do novo usuário.</p>
                    </div>
                    <form id="createUserForm" class="row g-3" action="{{ route('revendedores.store') }}" method="POST">
                        @csrf
                        <div class="col-12">
                            <label class="form-label" for="name">Nome</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="whatsapp">WhatsApp</label>
                            <input type="text" class="form-control" id="whatsapp" name="whatsapp" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="password">Senha</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="plano_id">Plano</label>
                            <select id="plano_id" name="plano_id" class="form-control" required>
                                @foreach ($planos_revenda as $plano)
                                    <option value="{{ $plano->id }}" data-limite="{{ $plano->limite }}">
                                        {{ $plano->nome }} - R$
                                        {{ number_format((float) $plano->preco, 2, ',', '.') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="trial_ends_at">Duração do Período de Teste (em meses)</label>
                            <select id="trial_ends_at" name="trial_ends_at" class="form-control" required>
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}">{{ $i }} mês{{ $i > 1 ? 'es' : '' }}
                                    </option>
                                @endfor
                            </select>
                            <small id="creditoInfo" class="form-text text-muted mt-2"></small>
                        </div>
                        <input type="hidden" id="creditos_necessarios" name="creditos_necessarios" value="1">
                        <input type="hidden" id="plano_limite" name="plano_limite" value="">
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1">Criar Usuário</button>
                            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                                aria-label="Close">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
        {{-- 
        <!-- Modal para Editar Usuário -->
        <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-simple modal-edit-user">
                <div class="modal-content p-3 p-md-5">
                    <div class="modal-body">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <div class="text-center mb-4">
                            <h3 class="mb-2">Editar Usuário</h3>
                            <p class="text-muted">Atualize os detalhes do usuário.</p>
                        </div>
                        @if (isset($cliente))
                            <form id="editUserForm" class="row g-3" action="{{ route('revendedores.update', $cliente->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="col-12">
                                    <label class="form-label" for="edit_name">Nome</label>
                                    <input type="text" class="form-control" id="edit_name" name="name" value="{{ $user->name }}" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="edit_whatsapp">WhatsApp</label>
                                    <input type="text" class="form-control" id="edit_whatsapp" name="whatsapp" value="{{ $user->whatsapp }}" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="edit_password">Senha</label>
                                    <input type="password" class="form-control" id="edit_password" name="password">
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="edit_plano_id">Plano</label>
                                    <select id="edit_plano_id" name="plano_id" class="form-control" required>
                                        @foreach ($planos_revenda as $plano)
                                            <option value="{{ $plano->id }}" data-limite="{{ $plano->limite }}" {{ $user->plano_id == $plano->id ? 'selected' : '' }}>
                                                {{ $plano->nome }} - R$ {{ number_format((float) $plano->preco, 2, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label" id="trial_ends_at" for="edit_trial_ends_at">Duração do Período de Teste (em meses)</label>
                                    <select id="trial_ends_at" name="trial_ends_at" class="form-control" required>
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}">{{ $i }} mês{{ $i > 1 ? 'es' : '' }}</option>
                                        @endfor
                                    </select>
                                    <small id="edit_creditoInfo" class="form-text text-muted mt-2"></small>
                                </div>
                                <input type="hidden" id="edit_creditos_necessarios" name="creditos_necessarios" value="1">
                                <input type="hidden" id="edit_plano_limite" name="plano_limite" value="">
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-primary me-sm-3 me-1">Salvar Alterações</button>
                                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancelar</button>
                                </div>
                            </form>
                        @else
                            <p class="text-center text-muted">Nenhum cliente selecionado para edição.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div> --}}

        <!-- Modal de Confirmação para Salvar Visibilidade das Colunas -->
        <div class="modal fade" id="confirmSaveColumnVisibility" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Salvar Visibilidade das Colunas</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Tem certeza de que deseja salvar as preferências de visibilidade das colunas?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="confirm">Salvar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Confirmação para Excluir Selecionados -->
        <div class="modal fade" id="confirmDeleteSelectedModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel2">Aviso!</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Tem certeza de que deseja excluir o(s) registro(s) selecionado(s)?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-danger" id="confirmDeleteSelections">Sim</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


     <!-- Pricing Modal -->
     <div class="modal fade" id="creditosModalUnique" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-simple modal-pricing">
            <div class="modal-content p-2 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <!-- Pricing Plans -->
                    <div class="pb-sm-5 pb-2 rounded-top">
                        <h2 class="text-center mb-2">Planos de Créditos</h2>
                        <p class="text-center">Escolha um plano de créditos para continuar gerenciando seus clientes IPTV.
                        </p>
                        <div class="row mx-0 gy-3">
                            @foreach ($revendas_creditos as $credito)
                                <div class="col-xl mb-md-0 mb-4">
                                    <div class="card border border rounded shadow-none">
                                        <div class="card-body">
                                            <div class="my-3 pt-2 text-center">
                                                <img src="{{ asset('assets/img/illustrations/' . ($loop->first ? 'page-pricing-basic.png' : ($loop->iteration == 2 ? 'page-pricing-standard.png' : 'page-pricing-enterprise.png'))) }}"
                                                    alt="Image" height="140">
                                            </div>
                                            <h3 class="card-title text-center text-capitalize mb-1">{{ $credito->nome }}
                                            </h3>
                                            <p class="text-center">Créditos: {{ $credito->creditos }}</p>
                                            <p class="text-center">Preço Por Créditos: R$ {{ $credito->preco }}</p>
                                            <div class="text-center h-px-100">
                                                <div class="d-flex justify-content-center">
                                                    <sup class="h6 pricing-currency mt-3 mb-0 me-1 text-primary">R$</sup>
                                                    <h1 class="display-4 mb-0 text-primary">{{ $credito->total }}</h1>
                                                    <sub
                                                        class="h6 pricing-duration mt-auto mb-2 text-muted fw-normal">/Reais</sub>
                                                </div>
                                            </div>
                                            <form id="creditoForm{{ $credito->id }}"
                                                action="{{ route('process-payment-creditos') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="credito_id" value="{{ $credito->id }}">
                                                <button type="button" class="btn btn-label-success d-grid w-100 mt-3"
                                                    onclick="openPixPaymentModal({{ $credito->id }})">Comprar</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <!--/ Pricing Plans -->
                </div>
            </div>
        </div>
    </div>
    <!--/ Pricing Modal -->


    <!-- Modal para Selecionar Opção de Pagamento PIX -->
    <div class="modal fade" id="pixPaymentModalUnique" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple modal-add-new-address">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-4">
                        <h3 class="address-title mb-2">Pagamento com PIX</h3>
                        <p class="text-muted address-subtitle">Escolha o método de pagamento</p>
                    </div>
                    <form id="pixPaymentFormUnique" class="row g-3" onsubmit="return false">
                        @if (Auth::check())
                            <input type="hidden" id="userIdUnique" value="{{ Auth::user()->id }}">
                        @endif
                        <input type="hidden" id="creditoIdUnique" value="">
                        <div class="col-12">
                            <div class="form-check custom-option custom-option-icon">
                                <input class="form-check-input" type="radio" name="paymentMethod"
                                    id="pixPaymentUnique" value="pix" checked>
                                <label class="form-check-label" for="pixPaymentUnique">
                                    <span class="option-icon"><i class="bx bxs-credit-card"></i></span>
                                    <span class="option-title">PIX</span>
                                </label>
                            </div>
                        </div>
                        <div id="pix-section-unique" class="d-none">
                            <div class="alert alert-info" role="alert">
                                <p id="pix-code-unique" class="mb-2"></p>
                                <img id="pix-qrcodeBase-unique" src="" alt="QR Code PIX"
                                    class="img-fluid d-block mx-auto" style="max-width: 200px;" />
                                <br>
                                <pre id="pix-qrcode-unique" class="text-break"
                                    style="word-wrap: break-word; white-space: pre-wrap; background-color: #f8f9fa; padding: 10px; border-radius: 5px;"></pre>
                                <button type="button" id="copy-pix-code-unique"
                                    class="btn btn-primary d-block mx-auto">Copiar Código
                                    PIX</button>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary w-100">Pagar</button>
                        </div>
                    </form>
                    <div id="paymentSuccessMessageUnique" class="alert alert-success d-none mt-3" role="alert">
                        Pagamento realizado com sucesso.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
