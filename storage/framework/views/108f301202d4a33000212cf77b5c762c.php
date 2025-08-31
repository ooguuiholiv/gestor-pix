<?php
    use App\Services\TrialService;

    $containerNav =
        isset($configData['contentLayout']) && $configData['contentLayout'] === 'compact'
            ? 'container-xxl'
            : 'container-fluid';
    $navbarDetached = $navbarDetached ?? '';

    $user = auth()->user();
    $isAdmin = $user->role_id == 1;
    $isClient = $user->role_id == 3;
    $isRevendedor = $user->role_id == 4;

    sleep(1);
    // Obter instância do serviço
    $trialService = app(TrialService::class);

    // Verificar o status do trial
    $trialStatus = $trialService->getTrialStatus($user->trial_ends_at);
    $daysRemaining = $trialStatus['daysRemaining'];
    $isExpired = $trialStatus['isExpired'];
    $trialEndsAt = $trialStatus['trialEndsAt'];

    // Obter a mensagem de expiração
    $expirationMessage = $trialService->getExpirationMessage($daysRemaining, $isExpired, $trialEndsAt);
?>

<!-- Navbar -->
<?php if(isset($navbarDetached) && $navbarDetached == 'navbar-detached'): ?>
    <nav class="layout-navbar <?php echo e($containerNav); ?> navbar navbar-expand-xl <?php echo e($navbarDetached); ?> align-items-center bg-navbar-theme"
        id="layout-navbar">
<?php endif; ?>
<?php if(isset($navbarDetached) && $navbarDetached == ''): ?>
    <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
        <div class="<?php echo e($containerNav); ?>">
<?php endif; ?>

