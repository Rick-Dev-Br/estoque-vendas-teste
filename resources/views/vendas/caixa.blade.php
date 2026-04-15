@extends('layouts.app')

@section('content')
@php
    $itens = old('itens', [
        ['produto_id' => '', 'quantidade' => 1],
    ]);

    $formasPagamento = [
        'Pix',
        'Cartão de crédito',
        'Cartão de débito',
        'Dinheiro',
        'Transferência bancária',
        'Boleto bancário',
        'Carteira digital',
        'Link de pagamento',
    ];
@endphp

<div class="container pb-4">
    <form action="{{ route('vendas.store') }}" method="POST" class="pdv-page">
        @csrf

        <input type="hidden" name="canal_venda" value="loja_fisica">
        <input type="hidden" name="usar_endereco_cliente" value="1">

        <div class="card pdv-hero mb-4">
            <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <span class="pdv-eyebrow">Loja física</span>
                    <h2 class="h4 mb-1">Caixa PDV</h2>
                    <p class="text-muted mb-0">Passe os produtos na pistola, confira o total e registre a venda sem sair da tela.</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('vendas.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-list-ul me-1"></i> Lista de vendas
                    </a>
                    <a href="{{ route('vendas.create') }}" class="btn btn-outline-primary">
                        <i class="bi bi-bag me-1"></i> Pedido online / manual
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-xl-8">
                <div class="card pdv-panel mb-4">
                    <div class="card-body">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                            <div>
                                <h3 class="h6 mb-1">Leitura por código de barras</h3>
                                <p class="text-muted small mb-0">O leitor funciona como teclado. Mantenha o foco no campo abaixo.</p>
                            </div>
                            <span class="badge text-bg-light border">Itens na venda: <span id="pdv-item-count">{{ count($itens) }}</span></span>
                        </div>

                        <label for="codigo_barras_leitura" class="form-label visually-hidden">Leitura por código de barras</label>
                        <input
                            type="text"
                            class="form-control pdv-scanner-input"
                            id="codigo_barras_leitura"
                            placeholder="Passe a pistola ou digite o código e pressione Enter"
                            inputmode="numeric"
                            autocomplete="off"
                            autofocus>

                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-2">
                            <div class="form-text mb-0">Se o item já estiver na lista, a quantidade será incrementada automaticamente.</div>
                            <div id="barcode-feedback" class="small text-muted"></div>
                        </div>
                    </div>
                </div>

                <div class="card pdv-panel">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            <h3 class="h6 mb-1">Itens da venda</h3>
                            <p class="text-muted small mb-0">Você pode usar a pistola ou adicionar manualmente.</p>
                        </div>
                        <button type="button" id="btn-adicionar-produto" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-plus-circle me-1"></i> Adicionar produto
                        </button>
                    </div>
                    <div class="card-body">
                        @error('itens')
                            <div class="alert alert-danger py-2">{{ $message }}</div>
                        @enderror

                        <div id="produtos-container" class="d-grid gap-3">
                            @foreach($itens as $index => $item)
                                <div class="row g-2 align-items-end produto-item pdv-item-row">
                                    <div class="col-md-7">
                                        <label class="form-label">Produto</label>
                                        <select class="form-select produto-select" name="itens[{{ $index }}][produto_id]" required>
                                            <option value="">Selecione um produto</option>
                                            @foreach($produtos as $produto)
                                                <option value="{{ $produto->id }}"
                                                    data-preco="{{ $produto->preco }}"
                                                    {{ (string) ($item['produto_id'] ?? '') === (string) $produto->id ? 'selected' : '' }}>
                                                    {{ $produto->nome }} - R$ {{ number_format((float) $produto->preco, 2, ',', '.') }} (Estoque: {{ $produto->estoque }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Quantidade</label>
                                        <input type="number"
                                            class="form-control quantidade"
                                            name="itens[{{ $index }}][quantidade]"
                                            min="1"
                                            value="{{ $item['quantidade'] ?? 1 }}"
                                            required>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button"
                                            class="btn btn-outline-danger btn-remover w-100"
                                            style="{{ count($itens) > 1 ? '' : 'display: none;' }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card pdv-panel mb-4">
                    <div class="card-header">
                        <h3 class="h6 mb-0">Dados da venda</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="cliente_id" class="form-label">Cliente *</label>
                            <select class="form-select @error('cliente_id') is-invalid @enderror" id="cliente_id" name="cliente_id" required>
                                <option value="">Selecione um cliente</option>
                                @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                        {{ $cliente->nome }}
                                    </option>
                                @endforeach
                            </select>
                            @error('cliente_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="forma_pagamento" class="form-label">Forma de pagamento</label>
                            <select class="form-select @error('forma_pagamento') is-invalid @enderror" id="forma_pagamento" name="forma_pagamento">
                                <option value="">Selecione</option>
                                @foreach($formasPagamento as $formaPagamento)
                                    <option value="{{ $formaPagamento }}" {{ old('forma_pagamento') === $formaPagamento ? 'selected' : '' }}>
                                        {{ $formaPagamento }}
                                    </option>
                                @endforeach
                            </select>
                            @error('forma_pagamento')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-0">
                            <label for="data_compra" class="form-label">Data da venda</label>
                            <input type="datetime-local"
                                class="form-control @error('data_compra') is-invalid @enderror"
                                id="data_compra"
                                name="data_compra"
                                value="{{ old('data_compra', now()->format('Y-m-d\TH:i')) }}">
                            @error('data_compra')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="card pdv-total-card">
                    <div class="card-body">
                        <p class="pdv-total-label mb-2">Total da venda</p>
                        <div class="pdv-total-value mb-3">R$ <span id="total-venda">0,00</span></div>
                        <div class="alert alert-light border mb-4">
                            O PDV salva a venda como <strong>Loja física</strong> e usa o endereço padrão do cliente quando ele existir.
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle me-1"></i> Registrar venda no caixa
                            </button>
                            <a href="{{ route('vendas.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Voltar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let produtoCount = document.querySelectorAll('.produto-item').length;

    const produtosContainer = document.getElementById('produtos-container');
    const btnAdicionarProduto = document.getElementById('btn-adicionar-produto');
    const barcodeInput = document.getElementById('codigo_barras_leitura');
    const barcodeFeedback = document.getElementById('barcode-feedback');
    const totalSpan = document.getElementById('total-venda');
    const itemCountSpan = document.getElementById('pdv-item-count');
    const barcodeEndpoint = '{{ route('produtos.buscar-por-codigo-barras') }}';

    const mostrarFeedbackCodigoBarras = (mensagem, tipo = 'muted') => {
        if (!barcodeFeedback) {
            return;
        }

        barcodeFeedback.textContent = mensagem;
        barcodeFeedback.className = `small text-${tipo}`;
    };

    const obterLinhasProduto = () => Array.from(document.querySelectorAll('.produto-item'));

    const atualizarResumoItens = () => {
        if (itemCountSpan) {
            itemCountSpan.textContent = obterLinhasProduto().length;
        }
    };

    const atualizarTotal = () => {
        let total = 0;

        obterLinhasProduto().forEach((linha) => {
            const select = linha.querySelector('.produto-select');
            const quantidade = Number(linha.querySelector('.quantidade')?.value || 0);
            const preco = Number(select?.selectedOptions[0]?.dataset.preco || 0);

            total += preco * quantidade;
        });

        totalSpan.textContent = total.toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    };

    const renomearCamposLinha = (linha, index) => {
        linha.querySelectorAll('select, input').forEach((elemento) => {
            elemento.name = elemento.name.replace(/\[\d+\]/, `[${index}]`);
        });
    };

    const limparLinhaProduto = (linha) => {
        const select = linha.querySelector('.produto-select');
        const quantidade = linha.querySelector('.quantidade');

        if (select) {
            select.value = '';
        }

        if (quantidade) {
            quantidade.value = 1;
        }
    };

    const atualizarVisibilidadeBotoesRemover = () => {
        const mostrar = obterLinhasProduto().length > 1;

        obterLinhasProduto().forEach((linha) => {
            const botaoRemover = linha.querySelector('.btn-remover');

            if (botaoRemover) {
                botaoRemover.style.display = mostrar ? 'block' : 'none';
            }
        });
    };

    const criarNovaLinhaProduto = () => {
        const modelo = document.querySelector('.produto-item');
        const novaLinha = modelo.cloneNode(true);

        renomearCamposLinha(novaLinha, produtoCount);
        limparLinhaProduto(novaLinha);

        produtosContainer.appendChild(novaLinha);
        produtoCount++;

        atualizarVisibilidadeBotoesRemover();
        atualizarResumoItens();
        atualizarTotal();

        return novaLinha;
    };

    const encontrarLinhaVazia = () => {
        return obterLinhasProduto().find((linha) => {
            const select = linha.querySelector('.produto-select');
            return select && !select.value;
        });
    };

    const encontrarLinhaPorProduto = (produtoId) => {
        return obterLinhasProduto().find((linha) => {
            const select = linha.querySelector('.produto-select');
            return select && select.value === String(produtoId);
        });
    };

    const garantirOpcaoNoSelect = (select, produto) => {
        let option = Array.from(select.options).find((item) => item.value === String(produto.id));

        if (!option) {
            option = new Option(produto.label_venda, String(produto.id), true, true);
            option.dataset.preco = produto.preco;
            select.add(option);
        }

        option.dataset.preco = produto.preco;
        option.textContent = produto.label_venda;

        return option;
    };

    const adicionarProdutoLido = (produto) => {
        const linhaExistente = encontrarLinhaPorProduto(produto.id);

        if (linhaExistente) {
            const quantidadeInput = linhaExistente.querySelector('.quantidade');
            const quantidadeAtual = Number(quantidadeInput.value) || 0;
            const novaQuantidade = quantidadeAtual + 1;

            if (novaQuantidade > Number(produto.estoque)) {
                throw new Error(`Estoque insuficiente para ${produto.nome}.`);
            }

            quantidadeInput.value = novaQuantidade;
            atualizarTotal();
            return;
        }

        const linhaDestino = encontrarLinhaVazia() || criarNovaLinhaProduto();
        const select = linhaDestino.querySelector('.produto-select');
        const quantidadeInput = linhaDestino.querySelector('.quantidade');

        garantirOpcaoNoSelect(select, produto);
        select.value = String(produto.id);
        quantidadeInput.value = 1;

        atualizarResumoItens();
        atualizarTotal();
    };

    const buscarProdutoPorCodigoBarras = async (codigoBarras) => {
        const response = await fetch(`${barcodeEndpoint}?codigo_barras=${encodeURIComponent(codigoBarras)}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            throw new Error(data.message || 'Não foi possível localizar o produto.');
        }

        return data;
    };

    btnAdicionarProduto?.addEventListener('click', () => {
        criarNovaLinhaProduto();
    });

    document.addEventListener('click', function(event) {
        const botao = event.target.closest('.btn-remover');

        if (!botao) {
            return;
        }

        const item = botao.closest('.produto-item');

        if (obterLinhasProduto().length > 1) {
            item.remove();
            atualizarVisibilidadeBotoesRemover();
            atualizarResumoItens();
            atualizarTotal();
        }
    });

    document.addEventListener('change', function(event) {
        if (event.target.matches('.produto-select') || event.target.matches('.quantidade')) {
            atualizarTotal();
        }
    });

    barcodeInput?.addEventListener('keydown', async (event) => {
        if (event.key !== 'Enter') {
            return;
        }

        event.preventDefault();

        const codigoBarras = barcodeInput.value.trim();

        if (!codigoBarras) {
            mostrarFeedbackCodigoBarras('Informe ou leia um código de barras.', 'warning');
            return;
        }

        barcodeInput.disabled = true;
        mostrarFeedbackCodigoBarras('Buscando produto...', 'muted');

        try {
            const produto = await buscarProdutoPorCodigoBarras(codigoBarras);
            adicionarProdutoLido(produto);
            mostrarFeedbackCodigoBarras(`${produto.nome} adicionado à venda.`, 'success');
        } catch (error) {
            mostrarFeedbackCodigoBarras(error.message, 'danger');
        } finally {
            barcodeInput.disabled = false;
            barcodeInput.value = '';
            barcodeInput.focus();
        }
    });

    atualizarVisibilidadeBotoesRemover();
    atualizarResumoItens();
    atualizarTotal();

    if (barcodeInput) {
        barcodeInput.focus();
    }
});
</script>
@endpush
@endsection
