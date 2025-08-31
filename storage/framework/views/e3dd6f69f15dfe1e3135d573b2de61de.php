

<?php $__env->startSection('title', 'Campanhas'); ?>

<?php $__env->startSection('vendor-style'); ?>
<!-- Inclui estilos de bibliotecas de terceiros -->
<link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/apex-charts/apex-charts.css')); ?>" />
<link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')); ?>" />
<link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')); ?>" />
<link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/bs-stepper/bs-stepper.css')); ?>" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-style'); ?>
<!-- Inclui estilos específicos da página -->
<link rel="stylesheet" href="<?php echo e(asset('assets/vendor/css/pages/app-logistics-dashboard.css')); ?>" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('vendor-script'); ?>
<!-- Inclui scripts de bibliotecas de terceiros -->
<script src="<?php echo e(asset('assets/vendor/libs/apex-charts/apexcharts.js')); ?>"></script>
<script src="<?php echo e(asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')); ?>"></script>
<script src="<?php echo e(asset('assets/vendor/libs/bs-stepper/bs-stepper.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-script'); ?>
<!-- Inclui scripts específicos da página -->
<script src="<?php echo e(asset('assets/js/pages-auth-multisteps.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<!-- Exibe alertas de sessão -->
<?php if(session('warning')): ?>
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
    <?php echo e(session('warning')); ?>

    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
<?php endif; ?>

<?php if(!$campanhasAtivo): ?>
<!-- Exibe modal de ativação de licença se campanhasAtivo for falso -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var activateLicenseModal = new bootstrap.Modal(document.getElementById('activateLicenseModal'), {
            backdrop: 'static',
            keyboard: false
        });
        activateLicenseModal.show();
    });
</script>
<?php endif; ?>

<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="container">
<h4 class="py-3 mb-2">
  <span class="text-muted fw-light"><?php echo e(config('variables.templateName', 'TemplateName')); ?> / </span>Campanhas
</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCampanhaModal">Criar Nova Campanha</button>
    <table class="table mt-4">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Horário</th>
                <th>Diariamente</th>
                <th>Data</th>
                <th>Mensagem</th>
                <th>Arquivo</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $campanhas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $campanha): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($campanha->id); ?></td>
                    <td><?php echo e($campanha->nome); ?></td>
                    <td><?php echo e($campanha->horario); ?></td>
                    <td><?php echo e($campanha->enviar_diariamente == 1 ? 'Sim' : 'Não'); ?></td>
                    <td><?php echo e($campanha->data ? date('d/m/Y', strtotime($campanha->data)) : 'N/A'); ?></td>
                    <td><?php echo e($campanha->mensagem); ?></td>
                    <td>
                        <?php if($campanha->arquivo): ?>
                            <a href="<?php echo e(asset($campanha->arquivo)); ?>" target="_blank">Ver Arquivo</a>
                        <?php else: ?>
                            Nenhum arquivo
                        <?php endif; ?>
                    </td>
                    <td>
                        <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#viewCampanhaModal<?php echo e($campanha->id); ?>">Ver</button>
                        <form action="<?php echo e(route('campanhas.destroy', $campanha->id)); ?>" method="POST" style="display:inline;">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-danger">Excluir</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>

<?php echo $__env->make('campanhas.modals', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts/layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/gestor-pro.vipconnect.top/public_html/resources/views/campanhas/index.blade.php ENDPATH**/ ?>