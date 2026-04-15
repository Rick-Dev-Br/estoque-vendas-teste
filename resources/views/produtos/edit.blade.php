@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-pencil-square me-2"></i> Editar Produto
                </h5>
            </div>

            <div class="card-body">
                <form action="{{ route('produtos.update', $produto) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="codigo" class="form-label">Código (SKU) *</label>
                        <input type="text"
                            class="form-control @error('codigo') is-invalid @enderror"
                            id="codigo"
                            name="codigo"
                            value="{{ old('codigo', $produto->codigo) }}"
                            required>
                        @error('codigo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome *</label>
                        <input type="text"
                            class="form-control @error('nome') is-invalid @enderror"
                            id="nome"
                            name="nome"
                            value="{{ old('nome', $produto->nome) }}"
                            required>
                        @error('nome')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="preco" class="form-label">Preço (R$) *</label>
                            <input type="number"
                                class="form-control @error('preco') is-invalid @enderror"
                                id="preco"
                                name="preco"
                                value="{{ old('preco', $produto->preco) }}"
                                step="0.01"
                                min="0.01"
                                required
                                data-clear-on-focus="1">
                            @error('preco')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="estoque" class="form-label">Estoque *</label>
                            <input type="number"
                                class="form-control @error('estoque') is-invalid @enderror"
                                id="estoque"
                                name="estoque"
                                value="{{ old('estoque', $produto->estoque) }}"
                                min="0"
                                required
                                data-clear-on-focus="1">
                            @error('estoque')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="row">
                            <label for="estoque_minimo" class="form-label">
                                <i class="bi bi-exclamation-triangle"></i> Estoque mínimo *
                            </label>
                        </div>
                        <input type="number"
                            class="form-control @error('estoque_minimo') is-invalid @enderror"
                            id="estoque_minimo"
                            name="estoque_minimo"
                            value="{{ old('estoque_minimo', $produto->estoque_minimo ?? 5) }}"
                            min="0"
                            required>
                            @error('estoque_minimo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="tipo_unidade" class="form-label">Tipo de unidade *</label>
                            <select class="form-select @error('tipo_unidade') is-invalid @enderror"
                                name="tipo_unidade" id="tipo_unidade" required>
                                @foreach (\App\Models\Produto::TIPOS_UNIDADE as $valor => $rotulo)
                                    <option value="{{ $valor }}" {{ old('tipo_unidade', $produto->tipo_unidade) === $valor ? 'selected' : '' }}>
                                        {{ $rotulo }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tipo_unidade')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="unidade_medida" class="form-label">Unidade de medida *</label>
                            <select class="form-select @error('unidade_medida') is-invalid @enderror"
                                name="unidade_medida" id="unidade_medida" required>
                                @foreach (\App\Models\Produto::UNIDADES_MEDIDA as $valor => $rotulo)
                                    <option value="{{ $valor }}" {{ old('unidade_medida', $produto->unidade_medida) === $valor ? 'selected' : '' }}>
                                        {{ strtoupper($rotulo) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('unidade_medida')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="unidade_quantidade" class="form-label">Quantidade por unidade *</label>
                            <input type="number"
                                class="form-control @error('unidade_quantidade') is-invalid @enderror"
                                name="unidade_quantidade"
                                value="{{ old('unidade_quantidade', $produto->unidade_quantidade) }}"
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
                    <div class="mb-3">
                        <label class="form-label">Status *</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input"
                                    type="radio"
                                    name="status"
                                    id="status_ativo"
                                    value="ativo"
                                    {{ old('status', $produto->status) == 'ativo' ? 'checked' : '' }}>
                                <label class="form-check-label" for="status_ativo">
                                    Ativo
                                </label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input"
                                    type="radio"
                                    name="status"
                                    id="status_inativo"
                                    value="inativo"
                                    {{ old('status', $produto->status) == 'inativo' ? 'checked' : '' }}>
                                <label class="form-check-label" for="status_inativo">
                                    Inativo
                                </label>
                            </div>
                        </div>
                        @error('status')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('produtos.index') }}" class="btn btn-secondary">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-6">
                        <form action="{{ route('produtos.toggle-status', $produto) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-outline-{{ $produto->status == 'ativo' ? 'warning' : 'success' }} w-100">
                                {{ $produto->status == 'ativo' ? 'Inativar' : 'Ativar' }}
                            </button>
                        </form>
                    </div>
                    <div class="col-6">
                        <form action="{{ route('produtos.destroy', $produto) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                Excluir
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
