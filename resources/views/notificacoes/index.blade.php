@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="mb-0">Notificações</h5>
                <div class="d-flex gap-2">
                    <form method="POST" action="{{ route('notificacoes.markAllAsRead') }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-outline-primary">Marcar todas como lidas</button>
                    </form>
                    <form method="POST" action="{{ route('notificacoes.clearAll') }}" onsubmit="return confirm('Tem certeza que deseja limpar todas as notificações?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Limpar notificações</button>
                    </form>
                </div>
            </div>

            <div class="card-body">
                <div class="btn-group mb-3" role="group" aria-label="Filtros de notificações">
                    <a href="{{ route('notificacoes.index', ['filtro' => 'todas']) }}"
                        class="btn btn-sm {{ $filtro === 'todas' ? 'btn-primary' : 'btn-outline-primary' }}">
                        Todas
                    </a>
                    <a href="{{ route('notificacoes.index', ['filtro' => 'nao_lidas']) }}"
                        class="btn btn-sm {{ $filtro === 'nao_lidas' ? 'btn-primary' : 'btn-outline-primary' }}">
                        Não lidas
                    </a>
                    <a href="{{ route('notificacoes.index', ['filtro' => 'lidas']) }}"
                        class="btn btn-sm {{ $filtro === 'lidas' ? 'btn-primary' : 'btn-outline-primary' }}">
                        Lidas
                    </a>
                </div>

                @forelse($notificacoes as $notificacao)
                    @php
                        $titulo = $notificacao->data['titulo'] ?? 'Atualização';
                        $produtosNotificacao = collect($notificacao->data['produtos'] ?? []);
                        $descricao = $notificacao->data['descricao']
                            ?? $produtosNotificacao->pluck('nome')->take(3)->join(', ');
                        $descricao = $descricao ?: 'Sem detalhes adicionais.';
                        $link = $notificacao->data['link'] ?? null;
                        $lida = !is_null($notificacao->read_at);
                    @endphp
                    <article class="notification-item mb-2 {{ $lida ? 'is-read' : 'is-unread' }}">
                        <div class="d-flex justify-content-between gap-3">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="notification-status-dot" aria-hidden="true"></span>
                                    <h6 class="mb-0">{{ $titulo }}</h6>
                                </div>
                                <p class="mb-1 text-muted notification-description">{{ $descricao }}</p>
                                <small class="text-muted">{{ optional($notificacao->created_at)->diffForHumans() }}</small>
                                @if($link)
                                    <div>
                                        <a href="{{ $link }}" class="small">Abrir link da notificação</a>
                                    </div>
                                @endif

                                @if($produtosNotificacao->isNotEmpty())
                                    <div class="mt-2">
                                        <p class="mb-1 small fw-semibold">Ações rápidas de estoque:</p>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($produtosNotificacao as $produtoNotificacao)
                                                @if(isset($produtoNotificacao['id']))
                                                    <a href="{{ route('produtos.edit', $produtoNotificacao['id']) }}" class="btn btn-sm btn-outline-warning">
                                                        Ajustar {{ $produtoNotificacao['nome'] ?? 'produto' }}
                                                    </a>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="d-flex flex-column gap-2">
                                @if(!$lida)
                                    <form method="POST" action="{{ route('notificacoes.markAsRead', $notificacao->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-primary">Marcar como lida</button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('notificacoes.destroy', $notificacao->id) }}" onsubmit="return confirm('Deseja apagar esta notificação?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Apagar</button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <p class="text-muted mb-0">Nenhuma notificação encontrada para este filtro.</p>
                @endforelse

                <div class="mt-3">
                    {{ $notificacoes->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
