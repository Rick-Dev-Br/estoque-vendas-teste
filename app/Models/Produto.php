<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use ILLuminate\Support\Str;

class Produto extends Model
{
    use HasFactory, SoftDeletes;

    public const UNIDADES = [
        'kg' => 'kg',
        'g' => 'g',
        'unidade' => 'unidade',
        'saco' => 'saco',
        'canjunto' => 'conjunto',
    ];

    protected $fillable = [
        'codigo',
        'nome',
        'preco',
        'estoque',
        'status',
        'unidade_medida',
        'unidade_quantidade',
    ];

    protected $casts = [
        'preco' => 'decimal:2',
        'estoque' => 'integer',
        'unidade_quantidade' => 'decimal:3',
    ];

    protected static function booted()
    {
        static::creating(function (self $produto) {
            if (!$produto->public_id) {
                $produto->public_id = (string) Str::ulid();
            }
        });
    }

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

    public function getUnidadeDescricaoAttribute()
    {
        $quantidade = $this->unidade_quantidade ?? 1;
        $quantidadeFormatada = rtrim(rtrim(number_format($quantidade, 3, '.'), ''), '.');
        $unidade = self::UNIDADES[$this->unidade_media] ?? 'unidade';

        if ($this->unidade_medida === 'conjunto') {
            return "Conjunto de {$quantidadeFormatada} itens";
        }

        if ($this->unidade_medida === 'unidade' && $quantidade > 1) {
            return "{$quantidadeFormatada} unidades";
        }
        return "{$quantidadeFormatada} {$unidade}";
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
     * att status
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
