@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-receipt me-2"></i> Venda #{{ $venda->id }}
                </h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('vendas.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Voltar
                    </a>
                    @if($venda->status === 'pendente')
                        <a href="{{ route('vendas.edit', $venda) }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-pencil me-1"></i> Editar
                        </a>
                    @endif
                </div>
            </div>

            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div><strong>Cliente:</strong> {{ $venda->cliente->nome ?? 'Não informado' }}</div>
                        <div><strong>Forma de pagamento:</strong> {{ $venda->forma_pagamento ?: '-' }}</div>
                        <div>
                            <strong>Canal da venda:</strong>
                            <span class="badge bg-{{ $venda->canal_venda_classe }}">
                                {{ $venda->canal_venda_formatado }}
                            </span>
                        </div>
                        <div>
                            <strong>Status:</strong>
                            <span class="badge bg-{{ $venda->status_classe }}">
                                {{ $venda->status_formatado }}
                            </span>
                        </div>
                        <div><strong>Data:</strong> {{ optional($venda->data_compra ?? $venda->created_at)->format('d/m/Y H:i') }}</div>
                        <div><strong>Total:</strong> R$ {{ number_format((float) $venda->total, 2, ',', '.') }}</div>
                    </div>

                    <div class="col-md-6">
                        <div><strong>Endereço:</strong> {{ $venda->endereco_entrega ?: '-' }}</div>
                        <div><strong>Número:</strong> {{ $venda->numero ?: '-' }}</div>
                        <div><strong>Complemento:</strong> {{ $venda->complemento ?: '-' }}</div>
                        <div><strong>Bairro:</strong> {{ $venda->bairro ?: '-' }}</div>
                        <div><strong>Cidade/UF:</strong> {{ $venda->cidade ?: '-' }} / {{ $venda->estado ?: '-' }}</div>
                        <div><strong>CEP:</strong> {{ $venda->cep ?: '-' }}</div>
                        <div>
                            <strong>Origem do endereço:</strong>
                            {{ $venda->usar_endereco_cliente ? 'Endereço padrão do cliente' : 'Endereço informado na venda' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-box-seam me-2"></i> Itens da venda
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Produto</th>
                                <th>Quantidade</th>
                                <th>Preço unitário</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($venda->itens as $item)
                                <tr>
                                    <td>{{ $item->produto->nome ?? 'Produto removido' }}</td>
                                    <td>{{ $item->quantidade }}</td>
                                    <td>R$ {{ number_format((float) $item->preco_unitario, 2, ',', '.') }}</td>
                                    <td>R$ {{ number_format((float) $item->subtotal, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total</th>
                                <th>R$ {{ number_format((float) $venda->total, 2, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
