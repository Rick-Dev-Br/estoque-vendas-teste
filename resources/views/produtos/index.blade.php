@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-box me-2"></i> Produtos Cadastrados
        </h5>
        <a href="{{ route('produtos.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Novo Produto
        </a>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Preço</th>
                        <th>Estoque</th>
                        <th>Status</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produtos as $produto)
                    <tr>
                        <td>#{{ $produto->id }}</td>
                        <td>{{  $produto->nome }}</td>
                        <td>R$ {{ number_format($produto->preco, 2, ',', '.') }}</td>
                        <td>
                            <span class="{{ $produto->estoque > 0 ? 'text-success' : 'text-danger' }}">
                                {{ $produto->estoque  }} unidades
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $produto->status_classe  }} status-badge">
                                {{ $produto->status_formatado }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('produtos.edit', $produto) }}" class="btn btn-sm btn-info" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>

                            <form action="{{ route('produtos.toggle-status', $produto) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                @if($produto->status == 'ativo')
                                <button type="submit" class="btn btn-sm btn-warning" title="Inativar">
                                    <i class="bi bi-lock"></i>
                                </button>
                                @else
                                <button type="submit" class="btn btn-sm btn-success" title="Ativar">
                                    <i class="bi bi-unlock"></i>
                                </button>
                                @endif
                            </form>

                            <form action="{{ route('produtos.destroy', $produto) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('Tem certeza que deseja excluir este produto?')" title="Excluir">
                                <i class="bi bi-trash"></i>
                            </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="bi bi-inbox display-6 d-block mb-2"></i>
                            Nenhum produto cadastrado
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
