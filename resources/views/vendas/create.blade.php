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
                                    <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
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
                            <input type="text" class="form-control @error('forma_pagamento') is-invalid @enderror"
                                id="forma_pagamento" name="forma_pagamento"
                                value="{{ old('forma_pagamento') }}">
                            @error('forma_pagamento')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
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
                                        name="itens[0][quantidade]" min="1" value="1" required>
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
});
</script>
@endpush
@endsection
