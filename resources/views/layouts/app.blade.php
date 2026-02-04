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
                                $notificacoesLista = $notificacoes ?? collect();
                                $notificacoesTotal = $notificacoesNaoLidas ?? 0;
                            @endphp
                            <li class="nav-item dropdown me-2">
                                <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown"
                                    id="notificationDropdown"
                                    data-notifications-url="{{ route('notificacoes.estoque-baixo') }}"
                                    data-dismiss-url="{{ route('notificacoes.dispensar') }}">
                                    <i class="bi bi-bell-fill"></i>
                                    @if($notificacoesTotal > 0)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                            id="notification-badge">
                                            {{ $notificacoesTotal }}
                                        </span>
                                    @endif
                                </a>
                                <div class="dropdown-menu dropdown-menu-end p-2" style="min-width: 320px;">
                                    <h6 class="dropdown-header">Estoque baixo</h6>
                                    <div id="estoque-baixo-lista">
                                        @forelse($notificacoesLista as $notificacao)
                                            <div class="px-2 py-2 border-bottom notification-item" data-notification-id="{{ $notificacao->id }}">
                                                <div class="d-flex justify-content-between align-items-start gap-2">
                                                    <div class="fw-semibold">
                                                        {{ $notificacao->data['titulo'] ?? 'Produtos com estoque baixo' }}
                                                    </div>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary notification-dismiss"
                                                        data-notification-id="{{ $notificacao->id }}">
                                                        Desativar
                                                    </button>
                                                </div>
                                                @forelse($notificacao->data['produtos'] ?? [] as $produto)
                                                    <div class="small text-muted">
                                                        {{ $produto['nome'] ?? 'Produto' }} — Estoque: {{ $produto['estoque'] ?? '-' }} / Mínimo: {{ $produto['estoque_minimo'] ?? '-' }}
                                                    </div>
                                                    @if(!empty($produto['id']))
                                                        <a href="{{ route('produtos.edit', $produto['id']) }}" class="small">Editar produto</a>
                                                    @endif
                                                @empty
                                                    <div class="small text-muted">Nenhum detalhe disponível.</div>
                                                @endforelse
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
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const dismissUrl = dropdown.dataset.dismissUrl;

                if (!dropdown) {
                    return;
                }

                const limparNotificacoes = () => {
                    lista.innerHTML = '';
                    const item = document.createElement('div');
                    item.className = 'px-3 py-2 text-muted small';
                    item.textContent = 'Nenhum produto com estoque baixo.';
                    lista.appendChild(item);
                };

                const atualizarBadge = (total) => {
                    const badge = document.getElementById('notification-badge');
                    if (total > 0) {
                        if (badge) {
                            badge.textContent = total;
                            return;
                        }

                        const newBadge = document.createElement('span');
                        newBadge.id = 'notification-badge';
                        newBadge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger';
                        newBadge.textContent = total;
                        dropdown.appendChild(newBadge);
                        return;
                    }

                    if (badge) {
                        badge.remove();
                    }
                };

                const obterTotalAtual = () => {
                    const badge = document.getElementById('notification-badge');
                    return Number(badge?.textContent || 0);
                };

                const atualizarNotificacoes = async () => {
                    const url = dropdown.dataset.notificationsUrl;
                    if (!url || !lista) {
                        return;
                    }

                    try {
                        const response = await fetch(url, {
                            headers: {
                                'Accept': 'application/json'
                            },
                            cache: 'no-store'
                        });

                        if (!response.ok) {
                            return;
                        }

                        const data = await response.json();
                        const total = Number(data.total || 0);
                        const notificacoes = Array.isArray(data.notificacoes) ? data.notificacoes : [];

                        atualizarBadge(total);

                        lista.innerHTML = '';
                        if (notificacoes.length === 0) {
                            limparNotificacoes();
                            return;
                        }

                        notificacoes.forEach((notificacao) => {
                            const item = document.createElement('div');
                            item.className = 'px-2 py-2 border-bottom notification-item';
                            const produtos = Array.isArray(notificacao.produtos) ? notificacao.produtos : [];
                            const titulo = notificacao.titulo || 'Produtos com estoque baixo';
                            const linhasProdutos = produtos.length
                                ? produtos.map((produto) => {
                                    const nome = produto.nome || 'Produto';
                                    const estoque = produto.estoque ?? '-';
                                    const minimo = produto.estoque_minimo ?? '-';
                                    const link = produto.id ? `<a href="/produtos/${produto.id}/edit" class="small">Editar produto</a>` : '';
                                    return `
                                        <div class="small text-muted">${nome} — Estoque: ${estoque} / Mínimo: ${minimo}</div>
                                        ${link}
                                    `;
                                }).join('')
                                : '<div class="small text-muted">Nenhum detalhe disponível.</div>';

                            item.innerHTML = `
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div class="fw-semibold">${titulo}</div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary notification-dismiss"
                                        data-notification-id="${notificacao.id}">
                                        Desativar
                                    </button>
                                </div>
                                ${linhasProdutos}
                            `;
                            lista.appendChild(item);
                        });
                    } catch (error) {
                        console.error('Falha ao atualizar notificações de estoque baixo.', error);
                    }
                };

                lista?.addEventListener('click', async (event) => {
                    const button = event.target.closest('.notification-dismiss');
                    if (!button) {
                        return;
                    }

                    const notificationId = button.dataset.notificationId;
                    if (!notificationId || !csrfToken || !dismissUrl) {
                        return;
                    }

                    try {
                        const response = await fetch(dismissUrl, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({ id: notificationId })
                        });

                        if (!response.ok) {
                            return;
                        }

                        const data = await response.json().catch(() => ({}));
                        const item = button.closest('.notification-item');
                        if (item) {
                            item.remove();
                        }

                        if (typeof data.total === 'number') {
                            atualizarBadge(data.total);
                        } else {
                            atualizarBadge(Math.max(0, obterTotalAtual() - 1));
                        }

                        if (lista.children.length === 0) {
                            limparNotificacoes();
                        }
                    } catch (error) {
                        console.error('Falha ao dispensar notificação.', error);
                    }
                });

                let notificacoesIniciadas = false;
                let intervaloNotificacoes = null;

                dropdown.addEventListener('shown.bs.dropdown', () => {
                    atualizarNotificacoes();

                    if (!notificacoesIniciadas) {
                        intervaloNotificacoes = setInterval(atualizarNotificacoes, 300000);
                        notificacoesIniciadas = true;
                    }
                });
            });
        </script>
    @endauth
</body>
</html>
