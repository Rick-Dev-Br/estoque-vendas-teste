@extends('layouts.app')

@section('content')
<div class="text-center mb-5">
    <h1 class="display-4 mb-3">
        <i class="bi bi-shop"></i> Teste de sistema de Vendas e Estoque
    </h1>
    <p class="lead text-muted">
        Sistema de teste pra treinamento e aprendizado, para gestão de produtos clientes e vendas
    </p>
</div>

<div class="row">
    <div class="col-mb-4 mb-4">
        <div class="card h-100 border-primary">
            <div class="card-body text-center">
                <i class="bi bi-box-seam display-1 text-primary mb-3"></i>
                <h3 class="card-title">Produtos</h3>
                <p class="card-text">
                    Cadastre, edite e gerencie seu catálogo de produtos.
                    Controle estoque e status dos itens.
                </p>
                <a href="{{ route('produtos.index') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-right"></i> Gerenciar Produtos
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card h-100 border-success">
            <div class="card-body text-center">
                <i class="bi bi-people display-1 text-success mb-3"></i>
                <h3 class="card-title">Clientes</h3>
                <p class="card-text">
                    Gerencia todos os seus clientes.
                    Controle status e informações de contato.
                </p>
                <a href="{{ route('clientes.index') }}" class="btn btn-success">
                    <i class="bi bi-arrow-right"></i> Gerenciar Cliente
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card h-100 border-warning">
            <div class="card-body text-center">
                <i class="bi bi-cart-check display-1 text-warning mb-3"></i>
                <h3 class="card-title">Vendas</h3>
                <p class="card-text">
                    Realize vendas, controle status e
                    gerencie todo o processo de vendas.
                </p>
                <a href="{{ route('vendas.index') }}" class="btn btn-warning">
                    <i class="bi bi-arrow-right"></i> Gerenciar Vendas
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-5">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-info-circle me-2"></i> Como Funciona
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Funcinalidade</h6>
                        <ul>
                            <li>CRUD completo de Produtos e clientes</li>
                            <li>Controle de status (Ativo/Inativo, Ativo/Bloqueado)</li>
                            <li>Sistema de vendas com mútiplos itens</li>
                            <li>Validações de negócio em tempo real</li>
                            <li>Contole de estoque automático</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Regras de Negócio</h6>
                        <ul>
                            <li>Produto inativo não pode ser vendido</li>
                            <li>Cliente bloqueado não pode comprar</li>
                            <li>Venda só é paga se tiver estoque suficiente</li>
                            <li>Venda paga não pode ser cancelada</li>
                            <li>Estoque é atualizado automaticamente</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
