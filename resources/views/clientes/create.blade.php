@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-person-plus me-2"></i> Novo Cliente
                </h5>
            </div>

            <div class="card-body">
                <form action="{{ route('clientes.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nome" class="form-label">Nome *</label>
                            <input type="text" class="form-control @error('nome') is-invalid @enderror"
                                id="nome" name="nome" value="{{ old('nome') }}" required>
                            @error('nome')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="nome_completo" class="form-label">Nome completo</label>
                            <input type="text" class="form-control @error('nome_completo') is-invalid @enderror"
                                id="nome_completo" name="nome_completo"
                                value="{{ old('nome_completo') }}">
                            @error('nome_completo')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">E-mail *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cpf" class="form-label">CPF</label>
                            <input type="text" class="form-control @error('cpf') is-invalid @enderror"
                                id="cpf" name="cpf" value="{{ old('cpf') }}">
                            @error('cpf')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="text" class="form-control @error('telefone') is-invalid @enderror"
                                id="telefone" name="telefone" value="{{ old('telefone') }}">
                            @error('telefone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-select @error('status') is-invalid @enderror"
                                    id="status" name="status" required>
                                <option value="ativo" {{ old('status', 'ativo') == 'ativo' ? 'selected' : '' }}>Ativo</option>
                                <option value="bloqueado" {{ old('status') == 'bloqueado' ? 'selected' : '' }}>Bloqueado</option>
                            </select>
                            @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="endereco" class="form-label">Endereço</label>
                            <input type="text" class="form-control @error('endereco') is-invalid @enderror"
                                id="endereco" name="endereco" value="{{ old('endereco') }}">
                            @error('endereco')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="numero" class="form-label">Número</label>
                            <input type="text" class="form-control @error('numero') is-invalid @enderror"
                                id="numero" name="numero" value="{{ old('numero') }}">
                            @error('numero')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="complemento" class="form-label">Complemento</label>
                            <input type="text" class="form-control @error('complemento') is-invalid @enderror"
                                id="complemento" name="complemento" value="{{ old('complemento') }}">
                            @error('complemento')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="bairro" class="form-label">Bairro</label>
                            <input type="text" class="form-control @error('bairro') is-invalid @enderror"
                                id="bairro" name="bairro" value="{{ old('bairro') }}">
                            @error('bairro')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="cidade" class="form-label">Cidade</label>
                            <input type="text" class="form-control @error('cidade')  is-invalid @enderror"
                                id="cidade" name="cidade"  value="{{ old('cidade') }}">
                            @error('cidade')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <input type="text" class="form-control @error('estado') is-invalid @enderror"
                                id="estado" name="estado" value="{{ old('estado') }}" maxlength="2">
                            @error('estado')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="cep" class="form-label">CEP</label>
                            <input type="text" class="form-control @error('cep') is-invalid @enderror"
                                id="cep" name="cep" value="{{ old('cep') }}">
                            @error('cep')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 justify-content-between">
                        <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Voltar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
