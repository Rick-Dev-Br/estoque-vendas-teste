@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-plus-circle me-2"></i> Novo Produto
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('produtos.store') }}" method="POST" class="p-4 border rounded">
                    @csrf
                    <div class="mb-3">
                        <label for="codigo" class="form-label">
                            <i class="bi bi-upc-scan"></i> Código (SKU) *
                        </label>
                        <input type="text" class="form-control @error('codigo') is-invalid @enderror"
                            id="codigo" name="codigo" value="{{ old('codigo') }}" required
                            placeholder="Ex: PRD-000001">
                        @error('codigo')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="nome" class="form-label">
                            <i class="bi bi-tag"></i> Nome do Produto *
                        </label>
                        <input type="text" class="form-control @error('nome') is-invalid @enderror"
                        id="nome" name="nome" value="{{ old('nome') }}" required placeholder="Digite o nome do produto">
                    @error('nome')
                    <div class="invalid-feedback">{{ $message}}</div>
                    @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="preco" class="form-label">
                                <i class="bi bi-currency-dollar"></i> Preço (R$) *
                            </label>
                            <input type="number" class="form-control @error('preco') is-invalid @enderror"
                            id="preco" name="preco" value="{{ old('preco') }}" step="0.01" min="0.01"
                            required placeholder="0,00" data-clear-on-focus="1">
                    @error('preco')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="estoque" class="form-label">
                                <i class="bi bi-box"></i> Estoque Inicial *
                            </label>
                            <input
                                type="number"
                                class="form-control @error('estoque') is-invalid @enderror"
                                id="estoque"
                                name="estoque"
                                value="{{ old('estoque', 0) }}"
                                min="0"
                                required
                                data-clear-on-focus="1">

                    @error('estoque')
                            <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="tipo_unidade" class="form-label">
                                <i class="bi bi-boxes"></i> Tipo de unidade *
                            </label>
                            <select class="form-select @error('tipo_unidade') is-invalid @enderror"
                                id="tipo_unidade" name="tipo_unidade" required>
                                @foreach (\App\Models\Produto::TIPOS_UNIDADE as $valor => $rotulo)
                                    <option value="{{ $valor }}" {{ old('tipo_unidade', 'unidade') === $valor ? 'selected' : '' }}>
                                        {{ $rotulo }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tipo_unidade')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="unidade_medida" class="form-label">
                                <i class="bi bi-rulers"></i> Unidade de medida *
                            </label>
                            <select class="form-select @error('unidade_medida') is-invalid @enderror"
                                id="unidade_medida" name="unidade_medida" required>
                                @foreach (\App\Models\Produto::UNIDADES_MEDIDA as $valor => $rotulo)
                                    <option value="{{ $valor }}" {{ old('unidade_medida', 'un') === $valor ? 'selected' : '' }}>
                                        {{ strtoupper($rotulo) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('unidade_medida')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="unidade_quantidade" class="form-label">
                                <i class="bi bi-box-seam"></i> Quantidade por unidade *
                            </label>
                            <input type="number"
                                class="form-control @error('unidade_quantidade') is-invalid @enderror"
                                id="unidade_quantidade"
                                name="unidade_quantidade"
                                value="{{ old('unidade_quantidade', 1) }}"
                                step="0.01"
                                min="0.01"
                                required
                                placeholder="Ex: 50 (kg por saco), 12 (un por caixa)"
                                data-clear-on-focus="1">
                            @error('unidade_quantidade')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="d-flex justify-contente-between mt-4">
                        <a href="{{ route('produtos.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Voltar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Salvar Produto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
