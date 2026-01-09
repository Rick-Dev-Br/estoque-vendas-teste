@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <h5 class="mb-0">
            <i class="bi bi-cart me-2"></i>Vendas
        </h5>
        <a href="{{ route('vendas.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Nova Venda
        </a>
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
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vendas as $venda)
                    <tr>
                        <td>#{{ $venda->id }}</td>
                        <td>{{ $venda->cliente->nome ?? 'N/A' }}</td>
                        <td>R$ {{ number_format($venda->total, 2, ',', '.') }}</td>
                        <td>
                            @if($venda->status == 'pendente')
                            <span class="badge bg-warning text-dark">Pendente</span>
                            @elseif($venda->status == 'pago')
                            <span class="badge bg-success">Pago</span>
                            @else
                            <span class="badge bg-danger">Cancelado</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-inline-flex flex-wrap gap-1 justify-content-center">
                                @if($venda->status == 'pendente')
                                <a href="{{ route('vendas.edit', $venda) }}" class="btn btn-sm btn-outline-primary" title="Editar venda">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('vendas.alterar-status', $venda) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="pago">
                                    <button type="submit" class="btn btn-sm btn-success" title="Marcar como Pago">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                </form>

                            <form action="{{ route('vendas.alterar-status', $venda) }}" method="POST" class="d-inline">
                                @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="cancelado">
                                    <button type="submit" class="btn btn-sm btn-danger" title="Cancelar">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </form>
                                @else
                                <span class="text-muted small">Sem ações disponíveis</span>
                            @endif
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
            <p class="text-muted">Comece criando sua primeira venda.</p>
            <a href="{{ route('vendas.create') }}" class="btn btn-primary mt-3">
                <i class="bi bi-plus-circle me-1"></i> Nova Venda
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
