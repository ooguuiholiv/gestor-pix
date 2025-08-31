<?php $__env->startSection('title', 'Login do Cliente'); ?>

<?php $__env->startSection('vendor-style'); ?>
<!-- Vendor -->
<link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/@form-validation/umd/styles/index.min.css')); ?>" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-style'); ?>
<!-- Page -->
<link rel="stylesheet" href="<?php echo e(asset('assets/vendor/css/pages/page-auth.css')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('vendor-script'); ?>
<!-- <script src="<?php echo e(asset('assets/vendor/libs/@form-validation/umd/bundle/popular.min.js')); ?>"></script> -->
<script src="<?php echo e(asset('assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-script'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('formAuthentication');

  form.addEventListener('submit', function (event) {
    event.preventDefault();

    // Limpar mensagens de erro anteriores
    const errorMessages = document.querySelectorAll('.text-danger');
    errorMessages.forEach(function (message) {
      message.remove();
    });

    let isValid = true;

    // Validação do número de WhatsApp
    const whatsappInput = document.getElementById('whatsapp');
    const whatsappValue = whatsappInput.value.trim();
    const whatsappErrorContainer = whatsappInput.nextElementSibling;
    if (whatsappValue === '') {
      isValid = false;
      const errorMessage = document.createElement('span');
      errorMessage.classList.add('text-danger');
      errorMessage.textContent = 'Por favor, insira seu número de WhatsApp';
      whatsappErrorContainer.appendChild(errorMessage);
    } else if (!/^\d{10,15}$/.test(whatsappValue)) {
      isValid = false;
      const errorMessage = document.createElement('span');
      errorMessage.classList.add('text-danger');
      errorMessage.textContent = 'Por favor, insira um número de WhatsApp válido';
      whatsappErrorContainer.appendChild(errorMessage);
    }

    // Validação da senha
    const passwordInput = document.getElementById('password');
    const passwordValue = passwordInput.value.trim();
    const passwordErrorContainer = passwordInput.parentNode.parentNode.querySelector('.error-container');
    if (passwordValue === '') {
      isValid = false;
      const errorMessage = document.createElement('span');
      errorMessage.classList.add('text-danger');
      errorMessage.textContent = 'Por favor, insira sua senha';
      passwordErrorContainer.appendChild(errorMessage);
    } else if (passwordValue.length < 6) {
      isValid = false;
      const errorMessage = document.createElement('span');
      errorMessage.classList.add('text-danger');
      errorMessage.textContent = 'A senha deve ter pelo menos 6 caracteres';
      passwordErrorContainer.appendChild(errorMessage);
    }

    if (isValid) {
      form.submit();
    }
  });
});
</script>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-xxl">
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner py-4">
      <!-- Login -->
      <div class="card">
        <div class="card-body">
          <!-- Logo -->
          <div class="app-brand justify-content-center mb-4 mt-2">
            <a href="<?php echo e(url('/')); ?>" class="app-brand-link gap-2">
              <span class="app-brand-text demo text-body fw-bold ms-1"><?php echo e(config('variables.templateName')); ?></span>
            </a>
          </div>
          <!-- /Logo -->
          <h4 class="mb-1 pt-2">Bem-vindo ao <?php echo e(config('variables.templateName')); ?>! 👋</h4>
          <p class="mb-4">Por favor, faça login na sua conta usando seu número de WhatsApp</p>

          <form id="formAuthentication" class="mb-3" action="<?php echo e(route('client.login')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="mb-3">
              <label for="whatsapp" class="form-label">Número de WhatsApp</label>
              <input type="text" class="form-control" id="whatsapp" name="whatsapp" placeholder="Digite seu número de WhatsApp" autofocus>
              <div class="error-container"></div>
              <?php if($errors->has('whatsapp')): ?>
                <span class="text-danger"><?php echo e($errors->first('whatsapp')); ?></span>
              <?php endif; ?>
            </div>
            <div class="mb-3 form-password-toggle">
              <div class="d-flex justify-content-between">
                <label class="form-label" for="password">Senha</label>
                <a href="<?php echo e(url('auth/forgot-password-basic')); ?>">
                  <small>Esqueceu a senha?</small>
                </a>
              </div>
              <div class="input-group input-group-merge">
                <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
                <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>
              </div>
              <div class="error-container"></div>
              <?php if($errors->has('password')): ?>
                <span class="text-danger"><?php echo e($errors->first('password')); ?></span>
              <?php endif; ?>
            </div>
            <div class="mb-3">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember-me" name="remember">
                <label class="form-check-label" for="remember-me">
                  Lembrar-me
                </label>
              </div>
            </div>
            <div class="mb-3">
              <button class="btn btn-primary d-grid w-100" type="submit">Entrar</button>
            </div>
          </form>
        </div>
      </div>
      <!-- /Login -->
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/layoutMaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/gestor-pro.vipconnect.top/public_html/resources/views/client/login.blade.php ENDPATH**/ ?>