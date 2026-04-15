<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Estoque & Vendas') }}</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css'])
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ route('dashboard') }}">
                    {{ config('app.name', 'Estoque & Vendas') }}
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto">
                        @auth
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('produtos.index') }}">Produtos</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('clientes.index') }}">Clientes</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    Vendas
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('vendas.caixa') }}">
                                            <i class="bi bi-upc-scan me-2"></i> Caixa PDV
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('vendas.create') }}">
                                            <i class="bi bi-bag me-2"></i> Pedido Online / Manual
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('vendas.index') }}">
                                            Lista de Vendas
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('vendas.historico') }}">
                                            Histórico de Vendas
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('produtos.media_vendas') }}">
                                            Média de Vendas
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endauth
                    </ul>

                    <ul class="navbar-nav ms-auto">
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">Entrar</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">Cadastrar</a>
                            </li>
                        @else
                            @php
                                $notificacoesLista = $notificacoes ?? collect();
                                $notificacoesTotal = $notificacoesNaoLidas ?? 0;
                            @endphp
                            <li class="nav-item dropdown me-2">
                                <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown"
                                    id="notificationDropdown" aria-label="Abrir notificações" aria-expanded="false">
                                    <i class="bi bi-bell-fill" aria-hidden="true"></i>
                                    @if($notificacoesTotal > 0)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                            id="notification-badge" aria-label="{{ $notificacoesTotal }} notificações não lidas">
                                            {{ $notificacoesTotal }}
                                        </span>
                                    @endif
                                </a>
                                <div class="dropdown-menu dropdown-menu-end p-0 notification-dropdown" aria-labelledby="notificationDropdown">
                                    <div class="px-3 py-2 border-bottom d-flex justify-content-between align-items-start gap-2 notification-dropdown-header">
                                        <h6 class="mb-0">Notificações</h6>
                                        <div class="d-flex gap-1">
                                            <form method="POST" action="{{ route('notificacoes.markAllAsRead') }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-primary" aria-label="Marcar todas notificações como lidas">
                                                    Marcar todas como lidas
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('notificacoes.clearAll') }}" onsubmit="return confirm('Tem certeza que deseja limpar todas as notificações?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" aria-label="Limpar todas as notificações">
                                                    Limpar notificações
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <div class="notification-list" role="list">
                                        @forelse($notificacoesLista as $notificacao)
                                            @php
                                                $notificacaoLida = !is_null($notificacao->read_at);
                                                $tituloNotificacao = $notificacao->data['titulo'] ?? 'Atualização';
                                                $tipoNotificacao = $notificacao->data['tipo'] ?? null;
                                                $descricaoNotificacao = $notificacao->data['descricao']
                                                    ?? collect($notificacao->data['produtos'] ?? [])->pluck('nome')->take(2)->join(', ');
                                                $descricaoNotificacao = $descricaoNotificacao ?: 'Sem detalhes adicionais.';
                                                $linkNotificacao = $notificacao->data['link'] ?? null;
                                            @endphp
                                            <div class="notification-item {{ $notificacaoLida ? 'is-read' : 'is-unread' }}" role="listitem">
                                                <div class="d-flex justify-content-between gap-2 align-items-start">
                                                    <a href="{{ $linkNotificacao ?: route('notificacoes.index') }}"
                                                        class="notification-body-link text-decoration-none text-reset"
                                                        aria-label="Abrir notificação {{ $tituloNotificacao }}">
                                                        <div class="d-flex align-items-center gap-2 mb-1">
                                                            <span class="notification-status-dot" aria-hidden="true"></span>
                                                            <span class="fw-semibold notification-title text-truncate">
                                                                @if($tipoNotificacao === 'estoque_esgotado')
                                                                    <i class="bi bi-exclamation-triangle-fill text-warning me-1" aria-hidden="true"></i>
                                                                @endif
                                                                {{ $tituloNotificacao }}
                                                            </span>
                                                        </div>
                                                        <div class="notification-description text-muted">{{ $descricaoNotificacao }}</div>
                                                        <div class="notification-meta mt-1">{{ optional($notificacao->created_at)->diffForHumans() }}</div>
                                                    </a>

                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-light border-0" type="button" data-bs-toggle="dropdown" aria-label="Ações da notificação">
                                                            <i class="bi bi-three-dots-vertical" aria-hidden="true"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            @if(!$notificacaoLida)
                                                                <li>
                                                                    <form method="POST" action="{{ route('notificacoes.markAsRead', $notificacao->id) }}">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <button type="submit" class="dropdown-item">Marcar como lida</button>
                                                                    </form>
                                                                </li>
                                                            @endif
                                                            <li>
                                                                <form method="POST" action="{{ route('notificacoes.destroy', $notificacao->id) }}" onsubmit="return confirm('Deseja apagar esta notificação?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="dropdown-item text-danger">Apagar</button>
                                                                </form>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="px-3 py-3 text-muted small">Nenhuma notificação disponível.</div>
                                        @endforelse
                                    </div>

                                    <div class="notification-actions border-top px-3 py-2 text-center bg-white">
                                        <a href="{{ route('notificacoes.index') }}" class="btn btn-sm btn-outline-secondary" aria-label="Ver todas as notificações">
                                            Ver todas
                                        </a>
                                    </div>
                                </div>
                            </li>

                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    {{ Auth::user()->name }}
                                </a>

                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item"
                                            href="{{ route('logout') }}"
                                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            Sair
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            <div class="container">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        {{ session('info') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            </div>
            @yield('content')
        </main>
    </div>

    @stack('scripts')
    @vite(['resources/js/app.js'])
</body>
</html>
