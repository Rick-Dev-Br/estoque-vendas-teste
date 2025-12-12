<!DOCTYPE html>
<html lang="pr-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=0.1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Estoque & Vendas teste rick</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">

        <style>
            body {
                padding-top: 20px;
                background-color: #f8f9fa;
            }
            .card {
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }
            .badge {
                font-size: 0.85em;
            }
            .table-actions {
                white-space: nowrap;
            }
            .table-actions .btn {
                margin-right: 5px;
            }
            .status-badge {
                padding: 5px 10px;
                border-radius: 20px;
                font-weight: 500;
            }
            .total-venda {
                font-size: 1.5em;
                font-weight: bold;
                color: #198754;
            }
            .item-venda {
                border-bottom: 1px solid #dee2e6;
                padding: 10px 0;
            }
            .item-venda:last-child {
                border-bottom: none;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light bg-white mb-4 rounded shadow-sm">
                <div class="container-fluid">
                    <a class="navbar-brand" href="/">
                        <i class="bi bi-box-sean"></i> Estoque & Vendas teste rick
                    </a>

                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('produtos*') ? 'active' : '' }}" href="{{ route('produtos.index') }}">
                                    <i class="bi bi-box"></i> Produtos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('clientes*') ? 'active' : '' }}" href="{{ route('clientes.index') }}">
                                    <i class="bi bi-people"></i> Clientes
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('vendas') ? 'active' : '' }}" href="{{ route('vendas.index') }}">
                                    <i class="bi bi-cart"></i> Vendas
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangke me-2"></i>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            <main>
                @yield('content')
            </main>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="{{ asset('js/app.js') }}"></script>

        @stack('scripts')
    </body>
</html>
