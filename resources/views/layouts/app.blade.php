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
                                <div class="dropdown-menu dropdown-menu-end p-0 notification-dropdown">
                                    <div class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center notification-dropdown-header">
                                        <div>
                                            <h6 class="mb-0">Estoque baixo</h6>
                                            <small class="text-muted">Acompanhe alertas ativos e dispensados.</small>
                                        </div>
                                        <span class="badge bg-primary-subtle text-primary fw-semibold">Monitoramento</span>
                                    </div>
                                    <div id="estoque-baixo-lista" class="notification-list">
                                        @forelse($notificacoesLista as $notificacao)
                                        @php
                                                $notificacaoLida = !is_null($notificacao->read_at);
                                                $tituloNotificacao = $notificacao->data['titulo'] ?? 'Produtos com estoque baixo';
                                                $ocultarInicial = $loop->index >= 3;
                                            @endphp
                                            <div class="notification-item {{ $notificacaoLida ? 'is-dismissed' : '' }} {{ $ocultarInicial ? 'd-none' : '' }}"
                                                data-notification-id="{{ $notificacao->id }}"
                                                data-notification-read="{{ $notificacaoLida ? '1' : '0' }}">
                                                <div class="notification-item-header">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="notification-status-dot"></span>
                                                        <div class="fw-semibold text-dark">{{ $tituloNotificacao }}</div>
                                                    </div>
                                                    @if($notificacaoLida)
                                                        <span class="badge bg-secondary-subtle text-secondary">Dispensada</span>
                                                    @else
                                                        <button type="button" class="btn btn-sm btn-outline-primary notification-dismiss"
                                                            data-notification-id="{{ $notificacao->id }}">
                                                            Desativar
                                                        </button>
                                                    @endif
                                                </div>
                                                <div class="notification-meta">
                                                    <i class="bi bi-clock"></i>
                                                    <span>
                                                        {{ optional($notificacao->created_at)->format('d/m/Y H:i') }}
                                                    </span>
                                                </div>
                                                <div class="notification-details">
                                                    @forelse($notificacao->data['produtos'] ?? [] as $produto)
                                                        <div class="notification-detail-row">
                                                            <div class="text-muted small">
                                                                {{ $produto['nome'] ?? 'Produto' }}
                                                            </div>
                                                            <div class="small fw-semibold">
                                                                {{ $produto['estoque'] ?? '-' }} / {{ $produto['estoque_minimo'] ?? '-' }}
                                                            </div>
                                                        </div>
                                                        @if(!empty($produto['id']))
                                                            <a href="{{ route('produtos.edit', $produto['id']) }}" class="small text-decoration-none">
                                                                <i class="bi bi-pencil-square"></i>
                                                                Ajustar produto
                                                            </a>
                                                        @endif
                                                    @empty
                                                        <div class="small text-muted">Nenhum detalhe disponível.</div>
                                                    @endforelse
                                                </div>
                                            </div>
                                            @empty
                                            <div class="px-3 py-3 text-muted small">Nenhum alerta registrado.</div>
                                        @endforelse
                                    </div>
                                    <div class="notification-actions border-top px-3 py-2 d-flex justify-content-between align-items-center">
                                        <small class="text-muted" id="notification-visible-count">Mostrando 3 alertas mais recentes</small>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="notification-toggle" hidden>
                                            Ver todos
                                        </button>
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
                if (!dropdown) {
                    return;
                }

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const dismissUrl = dropdown.dataset.dismissUrl;
                const toggleButton = document.getElementById('notification-toggle');
                const visibleCountLabel = document.getElementById('notification-visible-count');
                const limitInitial = 3;
                let mostrarTudo = false;

                const atualizarVisibilidade = () => {
                    if (!lista) {
                        return;
                    }

                    const items = Array.from(lista.querySelectorAll('.notification-item'));
                    const total = items.length;

                    if (total === 0) {
                        if (toggleButton) {
                            toggleButton.hidden = true;
                        }
                        if (visibleCountLabel) {
                            visibleCountLabel.textContent = 'Sem alertas ativos no momento';
                        }
                        return;
                    }

                    const limite = mostrarTudo ? total : limitInitial;
                    let exibidos = 0;

                    items.forEach((item, index) => {
                        const deveMostrar = index < limite;
                        item.classList.toggle('d-none', !deveMostrar);
                        if (deveMostrar) {
                            exibidos += 1;
                        }
                    });

                    if (toggleButton) {
                        toggleButton.hidden = total <= limitInitial;
                        toggleButton.textContent = mostrarTudo ? 'Mostrar menos' : 'Ver todos';
                    }

                    if (visibleCountLabel) {
                        visibleCountLabel.textContent = mostrarTudo
                            ? `Mostrando todos os ${total} alertas`
                            : `Mostrando ${exibidos} de ${total} alertas`;
                    }
                };


                const limparNotificacoes = () => {
                    lista.innerHTML = '';
                    const item = document.createElement('div');
                    item.className = 'px-3 py-3 text-muted small';
                    item.textContent = 'Nenhum alerta registrado.';
                    lista.appendChild(item);
                    if (toggleButton) {
                        toggleButton.hidden = true;
                    }
                    if (visibleCountLabel) {
                        visibleCountLabel.textContent = 'Sem alertas ativos no momento';
                    }
                };

                const formatarData = (data) => {
                    if (!data) {
                        return 'Data indisponível';
                    }

                    const parsed = new Date(data);
                    if (Number.isNaN(parsed.getTime())) {
                        return 'Data indisponível';
                    }

                    return parsed.toLocaleString('pt-BR', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                };

                const construirItem = (notificacao) => {
                    const item = document.createElement('div');
                    const produtos = Array.isArray(notificacao.produtos) ? notificacao.produtos : [];
                    const titulo = notificacao.titulo || 'Produtos com estoque baixo';
                    const isRead = Boolean(notificacao.lida);

                    item.className = `notification-item${isRead ? ' is-dismissed' : ''}`;
                    item.dataset.notificationId = notificacao.id;
                    item.dataset.notificationRead = isRead ? '1' : '0';

                    const linhasProdutos = produtos.length
                        ? produtos.map((produto) => {
                            const nome = produto.nome || 'Produto';
                            const estoque = produto.estoque ?? '-';
                            const minimo = produto.estoque_minimo ?? '-';
                            const link = produto.id
                                ? `<a href="/produtos/${produto.id}/edit" class="small text-decoration-none">
                                        <i class="bi bi-pencil-square"></i>
                                        Ajustar produto
                                    </a>`
                                : '';
                            return `
                                <div class="notification-detail-row">
                                    <div class="text-muted small">${nome}</div>
                                    <div class="small fw-semibold">${estoque} / ${minimo}</div>
                                </div>
                                ${link}
                            `;
                        }).join('')
                        : '<div class="small text-muted">Nenhum detalhe disponível.</div>';

                    const buttonOrBadge = isRead
                        ? '<span class="badge bg-secondary-subtle text-secondary">Dispensada</span>'
                        : `<button type="button" class="btn btn-sm btn-outline-primary notification-dismiss"
                                data-notification-id="${notificacao.id}">
                                Desativar
                            </button>`;

                    item.innerHTML = `
                        <div class="notification-item-header">
                            <div class="d-flex align-items-center gap-2">
                                <span class="notification-status-dot"></span>
                                <div class="fw-semibold text-dark">${titulo}</div>
                            </div>
                            ${buttonOrBadge}
                        </div>
                        <div class="notification-meta">
                            <i class="bi bi-clock"></i>
                            <span>${formatarData(notificacao.created_at)}</span>
                        </div>
                        <div class="notification-details">
                            ${linhasProdutos}
                        </div>
                    `;

                    return item;
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
                            lista.appendChild(construirItem(notificacao));
                        });

                        atualizarVisibilidade();
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
                            item.classList.add('is-dismissed');
                            item.dataset.notificationRead = '1';
                            const badge = document.createElement('span');
                            badge.className = 'badge bg-secondary-subtle text-secondary';
                            badge.textContent = 'Dispensada';
                            button.replaceWith(badge);
                        }

                        if (typeof data.total === 'number') {
                            atualizarBadge(data.total);
                        } else {
                            atualizarBadge(Math.max(0, obterTotalAtual() - 1));
                        }

                        if (lista.children.length === 0) {
                            limparNotificacoes();
                            return;
                        }

                        atualizarVisibilidade();
                    } catch (error) {
                        console.error('Falha ao dispensar notificação.', error);
                    }
                });

                toggleButton?.addEventListener('click', () => {
                    mostrarTudo = !mostrarTudo;
                    atualizarVisibilidade();
                });

                atualizarVisibilidade();

                let notificacoesIniciadas = false;
                let intervaloNotificacoes = null;

                dropdown.addEventListener('shown.bs.dropdown', () => {
                    atualizarNotificacoes();
                    atualizarVisibilidade();

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
