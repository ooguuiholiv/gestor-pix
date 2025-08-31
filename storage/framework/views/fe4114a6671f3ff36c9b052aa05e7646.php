<?php $__env->startSection('title', 'Gerenciar Templates'); ?>

<?php
    $visibleColumns = getUserPreferences('templates');
    $type = 'templates';
?>

<?php $__env->startSection('page-script'); ?>
<script>
    var loadDataUrl = '<?php echo e(route('templates.list')); ?>';
    var destroyMultipleUrl = '<?php echo e(route('manage-templates.destroy-multiple')); ?>';
    var label_update = '<?php echo e(__('messages.update')); ?>';
    var label_delete = '<?php echo e(__('messages.delete')); ?>';
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="<?php echo e(asset('assets/js/pages/notification.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
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

        <!-- Formulário para definir o horário de envio e a finalidade -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Configurar Horário de Envio</h5>
                <form action="<?php echo e(route('schedule-settings.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="user_id" value="<?php echo e(auth()->user()->id); ?>">
                    <div class="mb-3">
                        <label for="finalidade" class="form-label">Finalidade</label>
                        <select class="form-select" id="finalidade" name="finalidade" required>
                            <option value="cobranca_3_dias_atras">Cobrança 3 Dias Atrás</option>
                            <option value="cobranca_5_dias_atras">Cobrança 5 Dias Atrás</option>
                            <option value="cobranca_hoje">Cobrança Hoje</option>
                            <option value="cobranca_3_dias_futuro">Cobrança 3 Dias Futuro</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="execution_time" class="form-label">Horário de Envio</label>
                        <input type="time" class="form-control" id="execution_time" name="execution_time" required>
                    </div>
                    <button type="submit" class="btn btn-success">Salvar Configuração</button>
                </form>
            </div>
        </div>

        <!-- Tabela para exibir as configurações salvas -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <input type="hidden" id="data_type" value="templates">
                    <input type="hidden" id="save_column_visibility" name="visible_columns">
                    <div class="fixed-table-toolbar">
                    </div>
                    <table id="table" data-toggle="table" data-loading-template="loadingTemplate"
                        data-url="<?php echo e(route('manage-templates.list')); ?>" data-icons-prefix="bx" data-icons="icons"
                        data-show-refresh="true" data-total-field="total" data-trim-on-search="false"
                        data-data-field="rows" data-page-list="[5, 10, 20, 50, 100, 200]" data-search="true"
                        data-side-pagination="server" data-show-columns="true" data-pagination="true"
                        data-sort-name="id" data-sort-order="desc" data-mobile-responsive="true"
                        data-query-params="queryParams"
                        data-route-prefix="<?php echo e(Route::getCurrentRoute()->getPrefix()); ?>">
                        <thead>
                            <tr>
                                <th data-checkbox="true"></th>
                                <th data-sortable="true" data-field="id">ID</th>
                                <th data-field="finalidade" data-visible="<?php echo e(in_array('finalidade', $visibleColumns) || empty($visibleColumns) ? 'true' : 'false'); ?>" data-sortable="true">Finalidade</th>
                                <th data-field="execution_time" data-visible="<?php echo e(in_array('execution_time', $visibleColumns) || empty($visibleColumns) ? 'true' : 'false'); ?>" data-sortable="true">Horário de Envio</th>
                                <th data-field="status" data-visible="<?php echo e(in_array('status', $visibleColumns) || empty($visibleColumns) ? 'true' : 'false'); ?>" data-sortable="true">Status</th>
                                <th data-field="actions" data-visible="<?php echo e(in_array('actions', $visibleColumns) || empty($visibleColumns) ? 'true' : 'false'); ?>">Ações</th>
                            </tr>
                        </thead>
                    </table>
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
<?php echo $__env->make('layouts.layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/gestor-pro.vipconnect.top/public_html/resources/views/templates/manage-templates.blade.php ENDPATH**/ ?>