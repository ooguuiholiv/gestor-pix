@extends('layouts/layoutMaster')

@section('title', 'Gerenciar Clientes')

@php
    $visibleColumns = getUserPreferences('clientes');
    $type = 'clientes';
@endphp

@section('page-script')
<script>
    var loadDataUrl = '{{ route('app-ecommerce-customer-list') }}';
    var label_update = '{{ __('messages.update') }}';
    var label_delete = '{{ __('messages.delete') }}';
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('assets/js/pages/clientes.js') }}"></script>


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

        <!-- Card colapsável para a URL de login -->
        <div class="accordion" id="loginUrlAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                        URL de Login para Clientes
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
                    data-bs-parent="#loginUrlAccordion">
                    <div class="accordion-body">
                        <p>Compartilhe esta URL com seus clientes para que eles possam acessar a área de login.</p>
                        <div class="mb-3">
                            <label>URL de Login</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="loginUrl" value="{{ $loginUrl }}" readonly>
                                <button class="btn btn-outline-secondary" type="button" id="copyButton">Copiar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

                   <div class="d-flex justify-content-end">
                @if ($user->role_id == 1)
                    <span class="badge bg-success mb-3">Créditos: ∞</span>
                @else
                    <span class="badge bg-success mb-3">Limite do Usuário: {{ $user->limite }}</span>
                @endif
            </div>

        <!-- Botão para abrir o modal de adicionar cliente -->
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addClient">Adicionar Cliente</button>

        <!-- Botão para importar clientes -->
        <button class="btn btn-secondary mb-3" data-bs-toggle="modal" data-bs-target="#importClients">Importar Clientes</button>

        <!-- Botão para exportar clientes -->
        <a href="#" class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#exportModal">Exportar Clientes</a>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <select class="form-select" id="client_status_filter" aria-label="Default select example">
                            <option value="">Selecionar vencimento</option>
                            <option value="todos">Todos</option>
                            <option value="vencido">Vencido</option>
                            <option value="hoje">Vence hoje</option>
                            <option value="ainda_vai_vencer">Ainda vai vencer</option>        
                        </select>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <input type="hidden" id="data_type" value="clientes">
                        <input type="hidden" id="save_column_visibility" name="visible_columns">
                        <div class="fixed-table-toolbar">
                        </div>
                        <table id="table" data-toggle="table" data-loading-template="loadingTemplate"
                            data-url="{{ route('app-ecommerce-customer-list') }}" data-icons-prefix="bx" data-icons="icons"
                            data-show-refresh="true" data-total-field="total" data-trim-on-search="false"
                            data-data-field="rows" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true"
                            data-side-pagination="server" data-show-columns="true" data-pagination="true"
                            data-sort-name="id" data-sort-order="desc" data-mobile-responsive="true"
                            data-query-params="queryParams"
                            data-route-prefix="{{ Route::getCurrentRoute()->getPrefix() }}">
                
                            <thead>
                                <tr>
                                    <th data-checkbox="true"></th>
                                    <th data-sortable="true" data-field="id">ID</th>
                                    <th data-field="nome" data-visible="{{ in_array('nome', $visibleColumns) || empty($visibleColumns) ? 'true' : 'false' }}" data-sortable="true">Cliente</th>
                                    <th data-field="iptv_nome" data-visible="{{ in_array('iptv_nome', $visibleColumns) || empty($visibleColumns) ? 'true' : 'false' }}" data-sortable="true">Usuário IPTV</th>
                                    <th data-field="whatsapp" data-visible="{{ in_array('whatsapp', $visibleColumns) || empty($visibleColumns) ? 'true' : 'false' }}" data-sortable="true">WhatsApp</th>
                                    <th data-field="vencimento" data-visible="{{ in_array('vencimento', $visibleColumns) || empty($visibleColumns) ? 'true' : 'false' }}" data-sortable="true">Vencimento</th>
                                    <th data-field="servidor" data-visible="{{ in_array('servidor', $visibleColumns) || empty($visibleColumns) ? 'true' : 'false' }}" data-sortable="true">Servidor</th>
                                    <th data-field="mac" data-visible="{{ in_array('mac', $visibleColumns) || empty($visibleColumns) ? 'true' : 'false' }}" data-sortable="true">MAC</th>
                                    <th data-field="notificacoes" data-visible="{{ in_array('notificacoes', $visibleColumns) || empty($visibleColumns) ? 'true' : 'false' }}" data-sortable="true">Notificações</th>
                                    <th data-field="plano" data-visible="{{ in_array('plano', $visibleColumns) || empty($visibleColumns) ? 'true' : 'false' }}" data-sortable="true">Plano</th>
                                    <th data-field="valor" data-visible="{{ in_array('valor', $visibleColumns) || empty($visibleColumns) ? 'true' : 'false' }}" data-sortable="true">Valor</th>
                                    <th data-field="numero_de_telas" data-visible="{{ in_array('numero_de_telas', $visibleColumns) || empty($visibleColumns) ? 'true' : 'false' }}" data-sortable="true">Número de Telas</th>
                                    <th data-field="notas" data-visible="{{ in_array('notas', $visibleColumns) || empty($visibleColumns) ? 'true' : 'false' }}" data-sortable="true">Notas</th>
                                    <th data-field="actions" data-visible="{{ in_array('actions', $visibleColumns) || empty($visibleColumns) ? 'true' : 'false' }}">Ações</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para adicionar novo cliente -->
    <div class="modal fade" id="addClient" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple modal-add-client">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-4">
                        <h3 class="mb-2">Adicionar Novo Cliente</h3>
                        <p class="text-muted">Preencha os detalhes do novo cliente.</p>
                    </div>
                    <form id="addClientForm" class="row g-3" action="{{ route('app-ecommerce-customer-store') }}" method="POST">
                        @csrf
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="addClientNome">Nome Sistema</label>
                            <input type="text" id="addClientNome" name="nome" class="form-control" placeholder="Nome" required />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="addClientPassword">Senha Sistema</label>
                            <div class="input-group">
                                <input type="password" id="addClientPassword" name="password" class="form-control" placeholder="Senha" required />
                                <button type="button" class="btn btn-outline-secondary" onclick="generatePassword('addClientPassword')">
                                    <i class="fas fa-random"></i>
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePasswordVisibility('addClientPassword')">
                                    <i class="fas fa-eye" id="togglePasswordIcon"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="addClientIPTVNome">Usuário IPTV</label>
                            <input type="text" id="addClientIPTVNome" name="iptv_nome" class="form-control" placeholder="Opcional" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="addClientIPTVSenha">Senha IPTV</label>
                            <div class="input-group">
                                <input type="text" id="addClientIPTVSenha" name="iptv_senha" class="form-control" placeholder="Opcional" />
                                <button type="button" class="btn btn-outline-secondary" onclick="generatePassword('addClientIPTVSenha')">
                                    <i class="fas fa-random"></i> 
                                </button>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="addClientWhatsApp">WhatsApp</label>
                            <input type="text" id="addClientWhatsApp" name="whatsapp" class="form-control" placeholder="WhatsApp" required />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="addClientVencimento">Vencimento</label>
                            <input type="date" id="addClientVencimento" name="vencimento" class="form-control" placeholder="Vencimento" required />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="addClientServidor">Servidor</label>
                            <select id="addClientServidor" name="servidor_id" class="form-select" required>
                                <!-- Servidores serão carregados via AJAX -->
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="addClientMac">MAC</label>
                            <input type="text" id="addClientMac" name="mac" class="form-control" placeholder="MAC" />
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="addClientNotificacoes">Notificações</label>
                            <select id="addClientNotificacoes" name="notificacoes" class="form-select" required>
                                <option value="1">Sim</option>
                                <option value="0">Não</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="addClientPlano">Plano</label>
                            <select id="addClientPlano" name="plano_id" class="form-select" required>
                                <!-- Planos serão carregados via AJAX -->
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label" for="addClientNumeroDeTelas">Número de Telas</label>
                            <input type="number" id="addClientNumeroDeTelas" name="numero_de_telas" class="form-control" placeholder="Número de Telas" required />
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="addClientNotas">Notas</label>
                            <textarea id="addClientNotas" name="notas" class="form-control" placeholder="Notas"></textarea>
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1">Adicionar</button>
                            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para importar clientes -->
    <div class="modal fade" id="importClients" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple modal-import-clients">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-4">
                        <h3 class="mb-2">Importar Clientes</h3>
                        <p class="text-muted">Faça upload de um arquivo CSV para importar clientes.</p>
                    </div>
                    <form id="importClientsForm" class="row g-3" action="{{ route('app-ecommerce-customer-import') }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="col-12">
                            <label class="form-label" for="importClientsFile">Arquivo CSV</label>
                            <input type="file" id="importClientsFile" name="file" class="form-control"
                                accept=".csv" required />
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1">Importar</button>
                            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                                aria-label="Close">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


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

    <!-- Modal para exportar clientes -->
    <div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple modal-export-clients">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-4">
                        <h3 class="mb-2">Exportar Clientes</h3>
                        <p class="text-muted">Escolha o formato para exportar os clientes.</p>
                    </div>
                    <form id="exportClientsForm" class="row g-3" action="{{ route('app-ecommerce-customer-export') }}" method="GET">
                        <div class="col-12">
                            <label class="form-label" for="exportFormat">Formato de Exportação</label>
                            <select id="exportFormat" name="format" class="form-select" required>
                                <option value="csv">CSV</option>
                                <option value="excel">Excel</option>
                                <option value="pdf">PDF</option>
                            </select>
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1">Exportar</button>
                            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


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
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Fechar
                    </button>
                    <button type="submit" class="btn btn-danger" id="confirmDeleteSelections">Sim</button>
                </div>
            </div>
        </div>
    </div>
@endsection