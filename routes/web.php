<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\language\LanguageController;
use App\Http\Controllers\layouts\CollapsedMenu;
use App\Http\Controllers\layouts\ContentNavbar;
use App\Http\Controllers\layouts\ContentNavSidebar;
use App\Http\Controllers\layouts\NavbarFull;
use App\Http\Controllers\layouts\NavbarFullSidebar;
use App\Http\Controllers\layouts\Horizontal;
use App\Http\Controllers\layouts\Vertical;
use App\Http\Controllers\layouts\WithoutMenu;
use App\Http\Controllers\layouts\WithoutNavbar;
use App\Http\Controllers\layouts\Fluid;
use App\Http\Controllers\layouts\Container;
use App\Http\Controllers\layouts\Blank;
use App\Http\Controllers\front_pages\Landing;
use App\Http\Controllers\front_pages\Pricing;
use App\Http\Controllers\front_pages\Payment;
use App\Http\Controllers\front_pages\Checkout;
use App\Http\Controllers\front_pages\HelpCenter;
use App\Http\Controllers\front_pages\HelpCenterArticle;
use App\Http\Controllers\apps\Chat;
use App\Http\Controllers\apps\Kanban;
use App\Http\Controllers\apps\EcommerceDashboard;
use App\Http\Controllers\apps\EcommerceProductList;
use App\Http\Controllers\apps\EcommerceProductAdd;
use App\Http\Controllers\apps\EcommerceProductCategory;
use App\Http\Controllers\apps\EcommerceOrderList;
use App\Http\Controllers\apps\EcommerceOrderDetails;
use App\Http\Controllers\apps\EcommerceCustomerAll;
use App\Http\Controllers\apps\EcommerceReferrals;
use App\Http\Controllers\apps\EcommerceSettingsDetails;
use App\Http\Controllers\apps\EcommerceSettingsPayments;
use App\Http\Controllers\apps\InvoiceList;
use App\Http\Controllers\apps\InvoicePreview;
use App\Http\Controllers\apps\InvoicePrint;
use App\Http\Controllers\apps\InvoiceEdit;
use App\Http\Controllers\apps\InvoiceAdd;
use App\Http\Controllers\apps\UserViewAccount;
use App\Http\Controllers\apps\UserViewSecurity;
use App\Http\Controllers\apps\UserViewBilling;
use App\Http\Controllers\apps\UserViewNotifications;
use App\Http\Controllers\apps\UserViewConnections;
use App\Http\Controllers\apps\AccessRoles;
use App\Http\Controllers\pages\UserProfile;
use App\Http\Controllers\authentications\LoginBasic;
use App\Http\Controllers\authentications\RegisterBasic;
use App\Http\Controllers\authentications\ResetPasswordBasic;
use App\Http\Controllers\authentications\ResetPasswordCover;
use App\Http\Controllers\authentications\ForgotPasswordBasic;
use App\Http\Controllers\authentications\ForgotPasswordCover;
use App\Http\Controllers\icons\Tabler;
use App\Http\Controllers\icons\FontAwesome;
use App\Http\Controllers\form_layouts\VerticalForm;
use App\Http\Controllers\form_layouts\HorizontalForm;
use App\Http\Controllers\form_layouts\StickyActions;
use App\Http\Controllers\charts\ApexCharts;
use App\Http\Controllers\charts\ChartJs;




//comeca aqui minha parte
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AccessRoleController;

use App\Http\Controllers\ServidorController;

use App\Http\Controllers\ClienteController;
// WHATSAPP
use App\Http\Controllers\ConexaoController;

// Planos
use App\Http\Controllers\PlanoController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\SendMessageController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\PlanoRenovacaoController;

use App\Http\Controllers\PaymentController;

use App\Http\Controllers\apps\UserList;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\RevendaController;
use App\Http\Controllers\RevendedorUserController;
use App\Http\Controllers\ClienteAuthController;
use App\Http\Controllers\ClientDashboardController;

