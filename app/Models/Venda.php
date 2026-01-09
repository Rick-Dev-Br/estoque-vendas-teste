<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venda extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cliente_id',
        'total',
        'status',
        'data_compra',
        'forma_pagamento',
        'endereco_entrega',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'cep'
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'data_compra' => 'datetime'
    ];


    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function itens()
    {
        return $this->hasMany(VendaItem::class);
    }


    public function scopePendente($query)
    {
        return $query->where('status', 'pendente');
    }


    public function getStatusFormatadoAttribute()
    {
        return [
            'pendente' => 'Pendente',
            'pago' => 'Pago',
            'cancelado' => 'Cancelado'
        ][$this->status] ?? $this->status;
    }

    public function getStatusClasseAttribute()
    {
        return [
            'pendente' => 'warning',
            'pago' => 'success',
            'cancelado' => 'danger'
        ][$this->status] ?? 'secondary';
    }


    public function mudarStatus($novoStatus)
    {
        if (!$this->validarTransicaoStatus($novoStatus)) {
            throw new \Exception("Transição de status inválida.");
        }

        if ($novoStatus === 'pago') {
            $this->validarParaPagamento();
        }

        $this->executarAcoesStatus($novoStatus);

        $this->status = $novoStatus;
        $this->save();
    }

    private function validarTransicaoStatus($novoStatus)
    {
        $permitidas = [
            'pendente' => ['pago', 'cancelado'],
            'pago' => [],
            'cancelado' => []
        ];

        return in_array($novoStatus, $permitidas[$this->status] ?? []);
    }

    private function validarParaPagamento()
{
    if (!$this->cliente->podeComprar()) {
        throw new \Exception("Cliente bloqueado não pode receber venda.");
    }

    foreach ($this->itens as $item) {


        if (!$item->produto) {
            throw new \Exception(
                "O item {$item->id} refere-se a um produto que foi removido. A venda não pode ser concluída."
            );
        }


        if ($item->produto->status !== 'ativo') {
            throw new \Exception(
                "O produto '{$item->produto->nome}' está inativo e não pode ser vendido."
            );
        }


        if ($item->produto->estoque < $item->quantidade) {
            throw new \Exception(
                "Estoque insuficiente para o produto '{$item->produto->nome}'."
            );
        }
    }
}

    private function executarAcoesStatus($novoStatus)
{

    if ($novoStatus === 'pago') {
        foreach ($this->itens as $item) {
            $produto = $item->produto;
            if (!$produto) {
                throw new \Exception("Produto do item {$item->id} não encontrado. A venda não pode ser finalizada.");
            }
            $produto->atualizarEstoque($item->quantidade, 'baixa');
        }
    }

    if ($novoStatus === 'cancelado' && $this->status === 'pago') {
        foreach ($this->itens as $item) {
            $produto = $item->produto;
            if (!$produto) {
                throw new \Exception("Produto do item {$item->id} não encontrado. Impossível restaurar estoque.");
            }
            $produto->atualizarEstoque($item->quantidade, 'restaurar');
        }
    }
}
}
