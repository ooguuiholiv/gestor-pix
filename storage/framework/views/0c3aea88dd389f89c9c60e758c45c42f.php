<?php if(isset($pageConfigs)): ?>
<?php echo Helper::updatePageConfig($pageConfigs); ?>

<?php endif; ?>
<?php
$configData = Helper::appClasses();
?>

<?php if(isset($configData["layout"])): ?>
<?php echo $__env->make((( $configData["layout"] === 'horizontal') ? 'layouts.horizontalLayout' :
(( $configData["layout"] === 'blank') ? 'layouts.blankLayout' :
(($configData["layout"] === 'front') ? 'layouts.layoutFront' : 'layouts.contentNavbarLayout') )), \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>

<!-- BEGIN: Content-->

<link rel="stylesheet" href="<?php echo e(asset('assets/vendor/fonts/boxicons.css')); ?>" />
<link rel="stylesheet" href="<?php echo e(asset('assets/css/demo.css')); ?>" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
<link rel="stylesheet" href="<?php echo e(asset('assets/css/custom.css')); ?>" />


<!-- Vendors CSS -->
<link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')); ?>" />
<link rel="stylesheet" href="<?php echo e(asset('assets/vendor/css/pages/page-auth.css')); ?>" />
<link rel="stylesheet" href="<?php echo e(asset('assets/css/apex-charts.css')); ?>" />
<link rel="stylesheet" href="<?php echo e(asset('assets/lightbox/lightbox.min.css')); ?>" />

<!-- Page CSS -->
<!-- Helpers -->
<script src="<?php echo e(asset('assets/vendor/js/helpers.js')); ?>"></script>

<!-- Date picker -->
<link rel="stylesheet" href="<?php echo e(asset('assets/css/daterangepicker.css')); ?>" />
<link rel="stylesheet" href="<?php echo e(asset('assets/css/bootstrap-datetimepicker.min.css')); ?>" />
<link href="<?php echo e(asset('assets/css/select2.min.css')); ?>" rel="stylesheet" />
<link href="<?php echo e(asset('assets/css/bootstrap-table.min.css')); ?>" rel="stylesheet" />
<link href="<?php echo e(asset('assets/css/dragula.css')); ?>" rel="stylesheet" />
<link href="<?php echo e(asset('assets/css/toastr.min.css')); ?>" rel="stylesheet" />
<link href="<?php echo e(asset('assets/css/dropzone.min.css')); ?>" rel="stylesheet" />
<link href="<?php echo e(asset('assets/css/fullcalendar/core/main.css')); ?>" rel="stylesheet" />
<link href="<?php echo e(asset('assets/css/fullcalendar/daygrid/main.css')); ?>" rel="stylesheet" />
<link href="<?php echo e(asset('assets/css/fullcalendar/list/main.css')); ?>" rel="stylesheet" />
<link href="<?php echo e(asset('assets/css/frappe-gannt.css')); ?>" rel="stylesheet" />

<!-- Main JS -->
<script src="<?php echo e(asset('assets/js/ui-toasts.js')); ?>"></script>
<!-- Place this tag in your head or just before your close body tag. -->
<script async defer src="<?php echo e(asset('assets/js/buttons.js')); ?>"></script>
<script>
    var toastTimeOut = 5; // Valor padrão
    var toastPosition = 'toast-top-right'; // Valor padrão
    var csrf_token = '<?php echo e(csrf_token()); ?>';
    var js_date_format = 'DD/MM/YYYY'; // Formato de data padrão para BR
</script>
<!-- Toastr -->
<script src="<?php echo e(asset('assets/js/toastr.min.js')); ?>"></script>
<!-- TinyMCE -->
<script src="<?php echo e(asset('assets/js/tinymce.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/tinymce-jquery.min.js')); ?>"></script>
<!-- Custom JS -->
<script src="<?php echo e(asset('assets/js/custom.js')); ?>"></script>
<?php if(session()->has('message')): ?>
<script>
    toastr.options = {
        "positionClass": toastPosition,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": parseFloat(toastTimeOut) * 1000,
        "progressBar": true,
        "extendedTimeOut": "1000",
        "closeButton": true
    };
    toastr.success('<?php echo e(session('message')); ?>', 'Success');
</script>
<?php elseif(session()->has('error')): ?>
<script>
    toastr.options = {
        "positionClass": toastPosition,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": parseFloat(toastTimeOut) * 1000,
        "progressBar": true,
        "extendedTimeOut": "1000",
        "closeButton": true
    };
    toastr.error('<?php echo e(session('error')); ?>', 'Error');
</script>
<?php endif; ?>
<!-- select 2 js -->
<script src="<?php echo e(asset('assets/js/select2.min.js')); ?>"></script>
<!-- Bootstrap-table -->
<script src="<?php echo e(asset('assets/js/bootstrap-table/bootstrap-table.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/bootstrap-table/bootstrap-table-export.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/bootstrap-table/tableExport.min.js')); ?>"></script>
<!-- Dragula -->
<script src="<?php echo e(asset('assets/js/dragula.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/popper.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/tinymce.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/tinymce-jquery.min.js')); ?>"></script>
<!-- Date picker -->
<script src="<?php echo e(asset('assets/js/moment.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/daterangepicker.js')); ?>"></script>
<script src="<?php echo e(asset('assets/lightbox/lightbox.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/dropzone.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/extended-ui-perfect-scrollbar.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/fullcalendar/core/main.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/fullcalendar/interaction/main.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/fullcalendar/daygrid/main.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/fullcalendar/list/main.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/fullcalendar/google-calendar/main.js')); ?>"></script>

<?php /**PATH /home/gestor-pro.vipconnect.top/public_html/resources/views/layouts/layoutMaster.blade.php ENDPATH**/ ?>