use App\Http\Controllers\CampanhaController;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\PreferenceController;

use App\Http\Controllers\ScheduleSettingController;
use App\Http\Controllers\PluginController;
use App\Http\Controllers\PluginInstallController;
use App\Http\Controllers\XtreamClientController;


Route::middleware(['auth'])->group(function () {
  Route::get('/user', [UserController::class, 'index'])->name('user.index');
  Route::get('/user/edit', [UserController::class, 'edit'])->name('user.edit');
  Route::post('/user/update', [UserController::class, 'update'])->name('user.update');

  Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
  Route::get('/admin/edit/{id}', [AdminController::class, 'edit'])->name('admin.edit');
  Route::post('/admin/update/{id}', [AdminController::class, 'update'])->name('admin.update');
  Route::delete('/admin/destroy/{id}', [AdminController::class, 'destroy'])->name('admin.destroy');
});


Route::get('/', function () {
  return redirect()->route('front-pages-landing');
});

Route::get('/auth/login-basic', [LoginBasic::class, 'index'])->name('auth-login-basic');
Route::post('/auth/login-basic', [LoginBasic::class, 'login'])->name('auth-login-basic-post')->middleware('throttle:5,1');
Route::post('/auth/logout', [LoginBasic::class, 'logout'])->name('auth-logout');

// Rotas clientes Auth
Route::get('/client/login', [ClienteAuthController::class, 'showClientLoginForm'])->name('client.login.form');
Route::post('/client/login', [ClienteAuthController::class, 'clientLogin'])->name('client.login');




Route::middleware(['auth', 'role:admin'])->group(function () {
  Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
  })->name('admin.dashboard');
});

Route::middleware(['auth', 'role:cliente'])->group(function () {
  Route::get('/cliente/dashboard', function () {
    return view('cliente.dashboard');
  })->name('cliente.dashboard');
});


//ROTA reset

Route::get('auth/forgot-password-basic', [ForgotPasswordBasic::class, 'index'])->name('auth.forgot-password-basic');
Route::post('auth/forgot-password-basic', [ForgotPasswordBasic::class, 'sendResetPassword'])->name('auth.send-reset-password');
//PLUGINS
Route::post('auth/two-factor', [LoginBasic::class, 'verifyTwoFactor'])->name('auth.verify-two-factor');

Route::get('/plugins', [PluginController::class, 'index'])->name('plugins.index');
Route::get('/plugins/create', [PluginController::class, 'create'])->name('plugins.create');
Route::resource('plugins', PluginController::class);
Route::post('/plugins/initiate-purchase', [PluginController::class, 'initiatePurchase'])->name('plugins.initiatePurchase');
Route::post('/plugins/checkPaymentStatus', [PluginController::class, 'checkPaymentStatus'])->name('plugins.checkPaymentStatus');


Route::any('/plugins/gera-new', [PluginInstallController::class, 'GeraNew'])->name('plugins.geraNew');


//CRON

Route::get('/run-scheduled-tasks', function () {
  Artisan::call('clientes:verificar-vencidos');
  return 'Scheduled tasks executed';
})->name('run-scheduled-tasks');



Route::get('/run-campanhas', function () {
  Artisan::call('campanhas:disparar');
  return 'Campanhas executed';
})->name('run-campanhas');




Route::get('/migrar', function () {
  $output = shell_exec('php ' . base_path('run_migrations.php'));
  return "<pre>$output</pre>";
})->name('migrar');



Route::delete('/servidores/deletarMultiplos', [ServidorController::class, 'deletarMultiplos'])->name('servidores.deletarMultiplos');
Route::delete('/planos/destroy-multiple', [PlanoController::class, 'destroyMultiple'])->name('planos.destroy_multiple');
Route::delete('/templates/deletes-multiple', [TemplateController::class, 'destroy_multiple'])->name('templates.destroy-multiple');
Route::delete('/ordens/destroy-multiple', [EcommerceOrderList::class, 'destroyMultiple'])->name('ordens.destroy_multiple');

