@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h3>Histórico de Vendas</h3>
        <a class="btn btn-primary" href="{{ route('vendas.create') }}">Nova Venda</a>
    </div>

    @foreach($vendas as $venda)
        <div class="card mb-3">
            <div class="card-header">
                <strong>#{{ $venda->id }}</strong> |
                <strong>Cliente:</strong> {{ $venda->cliente?->nome ?? '---' }} |
                <strong>Status:</strong> {{ $venda->status }} |
                <strong>Total:</strong> R$ {{ number_format($venda->total,2,',','.') }} |
                <strong>Data:</strong> {{ optional($venda->data_compra ?? $venda->created_at)->format('d/m/Y H:i') }}
            </div>
            <div class="card-body">
                <p class="mb-1"><strong>Pagamento:</strong> {{ $venda->forma_pagamento ?? '---' }}</p>
                <p class="mb-2"><strong>Entrega:</strong>
                    {{ $venda->endereco_entrega ?? '---' }},
                    {{ $venda->numero ?? '' }} - {{ $venda->bairro ?? '' }} - {{ $venda->cidade ?? '' }}/{{ $venda->estado ?? '' }}
                    ({{ $venda->cep ?? '' }})
                </p>

                <table class="table table-sm">
                    <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Qtd</th>
                        <th>Preço</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($venda->itens as $item)
                        <tr>
                            <td>{{ $item->produto?->nome ?? '---' }}</td>
                            <td>{{ $item->quantidade }}</td>
                            <td>R$ {{ number_format($item->preco_unitario,2,',','.') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                @if($venda->status === 'pendente')
                    <a class="btn btn-warning btn-sm" href="{{ route('vendas.edit', $venda) }}">Editar (pendente)</a>
                @endif
            </div>
        </div>
    @endforeach
</div>
@endsection
