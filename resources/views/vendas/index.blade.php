@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <h5 class="mb-0">
            <i class="bi bi-cart me-2"></i> Vendas
        </h5>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('vendas.caixa') }}" class="btn btn-outline-primary">
                <i class="bi bi-upc-scan me-1"></i> Caixa PDV
            </a>
            <a href="{{ route('vendas.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Pedido Online / Manual
            </a>
        </div>
    </div>

    <div class="card-body">
        @if($vendas->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Canal</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vendas as $venda)
                            <tr>
                                <td>#{{ $venda->id }}</td>
                                <td>{{ $venda->cliente->nome ?? 'N/A' }}</td>
                                <td>R$ {{ number_format((float) $venda->total, 2, ',', '.') }}</td>
                                <td>
                                    <span class="badge bg-{{ $venda->status_classe }}{{ $venda->status === 'pendente' ? ' text-dark' : '' }}">
                                        {{ $venda->status_formatado }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $venda->canal_venda_classe }}">
                                        {{ $venda->canal_venda_formatado }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-inline-flex flex-wrap gap-1 justify-content-center">
                                        <a href="{{ route('vendas.show', $venda) }}" class="btn btn-sm btn-outline-secondary" title="Ver venda">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($venda->status === 'pendente')
                                            <a href="{{ route('vendas.edit', $venda) }}" class="btn btn-sm btn-outline-primary" title="Editar venda">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form action="{{ route('vendas.alterar-status', $venda) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="pago">
                                                <button type="submit" class="btn btn-sm btn-success" title="Marcar como pago">
                                                    <i class="bi bi-check-circle"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('vendas.alterar-status', $venda) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="cancelado">
                                                <button type="submit" class="btn btn-sm btn-danger" title="Cancelar venda">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-muted small">Sem ações disponíveis</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-cart display-1 text-muted mb-3"></i>
                <h5 class="text-muted">Nenhuma venda realizada</h5>
                <p class="text-muted">Use o caixa para loja física ou abra um pedido online/manual.</p>
                <div class="d-flex flex-wrap justify-content-center gap-2 mt-3">
                    <a href="{{ route('vendas.caixa') }}" class="btn btn-outline-primary">
                        <i class="bi bi-upc-scan me-1"></i> Caixa PDV
                    </a>
                    <a href="{{ route('vendas.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Pedido Online / Manual
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