Route::get('/revenda', [RevendaController::class, 'index'])->name('revenda.index');
Route::post('/revenda/store', [RevendaController::class, 'store'])->name('revenda.store');
Route::put('/revenda/update/{id}', [RevendaController::class, 'update'])->name('revenda.update');
Route::delete('/revenda/destroy/{id}', [RevendaController::class, 'destroy'])->name('revenda.destroy');
Route::get('/revenda/list', [RevendaController::class, 'list'])->name('revenda.list');
Route::post('/revenda/destroy-multiple', [RevendaController::class, 'destroyMultiple'])->name('revenda.destroyMultiple');



Route::get('/status-domain', [LicenseController::class, 'statusDomain']);
Route::post('/verify-license', [LicenseController::class, 'verifyLicense'])->name('verify-license');
Route::post('/activate-module', [LicenseController::class, 'ActiveModele'])->name('activate-module');
Route::post('/verify-module-license-status', [LicenseController::class, 'verifyModuleLicenseStatus'])->name('verify-module-license-status');
Route::get('/api/updates', [LicenseController::class, 'fetchUpdates'])->name('api.updates');
Route::post('/api/start-update', [LicenseController::class, 'startUpdate'])->name('api.startUpdate');
Route::post('/api/extractAndUpdate', [LicenseController::class, 'extractAndUpdate'])->name('api.extractAndUpdate');





Route::get('/revendedores', [RevendedorUserController::class, 'index'])->name('revendedores.index');
Route::get('/revendedores/list', [RevendedorUserController::class, 'list'])->name('revendedores.list');
Route::get('/revendedores/create', [RevendedorUserController::class, 'create'])->name('revendedores.create');
Route::post('/revendedores/store', [RevendedorUserController::class, 'store'])->name('revendedores.store');
Route::get('/revendedores/edit/{id}', [RevendedorUserController::class, 'edit'])->name('revendedores.edit');
Route::put('/revendedores/update/{id}', [RevendedorUserController::class, 'update'])->name('revendedores.update');
Route::delete('/revendedores/destroy/{id}', [RevendedorUserController::class, 'destroy'])->name('revendedores.destroy');
Route::post('/revendedores/ativar/{id}', [RevendedorUserController::class, 'ativar'])->name('revendedores.ativar');
Route::post('/revendedores/desativar/{id}', [RevendedorUserController::class, 'desativar'])->name('revendedores.desativar');
Route::delete('/revendedores/destroy-multiple', [RevendedorUserController::class, 'destroyMultiple'])->name('revendedores.destroy_multiple');

Route::get('/api/transactions', [TransactionController::class, 'filter']);
Route::get('/api/earning-reports', [TransactionController::class, 'earningReports']);

//rota TEMPLATE

Route::resource('templates', TemplateController::class)->except(['show'])->middleware('auth');
// Rota para listar os templates
Route::get('/templates/list', [TemplateController::class, 'list'])->name('templates.list');




// Rota para salvar as configurações de agendamento
Route::post('/schedule-settings', [ScheduleSettingController::class, 'store'])->name('schedule-settings.store')->middleware('auth');
Route::get('/manage-templates', [ScheduleSettingController::class, 'index'])->name('manage-templates.index')->middleware('auth');

// Rota para deletar uma configuração específica
Route::delete('/schedule-settings/{id}', [ScheduleSettingController::class, 'destroy'])->name('schedule-settings.destroy')->middleware('auth');
Route::get('/manage-templates/list', [ScheduleSettingController::class, 'list'])->name('manage-templates.list')->middleware('auth');
Route::delete('/manage-templates/delete-multiple', [ScheduleSettingController::class, 'destroyMultiple'])->name('manage-templates.destroy-multiple')->middleware('auth');
// Outras rotas existentes
Route::get('/dashboard/analytics', [Analytics::class, 'index'])->name('dashboard-analytics');
Route::get('/dashboard/crm', [Crm::class, 'index'])->name('dashboard-crm');
// locale
Route::get('lang/{locale}', [LanguageController::class, 'swap']);






