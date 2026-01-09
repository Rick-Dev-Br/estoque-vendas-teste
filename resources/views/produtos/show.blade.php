@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
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
@endsection
