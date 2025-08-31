@extends('layouts/layoutMaster')

@section('title', 'Dashboard')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
    <!-- <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script> -->
@endsection

@section('page-script')
    <script src="{{ asset('assets/js/app-ecommerce-dashboard.js') }}"></script>
    <script src="{{ asset('assets/js/dashboards-crm.js') }}"></script>
          <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('{{ route('api.updates') }}')
                .then(response => response.json())
                .then(data => {
                    console.log('Resposta da API:', data); // Adicionando console.log para ver a resposta da API
                    const updatesList = document.getElementById('updates-list');
                    const updatesSection = document.getElementById('updates-section');
                    updatesList.innerHTML = '';
    
                    const currentVersion = '{{ env('APP_VERSION', '1.0.0') }}'; // Obter a versão atual da aplicação
    
                    // Acessar a versão corretamente
                    const version = data.original ? data.original.version : null;
    
                    if (!version) {
                        updatesList.innerHTML = '<p class="text-muted">Nenhuma atualização disponível no momento.</p>';
                    } else if (compareVersions(version, currentVersion) > 0) {
                        const updateItem = document.createElement('div');
                        updateItem.classList.add('update-item', 'animate__animated', 'animate__fadeIn', 'mb-3', 'p-3', 'border', 'rounded', 'shadow-sm');
                        updateItem.innerHTML = `
                            <h3 class="text-primary">Versão ${version}</h3>
                            <p class="text-warning">Antes de qualquer atualização, recomendamos que faça um backup do seu sistema, incluindo o banco de dados. Não somos responsáveis por backups do seu sistema. É sua responsabilidade manter os backups em dia.</p>
                            <button class="btn btn-success" onclick="startUpdate('${version}')">Iniciar Atualização</button>
                            <div id="progress-container" style="display: none;">
                                <p id="progress-status">Progresso do download:</p>
                                <progress id="progress-bar" value="0" max="100" style="width: 100%;"></progress>
                                <span id="progress-percent">0%</span>
                                <p class="text-warning">Por favor, não recarregue a página até que o processo seja concluído.</p>
                            </div>
                        `;
                        updatesList.appendChild(updateItem);
                        updatesSection.style.display = 'block';
                    } else {
                        updatesList.innerHTML = '<p class="text-muted">Nenhuma atualização disponível no momento.</p>';
                    }
                })
                .catch(error => {
                    console.error('Erro ao buscar atualizações:', error);
                    const updatesList = document.getElementById('updates-list');
                    updatesList.innerHTML = '<p class="text-danger">Erro ao carregar atualizações.</p>';
                });
        });
    
        function compareVersions(v1, v2) {
            if (!v1 || !v2) return 0;
    
            console.log(`Comparando versões: v1 = ${v1}, v2 = ${v2}`);
    
            const v1Parts = v1.split('.').map(Number);
            const v2Parts = v2.split('.').map(Number);
    
            console.log(`Partes de v1: ${v1Parts}`);
            console.log(`Partes de v2: ${v2Parts}`);
    
            for (let i = 0; i < Math.max(v1Parts.length, v2Parts.length); i++) {
                const v1Part = v1Parts[i] || 0;
                const v2Part = v2Parts[i] || 0;
    
                if (v1Part > v2Part) {
                    console.log(`v1Parts[${i}] (${v1Part}) é maior que v2Parts[${i}] (${v2Part})`);
                    return 1;
                }
                if (v1Part < v2Part) {
                    console.log(`v1Parts[${i}] (${v1Part}) é menor que v2Parts[${i}] (${v2Part})`);
                    return -1;
                }
            }
    
            console.log('As versões são iguais');
            return 0;
        }
    
        function startUpdate(version) {
            const progressContainer = document.getElementById('progress-container');
            const progressBar = document.getElementById('progress-bar');
            const progressPercent = document.getElementById('progress-percent');
            const progressStatus = document.getElementById('progress-status');
    
            progressContainer.style.display = 'block';
    
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '{{ route('api.startUpdate') }}', true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
    
            xhr.upload.onprogress = function(event) {
                if (event.lengthComputable) {
                    const percentComplete = (event.loaded / event.total) * 100;
                    progressBar.value = percentComplete;
                    progressPercent.textContent = `${Math.round(percentComplete)}%`;
                }
            };
    
            xhr.onprogress = function(event) {
                if (event.lengthComputable) {
                    const percentComplete = (event.loaded / event.total) * 100;
                    progressBar.value = percentComplete;
                    progressPercent.textContent = `${Math.round(percentComplete)}%`;
                }
            };
    
            xhr.onload = function() {
                if (xhr.status === 200) {
                    const data = JSON.parse(xhr.responseText);
                    console.log('Resposta da API de atualização:', data); // Adicionando console.log para ver a resposta da API
                    if (data.success) {
                        progressStatus.textContent = 'Atualização concluída com sucesso!';
                        progressBar.value = 100;
                        progressPercent.textContent = '100%';
                        toastr.success('Atualização concluída com sucesso!');
                        window.location.reload();
                    } else {
                        toastr.error(`Erro ao iniciar atualização: ${data.message}`);
                        progressContainer.style.display = 'none';
                    }
                } else {
                    toastr.error('Erro ao iniciar atualização.');
                    progressContainer.style.display = 'none';
                }
            };
    
            xhr.onerror = function() {
                toastr.error('Erro ao iniciar atualização.');
                progressContainer.style.display = 'none';
            };
    
            xhr.send(JSON.stringify({ version: version }));
        }
    </script>