// layout
Route::get('/layouts/collapsed-menu', [CollapsedMenu::class, 'index'])->name('layouts-collapsed-menu');
Route::get('/layouts/content-navbar', [ContentNavbar::class, 'index'])->name('layouts-content-navbar');
Route::get('/layouts/content-nav-sidebar', [ContentNavSidebar::class, 'index'])->name('layouts-content-nav-sidebar');
Route::get('/layouts/navbar-full', [NavbarFull::class, 'index'])->name('layouts-navbar-full');
Route::get('/layouts/navbar-full-sidebar', [NavbarFullSidebar::class, 'index'])->name('layouts-navbar-full-sidebar');
Route::get('/layouts/horizontal', [Horizontal::class, 'index'])->name('dashboard-analytics');
Route::get('/layouts/vertical', [Vertical::class, 'index'])->name('dashboard-analytics');
Route::get('/layouts/without-menu', [WithoutMenu::class, 'index'])->name('layouts-without-menu');
Route::get('/layouts/without-navbar', [WithoutNavbar::class, 'index'])->name('layouts-without-navbar');
Route::get('/layouts/fluid', [Fluid::class, 'index'])->name('layouts-fluid');
Route::get('/layouts/container', [Container::class, 'index'])->name('layouts-container');
Route::get('/layouts/blank', [Blank::class, 'index'])->name('layouts-blank');

//TELA INICIAL
Route::get('/front-pages/landing', [Landing::class, 'index'])->name('front-pages-landing');
Route::get('/front-pages/pricing', [Pricing::class, 'index'])->name('front-pages-pricing');
Route::get('/front-pages/payment', [Payment::class, 'index'])->name('front-pages-payment');
Route::get('/front-pages/checkout', [Checkout::class, 'index'])->name('front-pages-checkout');
Route::get('/front-pages/help-center', [HelpCenter::class, 'index'])->name('front-pages-help-center');
Route::get('/front-pages/help-center-article', [HelpCenterArticle::class, 'index'])->name('front-pages-help-center-article');

// apps CLIENTES
Route::get('/app/ecommerce/customer/all', [EcommerceCustomerAll::class, 'index'])->name('app-ecommerce-customer-all');
Route::get('/app/ecommerce/customer/list', [EcommerceCustomerAll::class, 'list'])->name('app-ecommerce-customer-list');
Route::delete('/app/ecommerce/customer/destroy_multiple', [EcommerceCustomerAll::class, 'destroy_multiple'])->name('app-ecommerce-customer-destroy-multiple');

Route::post('/clientes/{id}/cobranca-manual', [EcommerceCustomerAll::class, 'cobrancaManual'])->name('app-ecommerce-customer-charge');

Route::post('/app/ecommerce/customer/store', [EcommerceCustomerAll::class, 'store'])->name('app-ecommerce-customer-store');
Route::put('/app/ecommerce/customer/update/{id}', [EcommerceCustomerAll::class, 'update'])->name('app-ecommerce-customer-update');
Route::delete('/app/ecommerce/customer/destroy/{id}', [EcommerceCustomerAll::class, 'destroy'])->name('app-ecommerce-customer-destroy');
Route::get('/send-login-details/{clienteId}', [EcommerceCustomerAll::class, 'sendLoginDetails'])->name('send-login-details');
Route::post('/clientes/import', [ClienteController::class, 'import'])->name('app-ecommerce-customer-import');
Route::get('/clientes/export', [ClienteController::class, 'export'])->name('app-ecommerce-customer-export');

