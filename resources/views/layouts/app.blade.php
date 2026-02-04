<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css'])
</head>
<body>
    <div id="app">

        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    Estoque & Vendas teste rick
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent">
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
                                <a class="nav-link dropdown-toggle" href="#" role="button"
                                    data-bs-toggle="dropdown">
                                    Vendas
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('vendas.index') }}">
                                            Lista de Vendas
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('vendas.create') }}">
                                            Nova Venda
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
                                $usuario = Auth::user();
                                $estoqueBaixo = collect();
                                $estoqueBaixoTotal = 0;

                                    if ($usuario && \Illuminate\Support\Facades\Schema::hasTable('produtos')) {
                                    $estoqueBaixo = \App\Models\Produto::estoqueBaixo()
                                        ->orderBy('estoque')
                                        ->limit(5)
                                        ->get(['id', 'nome', 'estoque', 'estoque_minimo']);
                                    $estoqueBaixoTotal = \App\Models\Produto::estoqueBaixo()->count();
                                }
                            @endphp
                            <li class="nav-item dropdown me-2">
                                <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown"
                                    id="notificationDropdown" data-notifications-url="{{ route('notificacoes.estoque-baixo') }}">
                                    <i class="bi bi-bell-fill"></i>
                                    @if($estoqueBaixoTotal > 0)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                            id="notification-badge">
                                            {{ $estoqueBaixoTotal }}
                                        </span>
                                    @endif
                                </a>
                                <div class="dropdown-menu dropdown-menu-end p-2" style="min-width: 320px;">
                                    <h6 class="dropdown-header">Estoque baixo</h6>
                                    <div id="estoque-baixo-lista">
                                        @forelse($estoqueBaixo as $produto)
                                            <div class="px-2 py-2 border-bottom notification-item">
                                                <div class="fw-semibold">{{ $produto->nome }}</div>
                                                <div class="small text-muted">
                                                    Estoque: {{ $produto->estoque }} / Mínimo: {{ $produto->estoque_minimo }}
                                                </div>
                                                <a href="{{ route('produtos.edit', $produto) }}" class="small">Editar produto</a>
                                            </div>
                                            @empty
                                            <div class="px-3 py-2 text-muted small">Nenhum produto com estoque baixo.</div>
                                        @endforelse
                                    </div>
                                </div>
                            </li>
                                <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button"
                                    data-bs-toggle="dropdown">
                                    {{ Auth::user()->name }}
                                </a>

                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item"
                                            href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                            document.getElementById('logout-form').submit();">
                                            Sair
                                        </a>

                                        <form id="logout-form"
                                                action="{{ route('logout') }}"
                                                method="POST"
                                                lass="d-none">
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
    @auth
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const dropdown = document.getElementById('notificationDropdown');
                const lista = document.getElementById('estoque-baixo-lista');
                const badge = document.getElementById('notification-badge');

                if (!dropdown) {
                    return;
                }

                const atualizarNotificacoes = async () => {
                    const url = dropdown.dataset.notificationsUrl;
                    if (!url || !lista) {
                        return;
                    }

                    try {
                        const response = await fetch(url, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });

                        if (!response.ok) {
                            return;
                        }

                        const data = await response.json();
                        const total = Number(data.total || 0);
                        const produtos = Array.isArray(data.produtos) ? data.produtos : [];

                        if (badge) {
                            if (total > 0) {
                                badge.textContent = total;
                            } else {
                                badge.remove();
                            }
                        } else if (total > 0) {
                            const newBadge = document.createElement('span');
                            newBadge.id = 'notification-badge';
                            newBadge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger';
                            newBadge.textContent = total;
                            dropdown.appendChild(newBadge);
                        }

                        lista.innerHTML = '';
                        if (produtos.length === 0) {
                            const item = document.createElement('div');
                            item.className = 'px-3 py-2 text-muted small';
                            item.textContent = 'Nenhum produto com estoque baixo.';
                            lista.appendChild(item);
                            return;
                        }

                        produtos.forEach((produto) => {
                            const item = document.createElement('div');
                            item.className = 'px-2 py-2 border-bottom notification-item';
                            item.innerHTML = `
                                <div class="fw-semibold">${produto.nome}</div>
                                <div class="small text-muted">Estoque: ${produto.estoque} / Mínimo: ${produto.estoque_minimo}</div>
                                <a href="/produtos/${produto.id}/edit" class="small">Editar produto</a>
                            `;
                            lista.appendChild(item);
                        });
                    } catch (error) {
                        console.error('Falha ao atualizar notificações de estoque baixo.', error);
                    }
                };

                dropdown.addEventListener('shown.bs.dropdown', atualizarNotificacoes);

                setInterval(atualizarNotificacoes, 30000);
            });
        </script>
    @endauth
</body>
</html>
