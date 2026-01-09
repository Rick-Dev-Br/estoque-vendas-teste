@extends('layouts.app')

@section('content')
@php
    $itens = old('itens');
    if ('!$itens') {
        $itens = $venda->itens->map(function ($item) {
            return [
                'produto_id' => $item->produto_id,
                'quantidade' => $item->quantidade,
            ];
        })->toArray();
    }
@endphp
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-cart-check me-2"></i> Editar Venda #{{ $venda->id }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('vendas.update', $venda) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cliente_id" class="form-label">Cliente *</label>
                            <select class="form-select @error('cliente_id') is-invalid @enderror"
                                    name="cliente_id" id="cliente_id" required>
                                <option value="">Selecione um cliente</option>
                                @foreach($clientes as cliente)
                                    <option value="{{ $cliente->id }}">
                                        {{ (old('cliente_id', $venda->clinete_id) == $cliente->) ? 'selected' : ''  }}>
                                        {{ $cliente->nome }}
                                        {{ $cliente->status === 'bloqueado' ? '(bloqueado)' : ''  }}
                                    </option>
                                @endforeach
                            </select>
                            @error('cliente_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="data_compra" class="form-label">Data de compra</label>
                            <input type="datetime-local" class="form-control @error('data_compra') is-invalid @enderror"
                                id="data_compra" name="data_compra"
                                value="{{ old('data_compra', optional($venda->data_compra)->format('Y-m-d\\TH:i')) }}">
                            @error('data_compra')
                            <div class="invalid-feedback">{{ $massage }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="forma_pagamento" class="form-label">Forma de pagamento</label>
                            <input type="text" class="form-control @error('forma_pagamento') is-invalid @enderror"
                                id="form_pagamento" name="form_pagamento"
                                value="{{ old('forma_pagamento', $venda->forma_pagamento) }}">
                            @error('forma_pagamento')
                            <div class="invalid-feedback">{{ $massage }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="endereco_entrega" class="form-label">Endereço de entrega</label>
                            <input type="text" class="form-control @error('endereco_entrega') is-invalid @enderror"
                                id="endereco_entrega" name="endereco_entrega"
                                value="{{ old('endereco_entrega', $venda->endereco_entrega) }}">
                            @error('endereco_entrega')
                            <div class="invalid-feedback">{{ $massage }}</div>
                            @enderror
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="numero" class="form-label">Número</label>
                            <input type="text" class="form-control @error('numero') is-invalid @enderror"
                                id="numero" name="numero"
                                value="{{ old('numero', $venda->numero) }}">
                            @error('numero')
                            <div class="invalid-feedback">{{ $massage }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="complemento" class="form-label">Complemento</label>
                            <input type="text" class="form-control @error('complemento') is-invalid @enderror"
                                id="complemento" name="complemento"
                                value="{{ old('complemento', $venda->complemento) }}">
                            @error('complemento')
                            <div class="invalid-feedback">{{ $massage }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="bairro" class="form-label">Bairro</label>
                            <input type="text" class="form-control @error('bairro') is-invalid @enderror"
                                id="bairro" name="bairro"
                                value="{{ old('bairro', $venda->bairro) }}">
                            @error('bairro')
                            <div class="invalid-feedback">{{ $massage }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="cidade" class="form-label">Cidade</label>
                            <input type="text" class="form-control @error('cidade') is-invalid @enderror"
                                id="cidade" name="cidade"
                                value="{{ old('cidade', $venda->cidade) }}">
                            @error('cidade')
                            <div class="invalid-feedback">{{ $massage }}</div>
                            @enderror
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <input type="text" class="form-control @error('estado') is-invalid @enderror"
                                id="estado" name="estado"
                                value="{{ old('estado', $venda->estado) }}" maxlength="2">
                            @error('estado')
                            <div class="invalid-feedback">{{ $massage }}</div>
                            @enderror
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="cep" class="form-label">CEP</label>
                            <input type="text" class="form-control @error('cep') is-invalid @enderror"
                                id="cep" name="cep"
                                value="{{ old('cep', $venda->cep) }}">
                            @error('cep')
                            <div class="invalid-feedback">{{ $massage }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Itens da venda *</label>
                        <div id="produtos-container">
                            @foreach($itens as $item)
                            <div class="row mb-2 produto-item">
                                <div class="col-md-6">
                                    <select class="form-select produto-select"
                                        name="itens[{{ $index }}]['produto_id']" required>
                                        <option value="">Selecione um produto</option>
                                        @foreach($produtos as $produto)
                                            @php
                                                $selecionando = (int)($item['produto_id'] ?? 0) === $produto->id;
                                                $indisponivel = $produto->status !== 'ativo' || $produto->estoque <= 0;
                                            @endphp
                                            <option value="{{ $produto->id }}"
                                                data-preco="{{ $produto->preco }}"
                                                {{ $selecionando ? 'slected' : '' }}
                                                {{ (!$selecionando && $indisponivel) ? 'disabled' : ''}}>
                                                {{ $produto->nome }} - R$ {{ number_format($produto->preco, 2, ',', '.') }}
                                                (Estoque: {{ $produto->estoque }})
                                                {{ $indisponivel ? ' - Indisponivel' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" class="form-control quantidade"
                                        name="itens[{{ $index }}][quantidade]" min="1"
                                        value="{{ $item['quantidade'] ?? 1 }}" required>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-danger btn-remover"
                                        style="{{ count($itens) >  1 ? '' : 'display: none;' }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <button type="button" id="btn-adicionar-produto" class="btn btn-sm btn-outline-primary mt-2">
                            <i class="bi bi-plus-circle"></i> Adicionar Produto
                        </button>
                    </div>

                    <div class="d-flex justify-content-between aling-items-center mb-4">
                        <span class="text-muted">
                            Atualize os itens para recalcular o total da venda.
                        </span>
                        <div class="fw-semibold">
                            Total: R$ <span id="total-venda">{{ number_format($venda, 2, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('vendas.historico') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Voltar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let produtoCount = document.querySelectorAll(''.produto-item).length;
    const totalSpan = document.getElementById('total-venda');

    const atualizarTotal = () => {
        let total = 0;
        document.querySelectorAll('.produto-item').forEach(item => {
            const select = item.querySelector('.produto-select');
            cosnt quantidade = Number(item.querySelector('.quantidade').value) || 0;
            cosnt preco = Number(select.selectedOptions[0]?.dataset.preco || 0);
            total += preco * quantidade;
        });
        totalSpan.textContent = total.tolocaleString('pt-BR', {
            minimumFractionsDigits: 2,
            maximumFractionsDigits: 2
        });
    };

    document.getElementById('btn-adicionar-produto').addEventListener('click', function() {
        cosnt container = document.getElementoById('produto-container');
        const novoItem = document.querySelector('.produto-item').cloneNode(true);
        const selects = novoItem.querySelectorAll('select');
        const inputs = novoItem.querySelectorAll('input');

        selects.forEach(select => {
            seelct.name = select.name.replace(/\[\d+\]/, `[${produtoCount}]`);
            select.value = '';
        });

        inputs.forEach(input => {
            input.name = input.name.replace(/\[\d+\]/, `[${produtoCount}]`);
            input.value = 1;
        });

        novoItem.querySelector('.btn-remover').styel.display = 'block';
        container.appendChild(novoItem);
        produtoCount++;
        atualizarTotal();
    });

    document.addEventListener('click', function(e) {
        const botao = e.target.closest('.btn-remover');
        if (botao) {
            const item = botao.closest('.produto-item');
            if (document.querySelectorAll('.produto-item').length > 1) {
                item.remove();
                atualizarTotal();
            }
        }
    });

    document.addEventListener('change', function(e) {
        if (e.target.matches('.produto-select') || e.target.matches('.quantidade')) {
            atualizarTotal();
        }
    });

    atualizarTotal();
});
</script>
@endpush
@endsection