//Servidores
// Route::get('/servidores', [ServidorController::class, 'index'])->name('servidores.index');
// Route::post('/servidores', [ServidorController::class, 'store'])->name('servidores.store');
// Route::put('/servidores/{id}', [ServidorController::class, 'update'])->name('servidores.update');
// Route::delete('/servidores/{id}', [ServidorController::class, 'destroy'])->name('servidores.destroy');

    Route::get('/servidores', [ServidorController::class, 'index'])->name('servidores.index');
    Route::get('/servidores/list', [ServidorController::class, 'list'])->name('servidores.list');
    Route::post('/servidores', [ServidorController::class, 'store'])->name('servidores.store');
    Route::get('/servidores/{id}', [ServidorController::class, 'show'])->name('servidores.show');
    Route::get('/servidores/{id}/edit', [ServidorController::class, 'edit'])->name('servidores.edit');
    Route::put('/servidores/{id}', [ServidorController::class, 'update'])->name('servidores.update');
    Route::delete('/servidores/{id}', [ServidorController::class, 'destroy'])->name('servidores.destroy');

      // User Prefrence
      Route::post('/save-column-visibility', [PreferenceController::class, 'saveColumnVisibility'])->name('preferences.saveColumnVisibility');
     
//Rota Campanhas



Route::resource('campanhas', CampanhaController::class)->middleware('auth');


Route::get('/app/ecommerce/settings/payments', [EcommerceSettingsPayments::class, 'index'])->name('app-ecommerce-settings-payments');
Route::post('/app/ecommerce/settings/payments/upload', [EcommerceSettingsPayments::class, 'uploadModulo'])->name('modulo.upload');

Route::middleware(['auth:cliente'])->group(function () {
  Route::get('/client/dashboard', [ClientDashboardController::class, 'index'])->name('client.dashboard');
  Route::get('cliente/comprovantes', [ClientDashboardController::class, 'showCompras'])->name('cliente.comprovantes');
  Route::get('cliente/planos', [ClientDashboardController::class, 'showPlanos'])->name('cliente.planos');
  // rota process-payment-planos
  Route::post('/process-payment-planos/{clienteId}', [ClientDashboardController::class, 'processPaymentPlanos'])->name('process-payment-planos');
});


// WEBHOOK MERCADOPAGO
Route::post('/webhook/mercadopago', [WebhookController::class, 'handle'])->name('webhook.mercadopago');


Route::get('/admin/sessions', [SessionController::class, 'index'])->name('admin.sessions.index');


//ENVIAR MENSAGENS

Route::post('/send-message', [SendMessageController::class, 'sendMessageWithoutAuth']);
Route::post('/send-media', [SendMessageController::class, 'sendMedia']);

// Planos

// Route::resource('planos', PlanoController::class);

// // Novas rotas
// Route::get('planos/list', [PlanoController::class, 'list'])->name('planos.list');
// Route::delete('planos/destroy-multiple', [PlanoController::class, 'destroyMultiple'])->name('planos.destroy-multiple');


Route::middleware(['auth'])->group(function () {
    Route::get('/planos', [PlanoController::class, 'index'])->name('planos.index');
    Route::get('/planos/list', [PlanoController::class, 'list'])->name('planos.list');
    Route::post('/planos', [PlanoController::class, 'store'])->name('planos.store');
    Route::get('/planos/{plano}/edit', [PlanoController::class, 'edit'])->name('planos.edit');
    Route::put('/planos/{plano}', [PlanoController::class, 'update'])->name('planos.update');
    Route::delete('/planos/{plano}', [PlanoController::class, 'destroy'])->name('planos.destroy');
});
// PROCESSA PAGAMENTOS PIX E CARTAO

Route::post('/process-payment', [PaymentController::class, 'processPayment'])->name('process-payment');
Route::post('/process-payment-creditos', [PaymentController::class, 'processPaymentCreditos'])->name('process-payment-creditos');

//EMPRESAS
Route::get('/configuracoes', [EcommerceSettingsDetails::class, 'index'])->name('configuracoes.index');
Route::post('/configuracoes', [EcommerceSettingsDetails::class, 'store'])->name('configuracoes.store');
Route::put('/configuracoes/{id}', [EcommerceSettingsDetails::class, 'update'])->name('configuracoes.update');
Route::delete('/configuracoes/{id}', [EcommerceSettingsDetails::class, 'destroy'])->name('configuracoes.destroy');

