
@extends('layouts/layoutMaster')

@section('title', 'Configurações')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave-phone.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/app-ecommerce-settings.js')}}"></script>
@endsection

@section('content')
<h4 class="py-3 mb-4">
<span class="text-muted fw-light">{{ config('variables.templateName', 'TemplateName') }} / </span> Configurações
</h4>

@if (session('success'))
  <div class="alert alert-success alert-dismissible">
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    {{ session('success') }}
  </div>
@endif

@if (session('error'))
  <div class="alert alert-danger alert-dismissible">
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    {{ session('error') }}
  </div>
@endif

<div class="row g-4">

  <!-- Navigation -->
  <div class="col-12 col-lg-4">
    <div class="d-flex justify-content-between flex-column mb-3 mb-md-0">
      <ul class="nav nav-align-left nav-pills flex-column">
        <li class="nav-item mb-1">
          <a class="nav-link py-2 active" href="javascript:void(0);">
            <i class="ti ti-building-store me-2"></i>
            <span class="align-middle">Detalhes da Empresa</span>
          </a>
        </li>
      </ul>
    </div>
   
  </div>
  <!-- /Navigation -->

  @php
  $publicKey = config('mercado_pago.public_key');
  $accessToken = config('mercado_pago.access_token');
  $siteId = config('mercado_pago.site_id');
  @endphp

  <!-- Options -->
  <div class="col-12 col-lg-8 pt-4 pt-lg-0">
    <div class="tab-content p-0">
      <!-- Store Details Tab -->
      <div class="tab-pane fade show active" id="store_details" role="tabpanel">
        @if(isset($companyDetails))
        <form action="{{ route('configuracoes.update', $companyDetails->id) }}" method="POST" enctype="multipart/form-data">
          @method('PUT')
        @else
        <form action="{{ route('configuracoes.store') }}" method="POST" enctype="multipart/form-data">
        @endif
          @csrf
          <div class="card mb-4">
            <div class="card-header">
              <h5 class="card-title m-0">Empresa</h5>
            </div>
            <div class="card-body">
              <div class="row mb-3 g-3">
                <div class="col-12 col-md-6">
                  <label class="form-label mb-0" for="ecommerce-settings-details-name">Nome da Empresa <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="ecommerce-settings-details-name" placeholder="Nome da Empresa" name="company_name" aria-label="Nome da Empresa" value="{{ $companyDetails->company_name ?? '' }}" required>
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label mb-0" for="ecommerce-settings-details-phone">WhatsApp da Empresa <span class="text-danger">*</span></label>
                  <input type="tel" class="form-control" id="ecommerce-settings-details-phone" placeholder="98933332211" name="company_whatsapp" aria-label="WhatsApp da Empresa" value="{{ $companyDetails->company_whatsapp ?? '' }}" required>
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label mb-0" for="ecommerce-settings-access-token">Access Token <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="ecommerce-settings-access-token" placeholder="Access Token" name="access_token" aria-label="Access Token" value="{{ $companyDetails->access_token ?? '' }}" required>
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label mb-0" for="ecommerce-settings-company-logo">Logotipo da Empresa <span class="text-danger">*</span></label>
                  <input type="file" class="form-control" id="ecommerce-settings-company-logo" name="company_logo" aria-label="Logotipo da Empresa" @if(!isset($companyDetails->company_logo))  @endif>
                  @if(isset($companyDetails->company_logo))
                  <img src="{{ asset($companyDetails->company_logo) }}" alt="Company Logo" width="100" class="mt-2">
                  @endif
                </div>
                <div class="col-12 col-md-6">
                  <label class="form-label mb-0" for="ecommerce-settings-favicon">Favicon <span class="text-danger">*</span></label>
                  <input type="file" class="form-control" id="ecommerce-settings-favicon" name="favicon" aria-label="Favicon" @if(!isset($companyDetails->favicon))  @endif>
                  @if(isset($companyDetails->favicon))
                  <img src="{{ asset($companyDetails->favicon) }}" alt="Favicon" width="32" class="mt-2">
                  @endif
              </div>

                <div class="col-12 col-md-6">
                  <label class="form-label mb-0" for="ecommerce-settings-pix-manual">PIX Manual <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="ecommerce-settings-pix-manual" placeholder="Chave PIX" name="pix_manual" aria-label="PIX Manual" value="{{ $companyDetails->pix_manual ?? '' }}" >
                </div>

                                      <!-- Campo para definir se deve usar o gateway de pagamento -->
                    <div class="col-12 col-md-6">
                        <label class="form-label mb-0" for="ecommerce-settings-not-gateway">Método de Cobrança</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="not_gateway" value="0">
                            <input class="form-check-input" type="checkbox" id="ecommerce-settings-not-gateway" name="not_gateway" value="1" {{ isset($companyDetails->not_gateway) && $companyDetails->not_gateway ? 'checked' : '' }}>
                            <label class="form-check-label" for="ecommerce-settings-not-gateway">Usar chave PIX manual para cobranças</label>
                        </div>
                        <small class="form-text text-muted">
                            Marque esta opção para usar a chave PIX manual para processar cobranças manualmente. Desmarque para usar o Mercado Pago para cobranças automáticas.
                        </small>
                    </div>

                @if (Auth::check() && Auth::user()->role_id == 1)
                <div class="col-12 col-md-6">
                  <label class="form-label mb-0" for="ecommerce-settings-referral-balance">Saldo de Indicações</label>
                  <input type="text" class="form-control" id="ecommerce-settings-referral-balance" placeholder="Saldo de Indicações" name="referral_balance" aria-label="Saldo de Indicações" value="{{ $companyDetails->referral_balance ?? '' }}">
                </div>

                <div class="col-12 col">
                  <label class="form-label mb-0" for="ecommerce-settings-api-session">API ipinfo.io</label>
                  <input type="text" class="form-control" id="ecommerce-settings-api-session" placeholder="API Session" name="api_session" aria-label="token" value="{{ $companyDetails->api_session ?? '' }}">
                  <small class="form-text text-muted">
                    Não tem uma API? <a href="https://ipinfo.io/account/home?service=google&loginState=create" target="_blank">Crie sua conta no ipinfo.io</a>
                  </small>
                </div>

                <!-- Nova entrada para public_key -->
                <div class="col-12 col-md-6">
                  <label class="form-label mb-0" for="ecommerce-settings-public-key">Public Key</label>
                  <input type="text" class="form-control" id="ecommerce-settings-public-key" placeholder="Public Key" name="public_key" aria-label="Public Key" value="{{ $companyDetails->public_key ?? '' }}">
                </div>

                <!-- Nova entrada para site_id -->
                <div class="col-12 col-md-6">
                  <label class="form-label mb-0" for="ecommerce-settings-site-id">Site ID</label>
                  <input type="text" class="form-control" id="ecommerce-settings-site-id" placeholder="Site ID" name="site_id" aria-label="Site ID" value="{{ $companyDetails->site_id ?? 'MLB' }}">
                </div>

                <!-- Nova entrada para evolution_api_url -->
                <div class="col-12 col-md-6">
                  <label class="form-label mb-0" for="ecommerce-settings-evolution-api-url">Evolution API URL</label>
                  <input type="text" class="form-control" id="ecommerce-settings-evolution-api-url" placeholder="Evolution API URL" name="evolution_api_url" aria-label="Evolution API URL" value="{{ $companyDetails->evolution_api_url ?? '' }}">
                </div>

                <!-- Nova entrada para evolution_api_key -->
                <div class="col-12 col-md-6">
                  <label class="form-label mb-0" for="ecommerce-settings-evolution-api-key">Evolution API Key</label>
                  <input type="text" class="form-control" id="ecommerce-settings-evolution-api-key" placeholder="Evolution API Key" name="evolution_api_key" aria-label="Evolution API Key" value="{{ $companyDetails->evolution_api_key ?? '' }}">
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label mb-0" for="ecommerce-settings-notification-url">Notification URL <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="ecommerce-settings-notification-url" placeholder="Notification URL" name="notification_url" aria-label="Notification URL" value="{{ url('/webhook/mercadopago') }}" required>
              </div>
                @endif
              </div>
            </div>
          </div>
          <div class="d-flex justify-content-end gap-3">
            @if(isset($companyDetails))
            <button type="submit" class="btn btn-primary">Atualizar</button>
          </form>
          <form action="{{ route('configuracoes.destroy', $companyDetails->id) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Deletar</button>
          </form>
          @else
          <button type="reset" class="btn btn-label-secondary">Descartar</button>
          <button type="submit" class="btn btn-primary">Salvar</button>
          @endif
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- /Options-->
</div>

@endsection
