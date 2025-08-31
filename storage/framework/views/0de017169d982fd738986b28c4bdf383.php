<!-- Modal para Criar Campanha -->
<div class="modal fade" id="createCampanhaModal" tabindex="-1" aria-labelledby="createCampanhaModalLabel"
  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createCampanhaModalLabel">Criar Nova Campanha</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="multiStepsValidation" class="bs-stepper shadow-none">
          <div class="bs-stepper-header border-bottom-0">
            <div class="step" data-target="#campaignDetails">
              <button type="button" class="step-trigger">
                <span class="bs-stepper-circle"><i class="ti ti-smart-home ti-sm"></i></span>
                <span class="bs-stepper-label">
                  <span class="bs-stepper-title">Detalhes da Campanha</span>
                  <span class="bs-stepper-subtitle">Informações Básicas</span>
                </span>
              </button>
            </div>
            <div class="line"><i class="ti ti-chevron-right"></i></div>
            <div class="step" data-target="#campaignContent">
              <button type="button" class="step-trigger">
                <span class="bs-stepper-circle"><i class="ti ti-file-text ti-sm"></i></span>
                <span class="bs-stepper-label">
                  <span class="bs-stepper-title">Conteúdo</span>
                  <span class="bs-stepper-subtitle">Mensagem e Arquivo</span>
                </span>
              </button>
            </div>
          </div>
          <div class="bs-stepper-content">
            <form action="<?php echo e(route('campanhas.store')); ?>" method="POST" enctype="multipart/form-data">
              <?php echo csrf_field(); ?>
              <!-- Step 1: Campaign Details -->
              <div id="campaignDetails" class="content">
                <div class="content-header mb-4">
                  <h3 class="mb-1">Detalhes da Campanha</h3>
                  <p>Preencha as informações básicas da campanha</p>
                </div>
                <div class="mb-3">
                  <label for="nome" class="form-label">Nome</label>
                  <input type="text" class="form-control" id="nome" name="nome" required>
                </div>
                <div class="mb-3">
                  <label for="data" class="form-label">Data (Opcional)</label>
                  <input type="date" class="form-control" id="data" name="data">
                </div>
                <div class="mb-3">
                  <label for="horario" class="form-label">Horário</label>
                  <input type="time" class="form-control" id="horario" name="horario" required>
                </div>
                <div class="mb-3">
                  <label for="origem_contatos" class="form-label">Origem dos Contatos</label>
                  <select class="form-control" id="origem_contatos" name="origem_contatos" required
                    onchange="toggleContatos(this.value)">
                    <option value="todos">Todos os Contatos</option>
                    <option value="manual">Selecionar Contatos Manualmente</option>
                    <option value="vencidos">Clientes Vencidos</option>
                    <option value="vencem_hoje">Clientes que Vencem Hoje</option>
                    <option value="ativos">Clientes Ativos</option>
                    <option value="servidores">Servidores</option>
                  </select>
                </div>
                <div class="mb-3" id="contatos_manual" style="display: none;">
                  <label for="contatos" class="form-label">Selecione os Contatos</label>
                  <div class="form-check" id="contatos_todos" style="display: none;">
                    <?php $__currentLoopData = $clientes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cliente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <input class="form-check-input" type="checkbox" name="contatos[]" value="<?php echo e($cliente->id); ?>"
                        id="cliente<?php echo e($cliente->id); ?>">
                      <label class="form-check-label" for="cliente<?php echo e($cliente->id); ?>"><?php echo e($cliente->nome); ?></label><br>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </div>
                  <div class="form-check" id="contatos_vencidos" style="display: none;">
                    <?php $__currentLoopData = $clientesVencidos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cliente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <input class="form-check-input" type="checkbox" name="contatos[]" value="<?php echo e($cliente->id); ?>"
                        id="cliente<?php echo e($cliente->id); ?>" checked>
                      <label class="form-check-label" for="cliente<?php echo e($cliente->id); ?>"><?php echo e($cliente->nome); ?>

                        (Vencido)</label><br>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </div>
                  <div class="form-check" id="contatos_vencem_hoje" style="display: none;">
                    <?php $__currentLoopData = $clientesVencemHoje; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cliente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <input class="form-check-input" type="checkbox" name="contatos[]" value="<?php echo e($cliente->id); ?>"
                        id="cliente<?php echo e($cliente->id); ?>" checked>
                      <label class="form-check-label" for="cliente<?php echo e($cliente->id); ?>"><?php echo e($cliente->nome); ?> (Vence
                        Hoje)</label><br>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </div>
                  <div class="form-check" id="contatos_ativos" style="display: none;">
                    <?php $__currentLoopData = $clientesAtivos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cliente): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <input class="form-check-input" type="checkbox" name="contatos[]" value="<?php echo e($cliente->id); ?>"
                        id="cliente<?php echo e($cliente->id); ?>" checked>
                      <label class="form-check-label" for="cliente<?php echo e($cliente->id); ?>"><?php echo e($cliente->nome); ?>

                        (Ativo)</label><br>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </div>
                <div class="form-check" id="contatos_servidores" style="display: none;"> <!-- Novo contêiner -->
                  <?php $__currentLoopData = $servidores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $servidor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <input class="form-check-input" type="checkbox" name="servidores[]" value="<?php echo e($servidor->id); ?>"
                      id="servidor<?php echo e($servidor->id); ?>">
                    <label class="form-check-label" for="servidor<?php echo e($servidor->id); ?>"><?php echo e($servidor->nome); ?> (<?php echo e($servidor->clientes_count); ?> clientes)</label><br>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
              </div>
              <div class="mb-3" style="display: none;">
                <label for="ignorar_contatos" class="form-label">Ignorar Contatos</label>
                <select class="form-control" id="ignorar_contatos" name="ignorar_contatos" required>
                  <option value="1">Sim</option>
                  <option value="0" selected>Não</option> <!-- "Não" selecionado por padrão -->
                </select>
              </div>
                <div class="mb-3">
                  <label for="enviar_diariamente" class="form-label">Enviar Diariamente</label>
                  <select class="form-control" id="enviar_diariamente" name="enviar_diariamente" required>
                    <option value="1">Sim</option>
                    <option value="0">Não</option>
                  </select>
                </div>
                <div class="d-flex justify-content-between mt-4">
                  <button class="btn btn-label-secondary btn-prev" disabled> <i
                      class="ti ti-arrow-left ti-xs me-sm-1 me-0"></i>
                    <span class="align-middle d-sm-inline-block d-none">Anterior</span>
                  </button>
                  <button class="btn btn-primary btn-next"> <span
                      class="align-middle d-sm-inline-block d-none me-sm-1 me-0">Próximo</span> <i
                      class="ti ti-arrow-right ti-xs"></i></button>
                </div>
              </div>
              <!-- Step 2: Campaign Content -->
              <div id="campaignContent" class="content">
                <div class="content-header mb-4">
                  <h3 class="mb-1">Conteúdo da Campanha</h3>
                  <p>Preencha a mensagem e adicione um arquivo</p>
                </div>
                <div class="mb-3">
                  <label for="mensagem" class="form-label">Mensagem</label>
                  <textarea class="form-control" id="mensagem" name="mensagem" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                  <label for="arquivo" class="form-label">Adicionar Arquivo</label>
                  <input type="file" class="form-control" id="arquivo" name="arquivo">
                  <small class="form-text text-muted">O tamanho máximo do arquivo é de 20MB.</small>
                </div>
                <div class="d-flex justify-content-between mt-4">
                  <button class="btn btn-label-secondary btn-prev"> <i class="ti ti-arrow-left ti-xs me-sm-1 me-0"></i>
                    <span class="align-middle d-sm-inline-block d-none">Anterior</span>
                  </button>
                  <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modais para Visualizar Campanhas -->
