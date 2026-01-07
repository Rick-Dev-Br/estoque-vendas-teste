@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h3>Média de Vendas por Produto</h3>
        <a class="btn btn-primary" href="{{ route('produtos.index') }}">Voltar para Produtos</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Média de Quantidade Vendida</th>
                        <th>Detalhes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dados as $produto)
                        <tr>
                            <td>{{ $produto->nome }}</td>
                            <td>{{ number_format($produto->media_quantidade, 2) }}</td>
                            <td>
                                <a href="{{ route('produtos.show', $produto->id) }}"
                                    class="btn btn-sm btn-info">
                                    Ver Produto
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Nenhum produto encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
