<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produto extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nome',
        'preco',
        'estoque',
        'status',
    ];

    protected $casts = [
        'preco' => 'decimal:2',
        'estoque' => 'integer'
    ];

    public function vendaItens()
    {
        return $this->hasMany(VendaItem::class);
    }

    public function scopeAtivo($query)
    {
        return $query->where('status', 'ativo');
    }

    public function scopeComEstoque($query)
    {
        return $query->where('estoque', '>', 0);
    }

    public function getStatusFormatadoAttribute()
    {
        return $this->status == 'ativo' ? 'Ativo' : 'Inativo';
    }

    public function getStatusClasseAttribute()
    {
        return $this->status == 'ativo' ? 'success' : 'danger';
    }

    /**
     *
     */
    public function podeVender($quantidade)
    {
        return $this->status === 'ativo'
            && $this->estoque >= $quantidade
            && $quantidade > 0;
    }

    /**
     *
     */
    public function atualizarEstoque(int $quantidade, string $operacao = 'baixa')
    {
        $operacao = strtolower($operacao);

        if ($quantidade <= 0) {
            throw new \Exception("Quantidade inválida para movimentação de estoque.");
        }

        if ($operacao === 'baixa') {

            if ($this->estoque < $quantidade) {
                throw new \Exception("Estoque insuficiente para o produto {$this->nome}.");
            }

            $this->estoque -= $quantidade;

        } elseif ($operacao === 'restaurar') {

            $this->estoque += $quantidade;

        } else {
            throw new \Exception("Operação de estoque inválida: {$operacao}");
        }

        $this->save();
    }
}