<?php $__currentLoopData = $campanhas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $campanha): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <div class="modal fade" id="viewCampanhaModal<?php echo e($campanha->id); ?>" tabindex="-1"
    aria-labelledby="viewCampanhaModalLabel<?php echo e($campanha->id); ?>" aria-hidden="true">
    <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
      <h5 class="modal-title" id="viewCampanhaModalLabel<?php echo e($campanha->id); ?>">Detalhes da Campanha</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      <div id="multiStepsViewValidation<?php echo e($campanha->id); ?>" class="bs-stepper shadow-none">
        <div class="bs-stepper-header border-bottom-0">
        <div class="step" data-target="#viewCampaignDetails<?php echo e($campanha->id); ?>">
          <button type="button" class="step-trigger">
          <span class="bs-stepper-circle"><i class="ti ti-smart-home ti-sm"></i></span>
          <span class="bs-stepper-label">
            <span class="bs-stepper-title">Detalhes da Campanha</span>
            <span class="bs-stepper-subtitle">Informações Básicas</span>
          </span>
          </button>
        </div>
        <div class="line"><i class="ti ti-chevron-right"></i></div>
        <div class="step" data-target="#viewCampaignContent<?php echo e($campanha->id); ?>">
          <button type="button" class="step-trigger">
          <span class="bs-stepper-circle"><i class="ti ti-file-text ti-sm"></i></span>
          <span class="bs-stepper-label">
            <span class="bs-stepper-title">Conteúdo</span>
            <span class="bs-stepper-subtitle">Mensagem e Arquivo</span>
          </span>
          </button>
        </div>
        </div>
        <div class="bs-stepper-content">
        <!-- Step 1: Campaign Details -->
        <div id="viewCampaignDetails<?php echo e($campanha->id); ?>" class="content">
          <div class="content-header mb-4">
          <h3 class="mb-1">Detalhes da Campanha</h3>
          <p>Informações Básicas da Campanha</p>
          </div>
          <div class="mb-3">
          <label for="nome" class="form-label">Nome</label>
          <input type="text" class="form-control" id="nome" value="<?php echo e($campanha->nome); ?>" readonly>
          </div>
          <div class="mb-3">
          <label for="horario" class="form-label">Horário</label>
          <input type="time" class="form-control" id="horario" value="<?php echo e($campanha->horario); ?>" readonly>
          </div>
          <div class="mb-3">
          <label for="origem_contatos" class="form-label">Origem dos Contatos</label>
          <input type="text" class="form-control" id="origem_contatos" value="<?php echo e($campanha->origem_contatos); ?>"
            readonly>
          </div>
          <div class="mb-3">
          <label for="ignorar_contatos" class="form-label">Ignorar Contatos</label>
          <input type="text" class="form-control" id="ignorar_contatos"
            value="<?php echo e($campanha->ignorar_contatos ? 'Sim' : 'Não'); ?>" readonly>
          </div>
          <div class="d-flex justify-content-between mt-4">
          <button class="btn btn-label-secondary btn-prev" disabled> <i
            class="ti ti-arrow-left ti-xs me-sm-1 me-0"></i>
            <span class="align-middle d-sm-inline-block d-none">Anterior</span>
          </button>
          <button class="btn btn-primary btn-next"> <span
            class="align-middle d-sm-inline-block d-none me-sm-1 me-0">Próximo</span> <i
            class="ti ti-arrow-right ti-xs"></i></button>
          </div>
        </div>
        <!-- Step 2: Campaign Content -->
        <div id="viewCampaignContent<?php echo e($campanha->id); ?>" class="content">
          <div class="content-header mb-4">
          <h3 class="mb-1">Conteúdo da Campanha</h3>
          <p>Mensagem e Arquivo da Campanha</p>
          </div>
          <div class="mb-3">
          <label for="mensagem" class="form-label">Mensagem</label>
          <textarea class="form-control" id="mensagem" rows="3" readonly><?php echo e($campanha->mensagem); ?></textarea>
          </div>
          <div class="mb-3">
          <label for="arquivo" class="form-label">Arquivo</label>
          <input type="text" class="form-control" id="arquivo" value="<?php echo e($campanha->arquivo); ?>" readonly>
          <small class="form-text text-muted">O tamanho máximo do arquivo é de 20MB.</small>
          </div>
          <div class="d-flex justify-content-between mt-4">
          <button class="btn btn-label-secondary btn-prev"> <i class="ti ti-arrow-left ti-xs me-sm-1 me-0"></i>
            <span class="align-middle d-sm-inline-block d-none">Anterior</span>
          </button>
          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Fechar</button>
          </div>
        </div>
        </div>
      </div>
      </div>
    </div>
    </div>
  </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<!-- Modal para Ativar Licença -->
