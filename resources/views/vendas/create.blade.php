@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-cart-plus me-2"></i> Nova Venda
                </h5>
            </div>

            <div class="card-body">
                <form action="{{ route('vendas.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cliente_id" class="form-label">Cliente *</label>
                            <select class="form-select @error('cliente_id') is-invalid @enderror"
                                    id="cliente_id" name="cliente_id" required>
                                <option value="">Selecione um cliente</option>
                                @foreach($clientes as $cliente)
                                    @if($cliente->status == 'ativo')
                                    <option value="{{ $cliente->id }}"
                                        data-endereco="{{ $cliente->endereco }}"
                                        data-numero="{{ $cliente->numero }}"
                                        data-complemento="{{ $cliente->complemento }}"
                                        data-bairro="{{ $cliente->bairro }}"
                                        data-cidade="{{ $cliente->cidade }}"
                                        data-estado="{{ $cliente->estado }}"
                                        data-cep="{{ $cliente->cep }}"
                                        {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                        {{ $cliente->nome }}
                                    </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('cliente_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="data_compra" class="form-label">Data de compra</label>
                            <input type="datetime-local" class="form-control @error('data_compra') is-invalid @enderror"
                                id="data_compra" name="data_compra"
                                value="{{ old('data_compra') }}">
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
                                    <option value="{{ $labelFormaPagamento }}" {{ old('forma_pagamento') === $labelFormaPagamento ? 'selected' : '' }}>
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
                                    {{ old('usar_endereco_cliente', '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="usar_endereco_cliente_sim">
                                    Usar endereço do cliente (padrão)
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="usar_endereco_cliente"
                                    id="usar_endereco_cliente_nao" value="0"
                                    {{ old('usar_endereco_cliente') == '0' ? 'checked' : '' }}>
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
                                value="{{ old('endereco_entrega') }}">
                            @error('endereco_entrega')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="numero" class="form-label">Número</label>
                            <input type="text" class="form-control @error('numero') is-invalid @enderror"
                                id="numero" name="numero"
                                value="{{ old('numero') }}">
                            @error('numero')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="complemento" class="form-label">Complemento</label>
                            <input type="text" class="form-control @error('complemento') is-invalid @enderror"
                                id="complemento" name="complemento"
                                value="{{ old('complemento') }}">
                            @error('complemento')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="bairro" class="form-label">Bairro</label>
                            <input type="text" class="form-control @error('bairro') is-invalid @enderror"
                                id="bairro" name="bairro"
                                value="{{ old('bairro') }}">
                            @error('bairro')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="cidade" class="form-label">Cidade</label>
                            <input type="text" class="form-control @error('cidade') is-invalid @enderror"
                                id="cidade" name="cidade"
                                value="{{ old('cidade') }}">
                            @error('cidade')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <input type="text" class="form-control @error('estado') is-invalid @enderror"
                                id="estado" name="estado"
                                value="{{ old('estado') }}" maxlength="2">
                            @error('estado')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="cep" class="form-label">CEP</label>
                            <input type="text" class="form-control @error('cep') is-invalid @enderror"
                                id="cep" name="cep"
                                value="{{ old('cep') }}">
                            @error('cep')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Itens de venda *</label>

                        <div id="produtos-container">

                            <div class="row mb-2 produto-item">
                                <div class="col-md-6">
                                    <select class="form-select produto-select" name="itens[0][produto_id]" required>
                                        <option value="">Selecione um produto</option>
                                        @foreach($produtos as $produto)
                                            @if($produto->status == 'ativo' && $produto->estoque > 0)
                                            <option value="{{ $produto->id }}" data-preco="{{ $produto->preco }}">
                                                {{ $produto->nome }} - R$ {{ number_format($produto->preco, 2, ',', '.') }} (Estoque: {{ $produto->estoque }})
                                            </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" class="form-control quantidade"
                                        name="itens[0][quantidade]" min="1" value="1" required
                                        data-clear-on-focus="1">
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-danger btn-remover" style="display: none;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button type="button" id="btn-adicionar-produto" class="btn btn-sm btn-outline-primary mt-2">
                            <i class="bi bi-plus-circle"></i> Adicionar Produto
                        </button>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        Apenas clientes ativos e produtos ativos com estoque são mostrados.
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('vendas.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Voltar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Finalizar Venda
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
    let produtoCount = 1;
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
            select.name = select.name.replace('[0]', `[${produtoCount}]`);
            select.value = '';
        });

        inputs.forEach(input => {
            input.name = input.name.replace('[0]', `[${produtoCount}]`);
            input.value = 1;
        });


        novoItem.querySelector('.btn-remover').style.display = 'block';

        container.appendChild(novoItem);
        produtoCount++;
    });


    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-remover') || e.target.closest('.btn-remover')) {
            const item = e.target.closest('.produto-item');
            if (document.querySelectorAll('.produto-item').length > 1) {
                item.remove();
            }
        }
    });

    alternarEndereco();
});
</script>
@endpush
@endsection
