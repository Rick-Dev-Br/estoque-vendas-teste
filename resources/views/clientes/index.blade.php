@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="bi bi-people me-2"></i>Clientes
        </h5>
        <a href="{{ route('clientes.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Novo Cliente
        </a>
    </div>

    <div class="card-body">
        @if($clientes->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clientes as $cliente)
                    <tr>
                        <td>#{{ $cliente->id }}</td>
                        <td>{{ $cliente->nome }}</td>
                        <td>{{ $cliente->email }}</td>
                        <td>
                            @if($cliente->status == 'ativo')
                            <span class="badge bg-success">Ativo</span>
                            @else
                            <span class="badge bg-danger">Bloqueado</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <form action="{{ route('clientes.toggle-status', $cliente) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                @if($cliente->status == 'ativo')
                                <button type="submit" class="btn btn-sm btn-warning" title="Bloquear">
                                    <i class="bi bi-lock"></i>
                                </button>
                                @else
                                <button type="submit" class="btn btn-sm btn-success" title="Ativar">
                                    <i class="bi bi-unlock"></i>
                                </button>
                                @endif
                            </form>

                            <form action="{{ route('clientes.destroy', $cliente) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Excluir este cliente?')"
                                        title="Excluir">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-people display-1 text-muted mb-3"></i>
            <h5 class="text-muted">Nenhum cliente cadastrado</h5>
            <p class="text-muted">Comece cadastrando seu primeiro cliente.</p>
            <a href="{{ route('clientes.create') }}" class="btn btn-primary mt-3">
                <i class="bi bi-plus-circle me-1"></i> Cadastrar Cliente
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
