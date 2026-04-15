@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-person-vcard me-2"></i> Detalhes do Cliente
                </h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('clientes.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Voltar
                    </a>
                    <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-pencil me-1"></i> Editar
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div><strong>Nome:</strong> {{ $cliente->nome }}</div>
                        <div><strong>Nome completo:</strong> {{ $cliente->nome_completo ?: '-' }}</div>
                        <div><strong>E-mail:</strong> {{ $cliente->email }}</div>
                        <div><strong>CPF:</strong> {{ $cliente->cpf ?: '-' }}</div>
                        <div><strong>Telefone:</strong> {{ $cliente->telefone ?: '-' }}</div>
                    </div>
                    <div class="col-md-6">
                        <div>
                            <strong>Status:</strong>
                            <span class="badge bg-{{ $cliente->status_classe }}">
                                {{ $cliente->status_formatado }}
                            </span>
                        </div>
                        <div><strong>Endereço:</strong> {{ $cliente->endereco ?: '-' }}</div>
                        <div><strong>Número:</strong> {{ $cliente->numero ?: '-' }}</div>
                        <div><strong>Complemento:</strong> {{ $cliente->complemento ?: '-' }}</div>
                        <div><strong>Bairro:</strong> {{ $cliente->bairro ?: '-' }}</div>
                        <div><strong>Cidade/UF:</strong> {{ $cliente->cidade ?: '-' }} / {{ $cliente->estado ?: '-' }}</div>
                        <div><strong>CEP:</strong> {{ $cliente->cep ?: '-' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="bi bi-clock-history me-2"></i> Últimas vendas
                </h6>
            </div>
            <div class="card-body">
                @if($cliente->vendas->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Data</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cliente->vendas as $venda)
                                    <tr>
                                        <td>#{{ $venda->id }}</td>
                                        <td>R$ {{ number_format((float) $venda->total, 2, ',', '.') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $venda->status_classe }}">
                                                {{ $venda->status_formatado }}
                                            </span>
                                        </td>
                                        <td>{{ optional($venda->data_compra ?? $venda->created_at)->format('d/m/Y H:i') }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('vendas.show', $venda) }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0">Este cliente ainda não realizou vendas.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
