@php
$containerFooter = (isset($configData['contentLayout']) && $configData['contentLayout'] === 'compact') ? 'container-xxl' : 'container-fluid';
@endphp

<!-- Footer-->
<footer class="content-footer footer bg-footer-theme">
  <div class="{{ $containerFooter }}">
    <div class="footer-container d-flex align-items-center justify-content-between py-2 flex-md-row flex-column">
      <div>
        © <script>
          document.write(new Date().getFullYear())
        </script>
        <a href="{{ (!empty(config('variables.creatorUrl')) ? config('variables.creatorUrl') : '') }}" target="_blank" class="fw-medium">{{ (!empty(config('variables.templateName')) ? config('variables.templateName') : '') }}</a>
        <span class="footer-text"> | Versão {{ env('APP_VERSION', '1.0.0') }}</span>
      </div>
    </div>
  </div>
</footer>
<!--/ Footer-->
