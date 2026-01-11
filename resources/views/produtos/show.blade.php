@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-box-seam me-2"></i> Detalhes do Produto
                </h5>
                <a href="{{ route('produtos.index') }}" class="btn btn-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Voltar
                </a>
            </div>

            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">ID</dt>
                    <dd class="col-sm-8">#{{ $produto->id }}</dd>

                    <dt class="col-sm-4">Nome</dt>
                    <dd class="col-sm-8">{{ $produto->nome }}</dd>

                    <dt class="col-sm-4">Preço</dt>
                    <dd class="col-sm-8">R$ {{ number_format($produto->preco, 2, ',', '.') }}</dd>

                    <dt class="col-sm-4">Estoque</dt>
                    <dd class="col-sm-8">{{ $produto->estoque }} unidades</dd>

                    <dt class="col-sm-4">Status</dt>
                    <dd class="col-sm-8">
                        <span class="badge bg-{{ $produto->status_classe }}">
                            {{ $produto->status_formatado }}
                        </span>
                    </dd>
                </dl>
            </div>

            <div class="card-footer d-flex gap-2">
                <a href="{{ route('produtos.edit', $produto) }}" class="btn btn-info btn-sm">
                    <i class="bi bi-pencil"></i> Editar
                </a>
                <form action="{{ route('produtos.toggle-status', $produto) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    @if($produto->status == 'ativo')
                    <button type="submit" class="btn btn-warning btn-sm">
                        <i class="bi bi-lock"></i> Inativar
                    </button>
                    @else
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="bi bi-unlock"></i> Ativar
                    </button>
                    @endif
                </form>
                <form action="{{ route('produtos.destroy', $produto) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm"
                        onclick="return confirm('Tem certeza que deseja excluir este produto?')">
                        <i class="bi bi-trash"></i> Excluir
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center mt-4">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-clock-history me-2"></i> Histórico de saídas do estoque
                </h5>
                <small class="text-muted d-block mt-1">
                    Exibindo apenas vendas pagas (estoque baixando).
                </small>
            </div>
            <div class="card-body">
                @if ($historico->isEmpty())
                    <p class="mb-0 text-muted">Nenhuma saída registrada para este produto.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm aling-meddle mb-0">
                            <thead>
                                <tr>
                                    <th>Venda</th>
                                    <th>Cliente</th>
                                    <th>Quantidade</th>
                                    <th>Data da venda</th>
                                    <th>Preço unitário</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($historico as $item)
                                    @php
                                        $dataVenda = $item->venda?->data_compra ?? $item->venda?->created_at;
                                    @endphp
                                    <tr>
                                        <td>#{{ $item->venda?->id ?? '-' }}</td>
                                        <td>{{ $item->venda?->cliente?->nome ?? 'Cliente não informado' }}</td>
                                        <td>{{ $item->quantidade }} unidades</td>
                                        <td>{{ $dataVenda ? $dataVenda->format('d/m/Y H:i') : '-' }}</td>
                                        <td>{{ $item->preco_formatado }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
