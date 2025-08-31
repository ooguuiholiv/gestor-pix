@extends('layouts/layoutMaster')

@section('title', 'Templates')

@php
    $visibleColumns = getUserPreferences('templates');
    $type = 'templates';
@endphp

@section('page-script')
<script>
    var loadDataUrl = '{{ route('templates.list') }}';
    var destroyMultipleUrl = '{{ route('templates.destroy-multiple') }}';
    var label_update = '{{ __('messages.update') }}';
    var label_delete = '{{ __('messages.delete') }}';
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('assets/js/pages/templates.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM completamente carregado e analisado');

        // Função para adicionar gatilhos ao campo de conteúdo
        function adicionarGatilhos(gatilhos, conteudoField) {
            if (gatilhos.length > 0 && conteudoField) {
                console.log('Gatilhos e campo de conteúdo encontrados');
                gatilhos.forEach(function(gatilho) {
                    gatilho.addEventListener('click', function() {
                        const textoGatilho = gatilho.getAttribute('data-gatilho');
                        console.log('Gatilho clicado:', textoGatilho);
                        inserirTextoNoCursor(conteudoField, textoGatilho);
                    });
                });
            } else {
                console.log('Gatilhos ou campo de conteúdo não encontrados');
            }
        }

        // Função para inserir texto na posição do cursor
        function inserirTextoNoCursor(campo, texto) {
            const startPos = campo.selectionStart;
            const endPos = campo.selectionEnd;
            campo.value = campo.value.substring(0, startPos) + texto + campo.value.substring(endPos, campo.value.length);
            campo.selectionStart = campo.selectionEnd = startPos + texto.length;
            campo.focus();
        }

        // Selecionar os gatilhos para o modal de adição
        const gatilhosAdicao = document.querySelectorAll('#gatilhos li');
        const conteudoFieldAdicao = document.getElementById('conteudo');
        adicionarGatilhos(gatilhosAdicao, conteudoFieldAdicao);

        // Selecionar os gatilhos para cada modal de edição
        @foreach ($templates as $template)
            (function() {
                const gatilhosEdit = document.querySelectorAll('#gatilhos-edit-{{ $template->id }} li');
                const conteudoFieldEdit = document.getElementById('conteudo{{ $template->id }}');
                adicionarGatilhos(gatilhosEdit, conteudoFieldEdit);
            })();
        @endforeach
    });
</script>
@endsection

@section('content')
<h4 class="py-3 mb-4">
    <span class="text-muted fw-light">{{ config('variables.templateName', 'TemplateName') }} / </span> Templates
</h4>