Route::get('/ordens', [EcommerceOrderList::class, 'index'])->name('app-ecommerce-order-list');
Route::get('/ordens/list', [EcommerceOrderList::class, 'list'])->name('ordens.list');
Route::put('/ordens/{order_id}', [EcommerceOrderList::class, 'update'])->name('app-ecommerce-order-update'); // Adiciona a rota para atualizar pedidos
Route::delete('/ordens/{order_id}', [EcommerceOrderList::class, 'destroy'])->name('app-ecommerce-order-destroy');

Route::get('/detalhes', [EcommerceOrderDetails::class, 'index'])->name('app-ecommerce-order-details');




Route::post('/add-payment', [EcommerceOrderDetails::class, 'addPayment'])->name('addPayment');
Route::get('/app/ecommerce/referrals', [EcommerceReferrals::class, 'index'])->name('app-ecommerce-referrals');
Route::post('/app/ecommerce/referrals', [EcommerceReferrals::class, 'create'])->name('app-ecommerce-referrals-create');

//KANBAN



Route::get('/app/kanban', [Kanban::class, 'index'])->name('app-kanban');
Route::post('/user-data', [Kanban::class, 'store'])->name('user-data.store');
Route::put('/user-data/{id}', [Kanban::class, 'update'])->name('user-data.update');
Route::delete('/user-data/{id}', [Kanban::class, 'destroy'])->name('user-data.destroy');


//CHAT
Route::get('/app/chat', [Chat::class, 'index'])->name('app-chat');
Route::get('/chat/messages/{cliente_id}', [Chat::class, 'fetchMessages'])->name('chat.fetchMessages');
Route::post('/chat/send', [Chat::class, 'sendMessage'])->name('chat.sendMessage');



Route::get('/app/ecommerce/dashboard', [EcommerceDashboard::class, 'index'])->name('app-ecommerce-dashboard');
Route::get('/app/ecommerce/product/list', [EcommerceProductList::class, 'index'])->name('app-ecommerce-product-list');
Route::get('/app/ecommerce/product/add', [EcommerceProductAdd::class, 'index'])->name('app-ecommerce-product-add');
Route::get('/app/ecommerce/product/category', [EcommerceProductCategory::class, 'index'])->name('app-ecommerce-product-category');





Route::get('/app/invoice/list', [InvoiceList::class, 'index'])->name('app-invoice-list');
Route::get('/app/invoice/preview', [InvoicePreview::class, 'index'])->name('app-invoice-preview');
Route::get('/app/invoice/edit', [InvoiceEdit::class, 'index'])->name('app-invoice-edit');
Route::get('/app/invoice/add', [InvoiceAdd::class, 'index'])->name('app-invoice-add');

//USUARIOS
Route::get('/app/user/list', [UserList::class, 'index'])->name('app-user-list');
Route::get('/app/user/list/data', [UserList::class, 'list'])->name('app-user-list-data');
Route::delete('/app/user/destroy-multiple', [UserList::class, 'destroyMultiple'])->name('users.destroy_multiple');
// imprimir fatura
// Route::get('/app/invoice/print', [InvoicePrint::class, 'index'])->name('app-invoice-print');
Route::get('/app/invoice/print/{payment_id}', [InvoicePrint::class, 'index']);



Route::resource('users', UserList::class);
Route::get('users/{user}/renew', [UserList::class, 'renew'])->name('users.renew');
Route::post('/users/{user}/renew', [UserList::class, 'renew'])->name('users.renew');


Route::get('/app/user/view/account', [UserViewAccount::class, 'index'])->name('app-user-view-account');
Route::get('/app/user/view/security', [UserViewSecurity::class, 'index'])->name('app-user-view-security');
Route::get('/app/user/view/billing', [UserViewBilling::class, 'index'])->name('app-user-view-billing');
Route::get('/app/user/view/notifications', [UserViewNotifications::class, 'index'])->name('app-user-view-notifications');
Route::get('/app/user/view/connections', [UserViewConnections::class, 'index'])->name('app-user-view-connections');
Route::get('/app/access-roles', [AccessRoles::class, 'index'])->name('app-access-roles');//aria de regras Roles

