<?php $__env->startSection('title', 'Registro Básico - Páginas'); ?>

<?php $__env->startSection('vendor-style'); ?>
<!-- Vendor -->
<link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/@form-validation/umd/styles/index.min.css')); ?>" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-style'); ?>
<!-- Page -->
<link rel="stylesheet" href="<?php echo e(asset('assets/vendor/css/pages/page-auth.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('vendor-script'); ?>
<script src="<?php echo e(asset('assets/vendor/libs/@form-validation/umd/bundle/popular.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-script'); ?>
<script src="<?php echo e(asset('assets/js/pages-auth.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-xxl">
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner py-4">

      <!-- Register Card -->
      <div class="card">
        <div class="card-body">
          <!-- Logo -->
          <div class="app-brand justify-content-center mb-4 mt-2">
            <a href="<?php echo e(url('/')); ?>" class="app-brand-link gap-2">
              <span class="app-brand-logo demo"><?php echo $__env->make('_partials.macros',["height"=>20,"withbg"=>'fill: #fff;'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?></span>
              <span class="app-brand-text demo text-body fw-bold ms-1"><?php echo e(config('variables.templateName')); ?></span>
            </a>
          </div>
          <!-- /Logo -->
          <h4 class="mb-1 pt-2">A aventura começa aqui 🚀</h4>
          <p class="mb-4">Torne a gestão do seu aplicativo fácil e divertida!</p>

          <form id="formAuthentication" class="mb-3" action="<?php echo e(route('auth-register-basic-post')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="mb-3">
              <label for="name" class="form-label">Nome de Usuário</label>
              <input type="text" class="form-control" id="name" name="name" placeholder="Digite seu nome de usuário" autofocus>
              <?php if($errors->has('name')): ?>
                <span class="text-danger"><?php echo e($errors->first('name')); ?></span>
              <?php endif; ?>
            </div>
            <div class="mb-3">
              <label for="whatsapp" class="form-label">Número do WhatsApp</label>
              <input type="text" class="form-control" id="whatsapp" name="whatsapp" placeholder="Digite seu número do WhatsApp">
              <?php if($errors->has('whatsapp')): ?>
                <span class="text-danger"><?php echo e($errors->first('whatsapp')); ?></span>
              <?php endif; ?>
            </div>
            <div class="mb-3 form-password-toggle">
              <label class="form-label" for="password">Senha</label>
              <div class="input-group input-group-merge">
                <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
              </div>
              <?php if($errors->has('password')): ?>
                <span class="text-danger"><?php echo e($errors->first('password')); ?></span>
              <?php endif; ?>
            </div>

            <div class="mb-3">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="terms-conditions" name="terms">
                <label class="form-check-label" for="terms-conditions">
                  Eu concordo com a
                  <a href="javascript:void(0);">política de privacidade & termos</a>
                </label>
              </div>
              <?php if($errors->has('terms')): ?>
                <span class="text-danger"><?php echo e($errors->first('terms')); ?></span>
              <?php endif; ?>
            </div>

            <!-- Campo oculto para o ID de referência -->
            <input type="hidden" name="ref" value="<?php echo e(request()->get('ref')); ?>">

            <button class="btn btn-primary d-grid w-100">
              Registrar
            </button>
          </form>

          <p class="text-center">
            <span>Já tem uma conta?</span>
            <a href="<?php echo e(url('auth/login-basic')); ?>">
              <span>Entrar</span>
            </a>
          </p>
        </div>
      </div>
      <!-- Register Card -->
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/gestorplayer/public_html/resources/views/content/authentications/auth-register-basic.blade.php ENDPATH**/ ?>