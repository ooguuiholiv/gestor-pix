<?php $__env->startSection('title', 'Lista de Pedidos'); ?>

<?php
    $visibleColumns = getUserPreferences('ordens');
    $type = 'ordens';
?>

<?php $__env->startSection('page-script'); ?>
<script>
    var loadDataUrl = '<?php echo e(route('ordens.list')); ?>';
    var destroyMultipleUrl = '<?php echo e(route('ordens.destroy_multiple')); ?>';
    var label_update = '<?php echo e(__('messages.update')); ?>';
    var label_delete = '<?php echo e(__('messages.delete')); ?>';
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="<?php echo e(asset('assets/js/pages/ordens.js')); ?>"></script>
<script>
  function confirmDelete(orderId) {
    Swal.fire({
      title: 'Tem certeza?',
      text: "Você não poderá reverter isso!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Sim, excluir!',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        document.getElementById('delete-form-' + orderId).submit();
      }
    })
  }
</script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<h4 class="py-3 mb-2">
  <span class="text-muted fw-light"><?php echo e(config('variables.templateName', 'TemplateName')); ?> / </span> Lista de Pedidos
</h4>

<!-- Verificação de Mensagens de Sessão -->
<?php if(session('warning')): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <?php echo e(session('warning')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- mensagens para erros -->
<?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo e(session('error')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- mensagens para sucesso -->
<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card mb-4">
  <div class="card-widget-separator-wrapper">
    <div class="card-body card-widget-separator">
      <div class="row gy-4 gy-sm-1">
        <div class="col-sm-6 col-lg-4">
          <div class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-3 pb-sm-0">
            <div>
              <h4 class="mb-2"><?php echo e($totalPending); ?></h4>
              <p class="mb-0 fw-medium">Pagamento Pendente</p>
            </div>
            <span class="avatar me-sm-4">
              <span class="avatar-initial bg-label-secondary rounded">
                <i class="ti-md ti ti-calendar-stats text-body"></i>
              </span>
            </span>
          </div>
          <hr class="d-none d-sm-block d-lg-none me-4">
        </div>
        <div class="col-sm-6 col-lg-4">
          <div class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-3 pb-sm-0">
            <div>
              <h4 class="mb-2"><?php echo e($totalCompleted); ?></h4>
              <p class="mb-0 fw-medium">Concluídos</p>
            </div>
            <span class="avatar p-2 me-lg-4">
              <span class="avatar-initial bg-label-secondary rounded"><i
                  class="ti-md ti ti-checks text-body"></i></span>
            </span>
          </div>
          <hr class="d-none d-sm-block d-lg-none">
        </div>
        <div class="col-sm-6 col-lg-4">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <h4 class="mb-2"><?php echo e($totalFailed); ?></h4>
              <p class="mb-0 fw-medium">Cancelados</p>
            </div>
            <span class="avatar p-2">
              <span class="avatar-initial bg-label-secondary rounded"><i
                  class="ti-md ti ti-alert-octagon text-body"></i></span>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Tabela de Lista de Pedidos -->
<div class="card">
  <div class="card-body">
      <div class="table-responsive text-nowrap">
          <input type="hidden" id="data_type" value="ordens">
          <input type="hidden" id="save_column_visibility" name="visible_columns">
          <div class="fixed-table-toolbar">
          </div>
    <table id="table" data-toggle="table" data-loading-template="loadingTemplate"
        data-url="<?php echo e(route('ordens.list')); ?>" data-icons-prefix="bx" data-icons="icons"
        data-show-refresh="true" data-total-field="total" data-trim-on-search="false"
        data-data-field="rows" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true"
        data-side-pagination="server" data-show-columns="true" data-pagination="true"
        data-sort-name="id" data-sort-order="desc" data-mobile-responsive="true"
        data-query-params="queryParams" data-route-prefix="<?php echo e(Route::getCurrentRoute()->getPrefix()); ?>">
      <thead>
        <tr>
          <th data-checkbox="true"></th>
          <th data-field="id" data-visible="<?php echo e(in_array('id', $visibleColumns) || empty($visibleColumns) ? 'true' : 'false'); ?>" data-sortable="true">Pedido</th>
          <th data-field="created_at" data-visible="<?php echo e(in_array('created_at', $visibleColumns) || empty($visibleColumns) ? 'true' : 'false'); ?>" data-sortable="true">Data</th>
          <th data-field="valor" data-visible="<?php echo e(in_array('valor', $visibleColumns) || empty($visibleColumns) ? 'true' : 'false'); ?>" data-sortable="true">Pagamento</th>
          <th data-field="status" data-visible="<?php echo e(in_array('status', $visibleColumns) || empty($visibleColumns) ? 'true' : 'false'); ?>" data-sortable="true">Status</th>
          <th data-field="mercado_pago_id" data-visible="<?php echo e(in_array('mercado_pago_id', $visibleColumns) || empty($visibleColumns) ? 'true' : 'false'); ?>" data-sortable="true">ID da Transação</th>
          <th data-field="cliente_nome" data-visible="<?php echo e(in_array('cliente_nome', $visibleColumns) || empty($visibleColumns) ? 'true' : 'false'); ?>" data-sortable="true">Cliente</th>
          <th data-field="actions" data-visible="<?php echo e(in_array('actions', $visibleColumns) || empty($visibleColumns) ? 'true' : 'false'); ?>">Ações</th>
        </tr>
      </thead>
    </table>
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/gestor-pro.vipconnect.top/public_html/resources/views/content/apps/ordens.blade.php ENDPATH**/ ?>