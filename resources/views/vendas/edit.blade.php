@extends('layouts.app')

@section('content')
@php
    $itens = old('itens');
    if (!$itens) {
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
                                @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->id }}"
                                        data-endereco="{{ $cliente->endereco }}"
                                        data-numero="{{ $cliente->numero }}"
                                        data-complemento="{{ $cliente->complemento }}"
                                        data-bairro="{{ $cliente->bairro }}"
                                        data-cidade="{{ $cliente->cidade }}"
                                        data-estado="{{ $cliente->estado }}"
                                        data-cep="{{ $cliente->cep }}"
                                        {{ old('cliente_id', $venda->cliente_id) == $cliente->id ? 'selected' : '' }}>
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
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="forma_pagamento" class="form-label">Forma de pagamento</label>
                            @php
                                $formasPagamento = [
                                    'pix' => 'Pix',
                                    'cartao_credito' => 'Cartão de crédito',
                                    'cartao_debito' => 'Cartão de débito',
                                    'dinheiro' => 'Dinheiro',
                                    'transferencia_bancaria' => 'Transferência bancária',
                                    'boleto' => 'Boleto bancário',
                                    'carteira_digital' => 'Carteira digital',
                                    'link_pagamento' => 'Link de pagamento',
                                ];
                            @endphp
                            <select class="form-select @error('forma_pagamento') is-invalid @enderror"
                                id="forma_pagamento" name="forma_pagamento">
                                <option value="">Selecione</option>
                                @foreach($formasPagamento as $valorFormaPagamento => $labelFormaPagamento)
                                    <option value="{{ $labelFormaPagamento }}" {{ old('forma_pagamento', $venda->forma_pagamento) === $labelFormaPagamento ? 'selected' : '' }}>
                                        {{ $labelFormaPagamento }}
                                    </option>
                                @endforeach
                            </select>
                            @error('forma_pagamento')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label d-block">Endereço de entrega</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="usar_endereco_cliente"
                                    id="usar_endereco_cliente_sim" value="1"
                                    {{ old('usar_endereco_cliente', $venda->usar_endereco_cliente ? '1' : '0') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="usar_endereco_cliente_sim">
                                    Usar endereço do cliente (padrão)
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="usar_endereco_cliente"
                                    id="usar_endereco_cliente_nao" value="0"
                                    {{ old('usar_endereco_cliente', $venda->usar_endereco_cliente ? '1' : '0') == '0' ? 'checked' : '' }}>
                                <label class="form-check-label" for="usar_endereco_cliente_nao">
                                    Informar outro endereço de entrega
                                </label>
                            </div>
                            @error('usar_endereco_cliente')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                            <div id="endereco-cliente-resumo" class="text-muted small mt-2 d-none"></div>
                        </div>
                    </div>

                    <div class="row" id="endereco-personalizado">
                        <div class="col-md-6 mb-3">
                            <label for="endereco_entrega" class="form-label">Endereço de entrega</label>
                            <input type="text" class="form-control @error('endereco_entrega') is-invalid @enderror"
                                id="endereco_entrega" name="endereco_entrega"
                                value="{{ old('endereco_entrega', $venda->endereco_entrega) }}">
                            @error('endereco_entrega')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="numero" class="form-label">Número</label>
                            <input type="text" class="form-control @error('numero') is-invalid @enderror"
                                id="numero" name="numero"
                                value="{{ old('numero', $venda->numero) }}">
                            @error('numero')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="complemento" class="form-label">Complemento</label>
                            <input type="text" class="form-control @error('complemento') is-invalid @enderror"
                                id="complemento" name="complemento"
                                value="{{ old('complemento', $venda->complemento) }}">
                            @error('complemento')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="bairro" class="form-label">Bairro</label>
                            <input type="text" class="form-control @error('bairro') is-invalid @enderror"
                                id="bairro" name="bairro"
                                value="{{ old('bairro', $venda->bairro) }}">
                            @error('bairro')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="cidade" class="form-label">Cidade</label>
                            <input type="text" class="form-control @error('cidade') is-invalid @enderror"
                                id="cidade" name="cidade"
                                value="{{ old('cidade', $venda->cidade) }}">
                            @error('cidade')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <input type="text" class="form-control @error('estado') is-invalid @enderror"
                                id="estado" name="estado"
                                value="{{ old('estado', $venda->estado) }}" maxlength="2">
                            @error('estado')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="cep" class="form-label">CEP</label>
                            <input type="text" class="form-control @error('cep') is-invalid @enderror"
                                id="cep" name="cep"
                                value="{{ old('cep', $venda->cep) }}">
                            @error('cep')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Itens da venda *</label>
                        <div id="produtos-container">
                            @foreach($itens as $index => $item)
                            <div class="row mb-2 produto-item">
                                <div class="col-md-6">
                                    <select class="form-select produto-select"
                                        name="itens[{{ $index }}][produto_id]" required>
                                        <option value="">Selecione um produto</option>
                                        @foreach($produtos as $produto)
                                            @php
                                                $selecionando = (int)($item['produto_id'] ?? 0) === $produto->id;
                                                $indisponivel = $produto->status !== 'ativo' || $produto->estoque <= 0;
                                            @endphp
                                            <option value="{{ $produto->id }}"
                                                data-preco="{{ $produto->preco }}"
                                                {{ $selecionando ? 'selected' : '' }}
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
                                        value="{{ $item['quantidade'] ?? 1 }}" required
                                        data-clear-on-focus="1">
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
                            Total: R$ <span id="total-venda">{{ number_format($venda->total, 2, ',', '.') }}</span>
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
    let produtoCount = document.querySelectorAll('.produto-item').length;
    const totalSpan = document.getElementById('total-venda');
    const usarEnderecoInputs = document.querySelectorAll('input[name="usar_endereco_cliente"]');
    const enderecoContainer = document.getElementById('endereco-personalizado');
    const resumoEndereco = document.getElementById('endereco-cliente-resumo');
    const clienteSelect = document.getElementById('cliente_id');
    const enderecoCampos = {
        endereco_entrega: document.getElementById('endereco_entrega'),
        numero: document.getElementById('numero'),
        complemento: document.getElementById('complemento'),
        bairro: document.getElementById('bairro'),
        cidade: document.getElementById('cidade'),
        estado: document.getElementById('estado'),
        cep: document.getElementById('cep'),
    };

    const atualizarTotal = () => {
        let total = 0;
        document.querySelectorAll('.produto-item').forEach(item => {
            const select = item.querySelector('.produto-select');
            const quantidade = Number(item.querySelector('.quantidade').value) || 0;
            const preco = Number(select.selectedOptions[0]?.dataset.preco || 0);
            total += preco * quantidade;
        });
        totalSpan.textContent = total.tolocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    };

    const obterEnderecoCliente = () => {
        const option = clienteSelect?.selectedOptions[0];
        if (!option) {
            return null;
        }

        return {
            endereco: option.dataset.endereco || '',
            numero: option.dataset.numero || '',
            complemento: option.dataset.complemento || '',
            bairro: option.dataset.bairro || '',
            cidade: option.dataset.cidade || '',
            estado: option.dataset.estado || '',
            cep: option.dataset.cep || '',
        };
    };

    const atualizarResumoEndereco = () => {
        if (!resumoEndereco) {
            return;
        }

        const endereco = obterEnderecoCliente();
        if (!endereco || !endereco.endereco) {
            resumoEndereco.textContent = 'Selecione um cliente para usar o endereço padrão.';
            resumoEndereco.classList.remove('d-none');
            return;
        }

        const complemento = endereco.complemento ? `, ${endereco.complemento}` : '';
        resumoEndereco.textContent = `${endereco.endereco}, ${endereco.numero || 's/n'}${complemento} - ${endereco.bairro || ''} - ${endereco.cidade || ''}/${endereco.estado || ''} (${endereco.cep || ''})`;
        resumoEndereco.classList.remove('d-none');
    };

    const aplicarEnderecoCliente = () => {
        const endereco = obterEnderecoCliente();
        if (!endereco) {
            return;
        }
        enderecoCampos.endereco_entrega.value = endereco.endereco;
        enderecoCampos.numero.value = endereco.numero;
        enderecoCampos.complemento.value = endereco.complemento;
        enderecoCampos.bairro.value = endereco.bairro;
        enderecoCampos.cidade.value = endereco.cidade;
        enderecoCampos.estado.value = endereco.estado;
        enderecoCampos.cep.value = endereco.cep;
    };

    const alternarEndereco = () => {
        const usarCliente = document.querySelector('input[name="usar_endereco_cliente"]:checked')?.value === '1';
        if (enderecoContainer) {
            enderecoContainer.style.display = usarCliente ? 'none' : '';
        }
        if (usarCliente) {
            aplicarEnderecoCliente();
            atualizarResumoEndereco();
        } else if (resumoEndereco) {
            resumoEndereco.classList.add('d-none');
        }
    };

    usarEnderecoInputs.forEach((input) => {
        input.addEventListener('change', alternarEndereco);
    });

    clienteSelect?.addEventListener('change', () => {
        const usarCliente = document.querySelector('input[name="usar_endereco_cliente"]:checked')?.value === '1';
        if (usarCliente) {
            aplicarEnderecoCliente();
            atualizarResumoEndereco();
        }
    });

    document.getElementById('btn-adicionar-produto').addEventListener('click', function() {
        const container = document.getElementById('produtos-container');
        const novoItem = document.querySelector('.produto-item').cloneNode(true);
        const selects = novoItem.querySelectorAll('select');
        const inputs = novoItem.querySelectorAll('input');

        selects.forEach(select => {
            select.name = select.name.replace(/\[\d+\]/, `[${produtoCount}]`);
            select.value = '';
        });

        inputs.forEach(input => {
            input.name = input.name.replace(/\[\d+\]/, `[${produtoCount}]`);
            input.value = 1;
        });

        novoItem.querySelector('.btn-remover').style.display = 'block';
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
    alternarEndereco();
});
</script>
@endpush
@endsection