<div class="modal fade" id="activateLicenseModal" tabindex="-1" aria-labelledby="activateLicenseModalLabel"
  aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="activateLicenseModalLabel">Ativar Licença</h5>
      </div>
      <div class="modal-body">
        <?php if($errors->has('license')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo e($errors->first('license')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <form id="activateLicenseForm" action="<?php echo e(route('activate-module')); ?>" method="POST">
          <?php echo csrf_field(); ?>
          <div class="mb-3">
            <label for="domain" class="form-label">Domínio</label>
            <input type="text" class="form-control" id="domain" name="domain" value="<?php echo e($domain); ?>" required readonly>
          </div>
          <div class="mb-3">
            <label for="license_key" class="form-label">Chave de Licença</label>
            <input type="text" class="form-control" id="license_key" name="license_key" required>
          </div>
          <button type="submit" class="btn btn-primary">Ativar</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
 document.addEventListener('DOMContentLoaded', function () {
  var stepper = new Stepper(document.querySelector('#multiStepsValidation'));

  document.querySelectorAll('.btn-next').forEach(function (button) {
    button.addEventListener('click', function () {
      stepper.next();
    });
  });

  document.querySelectorAll('.btn-prev').forEach(function (button) {
    button.addEventListener('click', function () {
      stepper.previous();
    });
  });

});

function toggleContatos(value) {
  var contatosManual = document.getElementById('contatos_manual');
  var contatosTodos = document.getElementById('contatos_todos');
  var contatosVencidos = document.getElementById('contatos_vencidos');
  var contatosVencemHoje = document.getElementById('contatos_vencem_hoje');
  var contatosAtivos = document.getElementById('contatos_ativos');
  var contatosServidores = document.getElementById('contatos_servidores');

  // Esconde todos os contêineres de contatos
  contatosManual.style.display = 'none';
  contatosTodos.style.display = 'none';
  contatosVencidos.style.display = 'none';
  contatosVencemHoje.style.display = 'none';
  contatosAtivos.style.display = 'none';
  contatosServidores.style.display = 'none';

  // Desmarca todos os checkboxes
  marcarCheckboxes(contatosTodos, false);
  marcarCheckboxes(contatosVencidos, false);
  marcarCheckboxes(contatosVencemHoje, false);
  marcarCheckboxes(contatosAtivos, false);
  marcarCheckboxes(contatosServidores, false);

  // Exibe e marca os checkboxes conforme a seleção
  if (value === 'manual') {
    contatosManual.style.display = 'block';
    contatosTodos.style.display = 'block';
  } else if (value === 'vencidos') {
    contatosManual.style.display = 'block';
    contatosVencidos.style.display = 'block';
    marcarCheckboxes(contatosVencidos, true);
  } else if (value === 'vencem_hoje') {
    contatosManual.style.display = 'block';
    contatosVencemHoje.style.display = 'block';
    marcarCheckboxes(contatosVencemHoje, true);
  } else if (value === 'ativos') {
    contatosManual.style.display = 'block';
    contatosAtivos.style.display = 'block';
    marcarCheckboxes(contatosAtivos, true);
  } else if (value === 'servidores') {
    contatosManual.style.display = 'block';
    contatosServidores.style.display = 'block';
  }
}

function marcarCheckboxes(container, checked) {
  var checkboxes = container.querySelectorAll('input[type="checkbox"]');
  checkboxes.forEach(function (checkbox) {
    checkbox.checked = checked;
  });
}
</script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    <?php $__currentLoopData = $campanhas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $campanha): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    var stepper<?php echo e($campanha->id); ?> = new Stepper(document.querySelector('#multiStepsViewValidation<?php echo e($campanha->id); ?>'));

    document.querySelectorAll('#viewCampanhaModal<?php echo e($campanha->id); ?> .btn-next').forEach(function (button) {
      button.addEventListener('click', function () {
      stepper<?php echo e($campanha->id); ?>.next();
      });
    });

    document.querySelectorAll('#viewCampanhaModal<?php echo e($campanha->id); ?> .btn-prev').forEach(function (button) {
      button.addEventListener('click', function () {
      stepper<?php echo e($campanha->id); ?>.previous();
      });
    });
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  });
</script>
<?php /**PATH /home/gestor-pro.vipconnect.top/public_html/resources/views/campanhas/modals.blade.php ENDPATH**/ ?>