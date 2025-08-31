@extends('layouts/layoutMaster')

@section('title', 'Conexões do WhatsApp')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/animate-css/animate.css')}}">
@endsection

@section('page-script')
<script src="{{asset('assets/js/ui-modals.js')}}"></script>
<script>
    function validateForm() {
        var phoneInput = document.getElementById('phone');
        var phoneError = document.getElementById('phoneError');

        if (phoneInput.value.trim() === '') {
            phoneError.style.display = 'block';
            return false;
        } else {
            phoneError.style.display = 'none';
            return true;
        }
    }

    function confirmDelete(id) {
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        document.getElementById('deleteForm').action = '/delete-connection/' + id;
        deleteModal.show();
    }
</script>
@endsection

@section('content')
<h4 class="py-3 mb-4">
  <span class="text-muted fw-light">WhatsApp /</span> Conexões
</h4>

<div class="container">
 <!-- Exibir mensagens de sucesso ou erro -->
 @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div id="alert-container"></div>
    @php
        $user_id = Auth::user()->id;
        $conexao = $conexoes->where('user_id', $user_id)->first();
    @endphp
    <!-- Formulário para criar nova conexão -->
    @if(!$conexao || $conexao->conn != 1)
    <!-- Formulário para criar nova conexão -->
    <form id="connectionForm" action="{{ route('create-connection') }}" method="GET" onsubmit="return validateForm()">
        <div class="mb-3">
            <label for="phone" class="form-label">Número do WhatsApp</label>
            <input type="text" class="form-control" id="phone" name="phone" placeholder="Digite o número do WhatsApp">
            <div id="phoneError" class="text-danger mt-2" style="display: none;">O número do WhatsApp é obrigatório.</div>
        </div>
        <button type="submit" class="btn btn-primary mb-4">Criar Conexão</button>
    </form>
@endif

    <!-- <h2 class="mt-5">Conexões Existentes</h2> -->
    <div class="card">
      <!-- <h5 class="card-header">Conexões do WhatsApp</h5> -->
      <div class="table-responsive text-nowrap">
        <table class="table">
          <thead>
            <tr>
              <th>ID</th>
              <th>ID do Usuário</th>
              <th>Número do WhatsApp</th>
              <th>Status</th>
              <!-- <th>Data de Cadastro</th> -->
              <th>Ações</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            @foreach($conexoes as $conexao)
            <tr>
              <td>{{ $conexao->id }}</td>
              <td>{{ $conexao->user_id }}</td>
              <td>{{ $conexao->whatsapp }}</td>
              <td><span class="badge bg-label-{{ $conexao->conn ? 'primary' : 'secondary' }}">{{ $conexao->conn ? 'Conectado' : 'Desconectado' }}</span></td>
              <!-- <td>{{ $conexao->data_cadastro }}</td> -->
              <td>
                <div class="dropdown">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                        <i class="ti ti-dots-vertical"></i>
                    </button>
                    <div class="dropdown-menu">
                        <button type="button" class="dropdown-item delete" onclick="confirmDelete({{ $conexao->id }})">
                            <i class="ti ti-trash me-1"></i> Delete
                        </button>
                    </div>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirmar Exclusão</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Tem certeza que deseja deletar este item?
      </div>
      <div class="modal-footer">
        <form id="deleteForm" method="POST">
            @csrf
            @method('DELETE')
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-danger">Deletar</button>
        </form>
      </div>
    </div>
  </div>
</div>

@php
    $user_id = Auth::user()->id;
    $conexao = $conexoes->where('user_id', $user_id)->first();
@endphp
@if($conexao && $conexao->conn == 0)
<!-- Modal Authentication App -->
<div class="modal fade" id="startWa" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered modal-add-new-role">
    <div class="modal-content p-3 p-md-5">
      <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
      <div class="modal-body">
        <div class="text-center mb-4">
          <h3 class="role-title mb-2">Autenticação do WhatsApp</h3>
          <p class="text-muted">Conecte seu WhatsApp ao sistema</p>
        </div>
        <div class="text-center">
          <h5 class="mb-2 pt-1 text-break">Instruções</h5>
          <p class="mb-4">
            1. Abra o WhatsApp no seu celular.<br>
            2. Vá em "Mais opções" ou "Configurações" e selecione "Aparelhos Conectados".<br>
            3. Toque em "Conectar Aparelho".<br>
            4. Aponte seu celular para o QR Code abaixo.
          </p>
          <img src="{{ $conexao->qrcode }}" alt="QR Code" width="300">
          <div class="card-text" align="center">Atualizando em <span id="contador">15</span> segundos</div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mostrar o modal
    var myModal = new bootstrap.Modal(document.getElementById('startWa'));
    myModal.show();

    var contador = document.getElementById('contador');
    var tempo = 15;
    var intervalo;

    function atualizarQRCode() {
        fetch('{{ route('update-connection') }}?phone={{ $conexao->whatsapp }}', {
            method: 'GET'
        })
        .then(response => {
            // Verificar o tipo de conteúdo da resposta
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                return response.text().then(text => {
                    throw new Error('Resposta não é JSON: ' + text);
                });
            }
        })
        .then(data => {
            if (data.qrcode) {
                document.querySelector('#startWa img').src = data.qrcode;
                reiniciarContador();
            } else if (data.success) {
                // Exibir a mensagem de sucesso
                mostrarAlertaSucesso(data.success);
                // Recarregar a página com mensagem de sucesso
                window.location.reload();
            } else {
                console.error('Erro ao atualizar o QR Code:', data.error);
            }
        })
        .catch(error => console.error('Erro na requisição:', error));
    }

    function iniciarContador() {
        intervalo = setInterval(function() {
            tempo--;
            contador.textContent = tempo;
            if (tempo <= 0) {
                clearInterval(intervalo);
                atualizarQRCode();
            }
        }, 1000);
    }

    function reiniciarContador() {
        clearInterval(intervalo);
        tempo = 15;
        contador.textContent = tempo;
        iniciarContador();
    }

    function mostrarAlertaSucesso(mensagem) {
        var alertContainer = document.getElementById('alert-container');
        var alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-success alert-dismissible';
        alertDiv.role = 'alert';
        alertDiv.innerHTML = `
            ${mensagem}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        alertContainer.appendChild(alertDiv);
    }

    // Verificar se já estamos conectados
    var connStatus = parseInt('{{ $conexao->conn }}', 10); // Garantir que é um número
    // console.log('Status da Conexão:', connStatus);

    if (connStatus === 1) {
        // Recarregar a página
        window.location.reload();
    } else {
        iniciarContador();
    }
});
</script>
@endif

@endsection