// pages  Perfil
Route::get('/pages/profile-user', [UserProfile::class, 'index'])->name('pages-profile-user');
// Route::post('/pages/profile-user', [UserProfile::class, 'update'])->name('pages-profile-user-post');
Route::put('/pages/profile-user', [UserProfile::class, 'update'])->name('pages-profile-user-post');


// API WHATSAPP ROTA
Route::get('/create-connection', [ConexaoController::class, 'createConnection'])->name('create-connection');
// Route::get('/update-connection', [ConexaoController::class, 'updateconexao'])->name('update-connection');
Route::get('/update-connection', [ConexaoController::class, 'updateConnection'])->name('update-connection');
Route::delete('/delete-connection/{id}', [ConexaoController::class, 'deleteConnection'])->name('delete-connection');
Route::get('/app/whatsapp', [ConexaoController::class, 'index'])->name('app-whatsapp');






// authentication
Route::get('/auth/register-basic', [RegisterBasic::class, 'index'])->name('auth-register-basic');
Route::post('/auth/register-basic', [RegisterBasic::class, 'register'])->name('auth-register-basic-post');
Route::get('/auth/forgot-password-basic', [ForgotPasswordBasic::class, 'index'])->name('auth-reset-password-basic');

Route::get('/auth/login-basic', [LoginBasic::class, 'index'])->name('auth-login-basic');

Route::get('/auth/reset-password-basic', [ResetPasswordBasic::class, 'index'])->name('auth-reset-password-basic');
Route::get('/auth/reset-password-cover', [ResetPasswordCover::class, 'index'])->name('auth-reset-password-cover');
Route::get('/auth/forgot-password-cover', [ForgotPasswordCover::class, 'index'])->name('auth-forgot-password-cover');




//PLANOS RENOVACAO MASTER
Route::middleware(['auth'])->group(function () {
    Route::get('/planos-renovacao', [PlanoRenovacaoController::class, 'index'])->name('planos-renovacao.index');
    Route::get('/planos-renovacao/create', [PlanoRenovacaoController::class, 'create'])->name('planos-renovacao.create');
    Route::post('/planos-renovacao', [PlanoRenovacaoController::class, 'store'])->name('planos-renovacao.store');
    Route::get('/planos-renovacao/{planoRenovacao}/edit', [PlanoRenovacaoController::class, 'edit'])->name('planos-renovacao.edit');
    Route::put('/planos-renovacao/{id}', [PlanoRenovacaoController::class, 'update'])->name('planos-renovacao.update');
    Route::delete('/planos-renovacao/{id}', [PlanoRenovacaoController::class, 'destroy'])->name('planos-renovacao.destroy');   
    Route::post('/planos-renovacao/destroy-multiple', [PlanoRenovacaoController::class, 'destroyMultiple'])->name('planos-renovacao.destroy');
    Route::get('/planos-renovacao/list', [PlanoRenovacaoController::class, 'list'])->name('planos-renovacao.list');
});
// icons
Route::get('/icons/tabler', [Tabler::class, 'index'])->name('icons-tabler');
Route::get('/icons/font-awesome', [FontAwesome::class, 'index'])->name('icons-font-awesome');


// form layouts
Route::get('/form/layouts-vertical', [VerticalForm::class, 'index'])->name('form-layouts-vertical');
Route::get('/form/layouts-horizontal', [HorizontalForm::class, 'index'])->name('form-layouts-horizontal');
Route::get('/form/layouts-sticky', [StickyActions::class, 'index'])->name('form-layouts-sticky');


// charts
Route::get('/charts/apex', [ApexCharts::class, 'index'])->name('charts-apex');
Route::get('/charts/chartjs', [ChartJs::class, 'index'])->name('charts-chartjs');