<!--  Brand demo (display only for navbar-full and hide on below xl) -->
<?php if(isset($navbarFull)): ?>
    <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
        <a href="<?php echo e(url('/')); ?>" class="app-brand-link gap-2">
            <span class="app-brand-logo demo">
                <?php echo $__env->make('_partials.macros', ['height' => 20], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </span>
            <span class="app-brand-text demo menu-text fw-bold"><?php echo e(config('variables.templateName')); ?></span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
            <i class="ti ti-x ti-sm align-middle"></i>
        </a>
    </div>
<?php endif; ?>

<!-- ! Not required for layout-without-menu -->
<?php if(!isset($navbarHideToggle)): ?>
    <div
        class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0<?php echo e(isset($menuHorizontal) ? ' d-xl-none ' : ''); ?> <?php echo e(isset($contentNavbar) ? ' d-xl-none ' : ''); ?>">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="ti ti-menu-2 ti-sm"></i>
        </a>
    </div>
<?php endif; ?>

<div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

    <?php if(!isset($menuHorizontal)): ?>
        <!-- Search -->
        <div class="navbar-nav align-items-center">
            <div class="nav-item navbar-search-wrapper mb-0">
                <a class="nav-item nav-link search-toggler d-flex align-items-center px-0" href="javascript:void(0);">
                    <i class="ti ti-search ti-md me-2"></i>
                    <span class="d-none d-md-inline-block text-muted">Search (Ctrl+/)</span>
                </a>
            </div>
        </div>
        <!-- /Search -->
    <?php endif; ?>
    <ul class="navbar-nav flex-row align-items-center ms-auto">
        <?php if(isset($menuHorizontal)): ?>
            <!-- Search -->
            <li class="nav-item navbar-search-wrapper me-2 me-xl-0">
                <a class="nav-link search-toggler" href="javascript:void(0);">
                    <i class="ti ti-search ti-md"></i>
                </a>
            </li>
            <!-- /Search -->
        <?php endif; ?>
        <?php if($configData['hasCustomizer'] == true): ?>
            <!-- Style Switcher -->
            <li class="nav-item dropdown-style-switcher dropdown me-2 me-xl-0">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <i class='ti ti-md'></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-styles">
                    <li>
                        <a class="dropdown-item" href="javascript:void(0);" data-theme="light">
                            <span class="align-middle"><i class='ti ti-sun me-2'></i>Light</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="javascript:void(0);" data-theme="dark">
                            <span class="align-middle"><i class="ti ti-moon me-2"></i>Dark</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="javascript:void(0);" data-theme="system">
                            <span class="align-middle"><i class="ti ti-device-desktop me-2"></i>System</span>
                        </a>
                    </li>
                </ul>
            </li>
        <?php endif; ?>

        <?php if(!$isClient): ?>
            <!-- Quick links  -->
            <li class="nav-item dropdown-shortcuts navbar-dropdown dropdown me-2 me-xl-0">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown"
                    data-bs-auto-close="outside" aria-expanded="false">
                    <i class='ti ti-layout-grid-add ti-md'></i>
                </a>
                <div class="dropdown-menu dropdown-menu-end py-0">
                    <div class="dropdown-menu-header border-bottom">
                        <div class="dropdown-header d-flex align-items-center py-3">
                            <h5 class="text-body mb-0 me-auto">Shortcuts</h5>
                            <a href="javascript:void(0)" class="dropdown-shortcuts-add text-body"
                                data-bs-toggle="tooltip" data-bs-placement="top" title="Add shortcuts"><i
                                    class="ti ti-sm ti-apps"></i></a>
                        </div>
                    </div>
                    <div class="dropdown-shortcuts-list scrollable-container">
                        <div class="row row-bordered overflow-visible g-0">
                            <div class="dropdown-shortcuts-item col">
                                <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                                    <i class="ti ti-brand-whatsapp fs-4"></i>
                                </span>
                                <a href="<?php echo e(url('app/whatsapp')); ?>" class="stretched-link">WhatsApp</a>
                                <small class="text-muted mb-0">Mensagens</small>
                            </div>
                            <div class="dropdown-shortcuts-item col">
                                <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                                    <i class="ti ti-calendar fs-4"></i>
                                </span>
                                <a href="javascript:void(0);" class="stretched-link">Validade do Painel</a>
                                <?php if(!$isAdmin): ?>
                                    <small class="text-muted mb-0"><?php echo e($expirationMessage); ?></small>
                                <?php endif; ?>
                            </div>
                            <?php if(auth()->user()->role_id == 1): ?>
                                <div class="dropdown-shortcuts-item col">
                                    <span class="dropdown-shortcuts-icon rounded-circle mb-2">
                                        <i class="ti ti-calendar fs-4"></i>
                                    </span>
                                    <a href="javascript:void(0);" class="stretched-link">Validade Licença</a>
                                    <small id="expiration-date" class="text-muted mb-0"></small>
                                    <!-- Novo campo para a data de expiração -->
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </li>
            <!-- Quick links -->
        <?php endif; ?>
        <!-- User -->
        <li class="nav-item navbar-dropdown dropdown-user dropdown">
            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                <div class="avatar avatar-online">
                    <img src="<?php echo e(Auth::check() && Auth::user()->role_id == 3 ? asset('assets/img/avatars/2.png') : (Auth::check() ? Auth::user()->profile_photo_url : asset('assets/img/avatars/2.png'))); ?>"
                        alt class="h-auto rounded-circle">
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="<?php echo e(route('pages-profile-user')); ?>">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar avatar-online">
                                    <img src="<?php echo e(Auth::check() && Auth::user()->role_id == 3 ? asset('assets/img/avatars/2.png') : (Auth::check() ? Auth::user()->profile_photo_url : asset('assets/img/avatars/2.png'))); ?>"
                                        alt class="h-auto rounded-circle">
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <span class="fw-medium d-block">
                                    <?php echo e(Auth::check() ? Auth::user()->name : 'John Doe'); ?>

                                </span>
                                <small class="text-muted">
                                    <?php if(Auth::check()): ?>
                                        <?php switch(Auth::user()->role_id):
                                            case (1): ?>
                                                Admin
                                            <?php break; ?>

                                            <?php case (2): ?>
                                                Master
                                            <?php break; ?>

                                            <?php case (3): ?>
                                                Cliente
                                            <?php break; ?>

                                            <?php case (4): ?>
                                                Revendedor
                                            <?php break; ?>

                                            <?php default: ?>
                                                Usuário
                                        <?php endswitch; ?>
                                    <?php endif; ?>
                                </small>
                            </div>
                        </div>
                    </a>
                </li>
                <li>
                    <div class="dropdown-divider"></div>
                </li>
                <?php if(auth()->user()->role_id != 3): ?>
                    <li>
                        <a class="dropdown-item" href="<?php echo e(route('pages-profile-user')); ?>">
                            <i class="ti ti-user-check me-2 ti-sm"></i>
                            <span class="align-middle">Perfil</span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if(Auth::check() && Laravel\Jetstream\Jetstream::hasApiFeatures()): ?>
                    <li>
                        <a class="dropdown-item" href="<?php echo e(route('api-tokens.index')); ?>">
                            <i class='ti ti-key me-2 ti-sm'></i>
                            <span class="align-middle">API Tokens</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if(Auth::user() && Laravel\Jetstream\Jetstream::hasTeamFeatures()): ?>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <h6 class="dropdown-header">Manage Team</h6>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <a class="dropdown-item"
                            href="<?php echo e(Auth::user() ? route('teams.show', Auth::user()->currentTeam->id) : 'javascript:void(0)'); ?>">
                            <i class='ti ti-settings me-2'></i>
                            <span class="align-middle">Team Settings</span>
                        </a>
                    </li>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create', Laravel\Jetstream\Jetstream::newTeamModel())): ?>
                        <li>
                            <a class="dropdown-item" href="<?php echo e(route('teams.create')); ?>">
                                <i class='ti ti-user me-2'></i>
                                <span class="align-middle">Create New Team</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if(Auth::user()->allTeams()->count() > 1): ?>
                        <li>
                            <div class="dropdown-divider"></div>
                        </li>
                        <li>
                            <h6 class="dropdown-header">Switch Teams</h6>
                        </li>
                        <li>
                            <div class="dropdown-divider"></div>
                        </li>
                    <?php endif; ?>
                    <?php if(Auth::user()): ?>
                        <?php $__currentLoopData = Auth::user()->allTeams(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $team): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                <?php endif; ?>
                <li>
                    <div class="dropdown-divider"></div>
                </li>
                <li>
                    <a class="dropdown-item" href="<?php echo e(route('auth-logout')); ?>"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class='ti ti-logout me-2'></i>
                        <span class="align-middle">Logout</span>
                    </a>
                </li>
                <form method="POST" id="logout-form" action="<?php echo e(route('auth-logout')); ?>">
                    <?php echo csrf_field(); ?>
                </form>
            </ul>
        </li>
        <!--/ User -->
    </ul>
</div>
<!-- Search Small Screens -->
<div class="navbar-search-wrapper search-input-wrapper <?php echo e(isset($menuHorizontal) ? $containerNav : ''); ?> d-none">
    <input type="text"
        class="form-control search-input <?php echo e(isset($menuHorizontal) ? '' : $containerNav); ?> border-0"
        placeholder="Search..." aria-label="Search...">
    <i class="ti ti-x ti-sm search-toggler cursor-pointer"></i>
</div>
<?php if(isset($navbarDetached) && $navbarDetached == ''): ?>
    </div>
<?php endif; ?>
</nav>

<?php $__env->startPush('pricing-script'); ?>
    <script src="<?php echo e(asset('assets/js/pages-pricing.js')); ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('/status-domain')
                .then(response => response.json())
                .then(data => {
                    if (data.license_active) {
                        const expirationDate = data.expiration_date;
                        const [year, month, day] = expirationDate.split('-');
                        const formattedDate = `${day}/${month}/${year}`;
                        const expirationElement = document.getElementById('expiration-date');
                        if (expirationElement) {
                            expirationElement.textContent = `Expiração: ${formattedDate}`;
                        }

                        // Verificar se a licença está expirada
                        const today = new Date();
                        const todayDateOnly = new Date(today.getFullYear(), today.getMonth(), today.getDate());
                        const expiration = new Date(year, month - 1, day); // Mês é zero-indexado em JavaScript
                        if (todayDateOnly > expiration) {
                            //         // Redirecionar para a página de login se a licença estiver expirada
                            window.location.href = '/auth/login-basic';
                        }
                    } else {
                        //     // Redirecionar para a página de login se a licença não estiver ativa
                        window.location.href = '/auth/login-basic';
                    }
                })
                .catch(error => console.error('Erro ao obter a data de expiração da licença:', error));
        });
    </script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('_partials/_modals/modal-pricing', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<!-- / Navbar --><?php /**PATH /home/gestor-pro.vipconnect.top/public_html/resources/views/layouts/sections/navbar/navbar.blade.php ENDPATH**/ ?>