<!-- Verificação de Mensagens de Sessão -->
@if (session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Botão para abrir o modal de adicionar template -->
<button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addTemplate">Adicionar Template</button>


<div class="card">
    <div class="card-body">
        <div class="table-responsive text-nowrap">
            <input type="hidden" id="data_type" value="templates">
            <input type="hidden" id="save_column_visibility" name="visible_columns">
            <div class="fixed-table-toolbar">
            </div>
            <table id="table" data-toggle="table" data-loading-template="loadingTemplate"
                data-url="{{ route('templates.list') }}" data-icons-prefix="bx" data-icons="icons"
                data-show-refresh="true" data-total-field="total" data-trim-on-search="false"
                data-data-field="rows" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true"
                data-side-pagination="server" data-show-columns="true" data-pagination="true"
                data-sort-name="id" data-sort-order="desc" data-mobile-responsive="true"
                data-query-params="queryParams" data-route-prefix="{{ Route::getCurrentRoute()->getPrefix() }}">
                <thead>
                    <tr>
                        <th data-checkbox="true"></th>
                        <th data-field="id" data-visible="{{ in_array('id', $visibleColumns) || empty($visibleColumns) ? 'true' : 'false' }}" data-sortable="true">ID</th>
                        <th data-field="nome" data-visible="{{ in_array('nome', $visibleColumns) || empty($visibleColumns) ? 'true' : 'false' }}" data-sortable="true">Nome</th>
                        <th data-field="user_name" data-visible="{{ in_array('user_name', $visibleColumns) || empty($visibleColumns) ? 'true' : 'false' }}" data-sortable="true">Dono</th>
                        <th data-field="finalidade" data-visible="{{ in_array('finalidade', $visibleColumns) || empty($visibleColumns) ? 'true' : 'false' }}" data-sortable="true">Finalidade</th>
                        <th data-field="conteudo" data-visible="{{ in_array('conteudo', $visibleColumns) || empty($visibleColumns) ? 'true' : 'false' }}" data-sortable="true">Conteúdo</th>
                        <th data-field="actions" data-visible="{{ in_array('actions', $visibleColumns) || empty($visibleColumns) ? 'true' : 'false' }}">Ações</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<!-- Modal de Adição -->
<div class="modal fade" id="addTemplate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-add-template">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <h5 class="modal-title">Adicionar Template</h5>
                <form action="{{ route('templates.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="finalidade" class="form-label">Finalidade</label>
                        <select class="form-select" id="finalidade" name="finalidade" required>
                            <option value="cobranca_manual">Cobrança Manual</option>
                            <option value="vencidos">Vencidos</option>
                            <option value="pagamentos">Pagamentos</option>
                            <option value="creditos_aprovados">Compras Creditos</option>
                            <option value="cobranca_3_dias_atras">Cobrança 3 Dias Vencidos</option>
                            <option value="cobranca_5_dias_atras">Cobrança 5 Dias Vencidos</option>
                            <option value="cobranca_hoje">Cobrança Hoje</option>
                            <option value="cobranca_3_dias_futuro">Cobrança 3 Dias Futuro</option>
                            <option value="dados_iptv">Dados IPTV</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="conteudo" class="form-label">Conteúdo</label>
                        <textarea class="form-control" id="conteudo" name="conteudo" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </form>
                <h5 class="mt-4">Gatilhos para usar no conteúdo</h5>
                <ul id="gatilhos">
                    <h6>Cliente</h6>
                    <li data-gatilho="{nome_cliente}" style="cursor: pointer;"><i class="fas fa-mouse-pointer"></i> {nome_cliente} - Nome do cliente</li>
                    <li data-gatilho="{telefone_cliente}" style="cursor: pointer;"><i class="fas fa-mouse-pointer"></i> {telefone_cliente} - Telefone do seu cliente</li>
                    <li data-gatilho="{notas}" style="cursor: pointer;"><i class="fas fa-mouse-pointer"></i> {notas} - Notas</li>
                    <li data-gatilho="{vencimento_cliente}" style="cursor: pointer;"><i class="fas fa-mouse-pointer"></i> {vencimento_cliente} - Vencimento do seu cliente</li>
                    <li data-gatilho="{plano_nome}" style="cursor: pointer;"><i class="fas fa-mouse-pointer"></i> {plano_nome} - Nome do plano que seu cliente contratou</li>
                    <li data-gatilho="{plano_valor}" style="cursor: pointer;"><i class="fas fa-mouse-pointer"></i> {plano_valor} - Valor do plano que seu cliente contratou</li>
                    <li data-gatilho="{data_atual}" style="cursor: pointer;"><i class="fas fa-mouse-pointer"></i> {data_atual} - Data atual no momento do envio</li>
                    <li data-gatilho="{plano_link}" style="cursor: pointer;"><i class="fas fa-mouse-pointer"></i> {plano_link} - Link de pagamento</li>
                    <li data-gatilho="{text_expirate}" style="cursor: pointer;"><i class="fas fa-mouse-pointer"></i> {text_expirate} - Mensagem de expiração: expirou há x dias, expira em x dias, expira hoje</li>
                    <li data-gatilho="{saudacao}" style="cursor: pointer;"><i class="fas fa-mouse-pointer"></i> {saudacao} - Mensagem de saudação: Bom dia!, Boa tarde!, Boa noite! (automático baseado no horário)</li>
                    <h6>Empresa</h6>
                    <li data-gatilho="{whatsapp_empresa}" style="cursor: pointer;"><i class="fas fa-building"></i> {whatsapp_empresa} - WhatsApp da Empresa</li>
                    <li data-gatilho="{status_pagamento}" style="cursor: pointer;"><i class="fas fa-mouse-pointer"></i> {status_pagamento} - Status do pagamento</li>
                    <li data-gatilho="{nome_empresa}" style="cursor: pointer;"><i class="fas fa-building"></i> {nome_empresa} - Nome da empresa</li>
                    <li data-gatilho="{nome_dono}" style="cursor: pointer;"><i class="fas fa-building"></i> {nome_dono} - Nome do Dono Da Empresa</li>
                    <li data-gatilho="{creditos}" style="cursor: pointer;"><i class="fas fa-building"></i> {creditos} - Creditos</li>
                    <h6>Dados de Acesso</h6>
                    <li data-gatilho="{password}" style="cursor: pointer;"><i class="fas fa-building"></i> {password} - Senha do Sistema Somente para clientes</li>
                    <li data-gatilho="{iptv_nome}" style="cursor: pointer;"><i class="fas fa-building"></i> {iptv_nome} - Usuário IPTV Somente para clientes</li>
                    <li data-gatilho="{iptv_senha}" style="cursor: pointer;"><i class="fas fa-building"></i> {iptv_senha} - Senha IPTV Somente para clientes</li>
                    <li data-gatilho="{login_url}" style="cursor: pointer;"><i class="fas fa-building"></i> {login_url} - URL de Login Somente para clientes</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Edição -->
@foreach ($templates as $template)
    <div class="modal fade" id="editTemplate{{ $template->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple modal-edit-template">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <h5 class="modal-title">Editar Template</h5>
                    <form action="{{ route('templates.update', $template->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="nome{{ $template->id }}" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="nome{{ $template->id }}" name="nome" value="{{ $template->nome }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="finalidade{{ $template->id }}" class="form-label">Finalidade</label>
                            <select class="form-select" id="finalidade{{ $template->id }}" name="finalidade" required>
                                <option value="cobranca_manual" {{ $template->finalidade == 'cobranca_manual' ? 'selected' : '' }}>Cobrança Manual</option>
                                <option value="vencidos" {{ $template->finalidade == 'vencidos' ? 'selected' : '' }}>Vencidos</option>
                                <option value="pagamentos" {{ $template->finalidade == 'pagamentos' ? 'selected' : '' }}>Pagamentos</option>
                                <option value="creditos_aprovados" {{ $template->finalidade == 'creditos_aprovados' ? 'selected' : '' }}>Compras Creditos</option>
                                <option value="cobranca_3_dias_atras" {{ $template->finalidade == 'cobranca_3_dias_atras' ? 'selected' : '' }}>Cobrança 3 Dias vencidos</option>
                                <option value="cobranca_5_dias_atras" {{ $template->finalidade == 'cobranca_5_dias_atras' ? 'selected' : '' }}>Cobrança 5 Dias vencidos</option>
                                <option value="cobranca_hoje" {{ $template->finalidade == 'cobranca_hoje' ? 'selected' : '' }}>Cobrança Hoje</option>
                                <option value="cobranca_3_dias_futuro" {{ $template->finalidade == 'cobranca_3_dias_futuro' ? 'selected' : '' }}>Cobrança 3 Dias Futuro</option>
                                <option value="dados_iptv" {{ $template->finalidade == 'dados_iptv' ? 'selected' : '' }}>Dados do Clientes IPTV</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="conteudo{{ $template->id }}" class="form-label">Conteúdo</label>
                            <textarea class="form-control" id="conteudo{{ $template->id }}" name="conteudo" required>{{ $template->conteudo }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </form>
                    <h5 class="mt-4">Gatilhos para usar no conteúdo</h5>
                    <ul id="gatilhos-edit-{{ $template->id }}">
                        <h6>Cliente</h6>
                        <li data-gatilho="{nome_cliente}" style="cursor: pointer;"><i class="fas fa-mouse-pointer"></i> {nome_cliente} - Nome do cliente</li>
                        <li data-gatilho="{telefone_cliente}" style="cursor: pointer;"><i class="fas fa-mouse-pointer"></i> {telefone_cliente} - Telefone do seu cliente</li>
                        <li data-gatilho="{notas}" style="cursor: pointer;"><i class="fas fa-mouse-pointer"></i> {notas} - Notas</li>
                        <li data-gatilho="{vencimento_cliente}" style="cursor: pointer;"><i class="fas fa-mouse-pointer"></i> {vencimento_cliente} - Vencimento do seu cliente</li>
                        <li data-gatilho="{plano_nome}" style="cursor: pointer;"><i class="fas fa-mouse-pointer"></i> {plano_nome} - Nome do plano que seu cliente contratou</li>
                        <li data-gatilho="{plano_valor}" style="cursor: pointer;"><i class="fas fa-mouse-pointer"></i> {plano_valor} - Valor do plano que seu cliente contratou</li>
                        <li data-gatilho="{data_atual}" style="cursor: pointer;"><i class="fas fa-mouse-pointer"></i> {data_atual} - Data atual no momento do envio</li>
                        <li data-gatilho="{plano_link}" style="cursor: pointer;"><i class="fas fa-mouse-pointer"></i> {plano_link} - Link de pagamento</li>
                        <li data-gatilho="{text_expirate}" style="cursor: pointer;"><i class="fas fa-mouse-pointer"></i> {text_expirate} - Mensagem de expiração: expirou há x dias, expira em x dias, expira hoje</li>
                        <li data-gatilho="{saudacao}" style="cursor: pointer;"><i class="fas fa-mouse-pointer"></i> {saudacao} - Mensagem de saudação: Bom dia!, Boa tarde!, Boa noite! (automático baseado no horário)</li>
                        <h6>Empresa</h6>
                        <li data-gatilho="{whatsapp_empresa}" style="cursor: pointer;"><i class="fas fa-building"></i> {whatsapp_empresa} - WhatsApp da Empresa</li>
                        <li data-gatilho="{status_pagamento}" style="cursor: pointer;"><i class="fas fa-mouse-pointer"></i> {status_pagamento} - Status do pagamento</li>
                        <li data-gatilho="{nome_empresa}" style="cursor: pointer;"><i class="fas fa-building"></i> {nome_empresa} - Nome da empresa</li>
                        <li data-gatilho="{nome_dono}" style="cursor: pointer;"><i class="fas fa-building"></i> {nome_dono} - Nome do Dono Da Empresa</li>
                        <li data-gatilho="{creditos}" style="cursor: pointer;"><i class="fas fa-building"></i> {creditos} - Creditos</li>
                        <h6>Dados de Acesso</h6>
                        <li data-gatilho="{password}" style="cursor: pointer;"><i class="fas fa-building"></i> {password} - Senha do Sistema Somente para clientes</li>
                        <li data-gatilho="{iptv_nome}" style="cursor: pointer;"><i class="fas fa-building"></i> {iptv_nome} - Usuário IPTV Somente para clientes</li>
                        <li data-gatilho="{iptv_senha}" style="cursor: pointer;"><i class="fas fa-building"></i> {iptv_senha} - Senha IPTV Somente para clientes</li>
                        <li data-gatilho="{login_url}" style="cursor: pointer;"><i class="fas fa-building"></i> {login_url} - URL de Login Somente para clientes</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endforeach

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
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Fechar
                </button>
                <button type="submit" class="btn btn-danger" id="confirmDeleteSelections">Sim</button>
            </div>
        </div>
    </div>
</div>

@endsection