@endsection


@section('content')
    <div class="row">

        <!-- Verificação de Mensagens de Sessão -->
        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- mensagens para erros -->
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- mensagens para sucesso -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Exibição de Erros de Validação -->
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif



        <!-- Atualizações Disponíveis -->
        <div class="col-12 mb-4" id="updates-section" style="display: none;">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Atualizações Disponíveis</h5>
                </div>
                <div class="card-body">
                    <div id="updates-list">
                        <p>Carregando atualizações...</p>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Atualizações Disponíveis -->

        <!-- View sales -->
        <div class="col-xl-4 mb-4 col-lg-5 col-12">
            <div class="card">
                <div class="d-flex align-items-end row">
                    <div class="col-7">
                        <div class="card-body text-nowrap">
                            @if ($clienteMaisCompras)
                                <h5 class="card-title mb-0">Parabéns {{ $clienteMaisCompras->nome }}! 🎉</h5>
                                <p class="mb-2">Melhor comprador do mês</p>
                                <h4 class="text-primary mb-1">
                                    R${{ number_format($totalComprasClienteMaisCompras, 2, ',', '.') }}</h4>
                                <a href="{{ route('app-ecommerce-order-list', ['order_id' => $clienteMaisCompras->id]) }}"
                                    class="btn btn-primary">Ver Compras</a>
                            @else
                                <h5 class="card-title mb-0">Sem compras registradas ainda.</h5>
                                <p class="mb-2">Melhor comprador do mês</p>
                                <h4 class="text-primary mb-1">R$0,00</h4>
                            @endif
                        </div>
                    </div>
                    <div class="col-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="{{ asset('assets/img/illustrations/card-advance-sale.png') }}" height="140"
                                alt="view sales">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- View sales -->

        <!-- Estatísticas -->
        <div class="col-xl-8 mb-4 col-lg-7 col-12">
            <div class="card h-100">
                <div class="card-header">
                    <div class="d-flex justify-content-between mb-3">
                        <h5 class="card-title mb-0">Estatísticas</h5>
                        <!-- <small class="text-muted">Atualizado há 1 mês</small> -->
                    </div>
                </div>
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-md-3 col-6">
                            <div class="d-flex align-items-center">
                                <div class="badge rounded-pill bg-label-primary me-3 p-2"><i class="ti ti-users ti-sm"></i>
                                </div>
                                <div class="card-info">
                                    <h5 class="mb-0">{{ number_format($totalClientes, 0, ',', '.') }}</h5>
                                    <small>Total de clientes</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="d-flex align-items-center">
                                <div class="badge rounded-pill bg-label-info me-3 p-2"><i
                                        class="ti ti-chart-pie-2 ti-sm"></i></div>
                                <div class="card-info">
                                    <h5 class="mb-0">{{ number_format($inadimplentes, 0, ',', '.') }}</h5>
                                    <small>Inadimplentes</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="d-flex align-items-center">
                                <div class="badge rounded-pill bg-label-danger me-3 p-2"><i
                                        class="ti ti-shopping-cart ti-sm"></i></div>
                                <div class="card-info">
                                    <h5 class="mb-0">{{ number_format($ativos, 0, ',', '.') }}</h5>
                                    <small>Ativos</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="d-flex align-items-center">
                                <div class="badge rounded-pill bg-label-success me-3 p-2"><i
                                        class="ti ti-currency-dollar ti-sm"></i></div>
                                <div class="card-info">
                                    <h5 class="mb-0">{{ number_format($expiramHoje, 0, ',', '.') }}</h5>
                                    <small>Expiram hoje</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Estatísticas -->


        <!-- Estatísticas Detalhadas -->
        <div class="col-12 col-xl-8 mb-4">
            <div class="card">
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-md-8 position-relative p-4">
                            <div class="card-header d-inline-block p-0 text-wrap position-absolute">
                                <h5 class="m-0 card-title">Estatísticas Detalhadas</h5>
                            </div>
                            <br>
                            <div id="detailedStatisticsChart" class="mt-n1"></div>
                        </div>
                        <div class="col-md-4 p-4">
                            <div class="text-center mt-4">
                                <div class="dropdown">
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="statisticsYear">
                                        <li><a class="dropdown-item prev-year1" href="javascript:void(0);">
                                                <script>
                                                    document.write(new Date().getFullYear() - 1)
                                                </script>
                                            </a></li>
                                        <li><a class="dropdown-item prev-year2" href="javascript:void(0);">
                                                <script>
                                                    document.write(new Date().getFullYear() - 2)
                                                </script>
                                            </a></li>
                                        <li><a class="dropdown-item prev-year3" href="javascript:void(0);">
                                                <script>
                                                    document.write(new Date().getFullYear() - 3)
                                                </script>
                                            </a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--/ Estatísticas Detalhadas -->

        <script>
            var estatisticas = {
                totalClientes: @json($totalClientes),
                inadimplentes: @json($inadimplentes),
                ativos: @json($ativos),
                expiramHoje: @json($expiramHoje)
            };

            document.addEventListener('DOMContentLoaded', function() {
                const detailedStatisticsChartEl = document.querySelector('#detailedStatisticsChart');

                // console.log("estatisticas", estatisticas);

                // Defina cores manualmente para os modos
                const chartTextColor = '#333'; // Cor para o modo claro
                const chartFillColors = ['#008ffb', '#33FF57', '#feb019', '#ff4560']; // Cores dos dados
                const legendTextColor = '#a2a2a2'; // Cor para o texto da legenda
                const axisTextColor = '#a2a2a2'; // Cor para o texto dos eixos e títulos

                const detailedStatisticsChartOptions = {
                    series: [{
                            name: 'Total de Clientes',
                            data: [estatisticas.totalClientes]
                        },
                        {
                            name: 'Inadimplentes',
                            data: [estatisticas.inadimplentes]
                        },
                        {
                            name: 'Ativos',
                            data: [estatisticas.ativos]
                        },
                        {
                            name: 'Expiram Hoje',
                            data: [estatisticas.expiramHoje]
                        }
                    ],
                    chart: {
                        height: 350,
                        type: 'bar',
                        toolbar: {
                            show: false
                        },
                        foreColor: chartTextColor
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '55%',
                            endingShape: 'rounded'
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    xaxis: {
                        categories: ['Estatísticas'],
                        labels: {
                            style: {
                                colors: axisTextColor
                            }
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Quantidade',
                            style: {
                                color: axisTextColor
                            }
                        },
                        labels: {
                            style: {
                                colors: axisTextColor
                            }
                        }
                    },
                    fill: {
                        opacity: 1,
                        colors: chartFillColors // Define cores de preenchimento dos gráficos
                    },
                    tooltip: {
                        theme: 'light', // Ou 'dark' se preferir
                        y: {
                            formatter: function(val) {
                                return val;
                            }
                        }
                    },
                    legend: {
                        labels: {
                            colors: legendTextColor // Define a cor do texto da legenda
                        }
                    }
                };

                if (detailedStatisticsChartEl !== undefined && detailedStatisticsChartEl !== null) {
                    const detailedStatisticsChart = new ApexCharts(detailedStatisticsChartEl,
                        detailedStatisticsChartOptions);
                    detailedStatisticsChart.render();

                    // console.log("detailedStatisticsChart", detailedStatisticsChart);
                }
            });
        </script>



        <!-- Transações -->
        <div class="col-md-6 col-xl-4 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between">
                    <div class="card-title m-0 me-2">
                        <h5 class="m-0 me-2">Transações</h5>
                        <small class="text-muted" id="transactionCount">Ultimas {{ $pagamentos->count() }} transações
                            realizadas</small>
                    </div>
                    {{-- <div class="dropdown">
        <button class="btn p-0" type="button" id="transactionID" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="ti ti-dots-vertical ti-sm text-muted"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="transactionID">
          <a class="dropdown-item" href="javascript:void(0);" onclick="filterTransactions('28_days')">Últimos 28 dias</a>
          <a class="dropdown-item" href="javascript:void(0);" onclick="filterTransactions('last_month')">Último mês</a>
          <a class="dropdown-item" href="javascript:void(0);" onclick="filterTransactions('last_year')">Último ano</a>
        </div>
      </div> --}}
                </div>
                <div class="card-body">
                    <ul class="p-0 m-0" id="transactionList">
                        @foreach ($pagamentos as $pagamento)
                            <li class="d-flex mb-3 pb-1 align-items-center">
                                <div class="badge bg-label-primary me-3 rounded p-2">
                                    <i class="ti ti-wallet ti-sm"></i>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <h6 class="mb-0">Pagamento</h6>
                                        <small class="text-muted d-block">ID da Transação:
                                            {{ $pagamento->mercado_pago_id }}</small>
                                    </div>
                                    <div class="user-progress d-flex align-items-center gap-1">
                                        <h6 class="mb-0 text-success">
                                            +R${{ number_format($pagamento->valor, 2, ',', '.') }}</h6>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <!--/ Transações -->

        <!-- Relatórios de Ganhos -->
        <div class="col-12 col-xl-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Relatórios de Ganhos</h5>
                        <small class="text-muted">Visão geral dos ganhos anuais</small>
                    </div>
                    <div class="dropdown">
                        <button class="btn p-0" type="button" id="earningReportsTabsId" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <i class="ti ti-dots-vertical ti-sm text-muted"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs widget-nav-tabs pb-3 gap-4 mx-1 d-flex flex-nowrap" role="tablist">
                        <li class="nav-item">
                            <a href="javascript:void(0);"
                                class="nav-link btn active d-flex flex-column align-items-center justify-content-center"
                                role="tab" data-bs-toggle="tab" data-bs-target="#navs-orders-id"
                                aria-controls="navs-orders-id" aria-selected="true">
                                <div class="badge bg-label-secondary rounded p-2"><i
                                        class="ti ti-shopping-cart ti-sm"></i></div>
                                <h6 class="tab-widget-title mb-0 mt-2">Ordens</h6>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="javascript:void(0);"
                                class="nav-link btn d-flex flex-column align-items-center justify-content-center"
                                role="tab" data-bs-toggle="tab" data-bs-target="#navs-sales-id"
                                aria-controls="navs-sales-id" aria-selected="false">
                                <div class="badge bg-label-secondary rounded p-2"><i class="ti ti-chart-bar ti-sm"></i>
                                </div>
                                <h6 class="tab-widget-title mb-0 mt-2">Receita</h6>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="javascript:void(0);"
                                class="nav-link btn d-flex flex-column align-items-center justify-content-center"
                                role="tab" data-bs-toggle="tab" data-bs-target="#navs-earnings-id"
                                aria-controls="navs-earnings-id" aria-selected="false">
                                <div class="badge bg-label-secondary rounded p-2"><i
                                        class="ti ti-currency-dollar ti-sm"></i></div>
                                <h6 class="tab-widget-title mb-0 mt-2">Ganhos</h6>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content p-0 ms-0 ms-sm-2">
                        <div class="tab-pane fade show active" id="navs-orders-id" role="tabpanel">
                            <div id="earningReportsTabsOrders"></div>
                        </div>
                        <div class="tab-pane fade" id="navs-sales-id" role="tabpanel">
                            <div id="earningReportsTabsSales"></div>
                        </div>
                        <div class="tab-pane fade" id="navs-profit-id" role="tabpanel">
                            <div id="earningReportsTabsProfit"></div>
                        </div>
                        <div class="tab-pane fade" id="navs-income-id" role="tabpanel">
                            <div id="earningReportsTabsIncome"></div>
                        </div>
                        <div class="tab-pane fade" id="navs-earnings-id" role="tabpanel">
                            <div id="earningsLast7Days"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Script para Filtrar Transações -->
        <script>
            function filterTransactions(period) {
                const userId = {{ auth()->user()->id }}; // Obtém o ID do usuário autenticado

                fetch(`/api/transactions?period=${period}&user_id=${userId}`)
                    .then(response => response.json())
                    .then(data => {
                        const transactionList = document.getElementById('transactionList');
                        const transactionCount = document.getElementById('transactionCount');

                        // Atualizar contagem de transações
                        transactionCount.textContent = `Total de ${data.payments.length} transações realizadas`;

                        // Limpar lista de transações
                        transactionList.innerHTML = '';

                        // Adicionar transações filtradas
                        data.payments.forEach(pagamento => {
                            const li = document.createElement('li');
                            li.classList.add('d-flex', 'mb-3', 'pb-1', 'align-items-center');

                            li.innerHTML = `
            <div class="badge bg-label-primary me-3 rounded p-2">
              <i class="ti ti-wallet ti-sm"></i>
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-0">Mercado Pago</h6>
                <small class="text-muted d-block">ID da Transação: ${pagamento.mercado_pago_id}</small>
              </div>
              <div class="user-progress d-flex align-items-center gap-1">
                <h6 class="mb-0 text-success">+R$${parseFloat(pagamento.valor).toFixed(2).replace('.', ',')}</h6>
              </div>
            </div>
          `;

                            transactionList.appendChild(li);
                        });
                    })
                    .catch(error => console.error('Erro ao buscar transações:', error));
            }
        </script>
    </div>
@endsection
