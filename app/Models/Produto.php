<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Produto extends Model
{
    use HasFactory, SoftDeletes;

    public const TIPOS_UNIDADE = [
        'unidade' => 'Unidade',
        'saco' => 'Saco',
        'caixa' => 'Caixa',
        'pacote' => 'Pacote',
    ];

    public const UNIDADES_MEDIDA = [
        'un' => 'un',
        'kg' => 'kg',
        'l' => 'l',
        'm' => 'm',
    ];

    protected $fillable = [
        'codigo',
        'nome',
        'preco',
        'estoque',
        'status',
        'tipo_unidade',
        'unidade_medida',
        'unidade_quantidade',
    ];

    protected $casts = [
        'preco' => 'decimal:2',
        'estoque' => 'integer',
        'unidade_quantidade' => 'decimal:2',
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

    public function scopeEstoqueBaixo($query)
    {
        return $query->whereColumn('estoque', '<=', 'estoque_minimo')
            ->where('status', 'ativo');
    }

    public function getStatusFormatadoAttribute()
    {
        return $this->status == 'ativo' ? 'Ativo' : 'Inativo';
    }

    public function getStatusClasseAttribute()
    {
        return $this->status == 'ativo' ? 'success' : 'danger';
    }

    public function getUnidadeFormatadaAttribute()
    {
        $tipoUnidade = $this->tipo_unidade ?? 'unidade';
        $medida = $this->unidade_medida ?? 'un';
        $quantidade = $this->unidade_quantidade ?? 1;
        $quantidadeFormatada = $this->formatarQuantidade($quantidade);
        $tipoFormatado = self::TIPOS_UNIDADE[$tipoUnidade] ?? ucfirst($tipoUnidade);

        return "{$tipoFormatado} ({$quantidadeFormatada} {$medida})";
    }

        public function getEstoqueConvertidoAttribute(): ?string
    {
        $medida = $this->unidade_medida ?? 'un';
        $quantidade = $this->unidade_quantidade ?? 1;

        if ($medida === 'un' && $quantidade == 1) {
            return null;
        }

        $total = ($this->estoque ?? 0) * $quantidade;
        $totalFormatado = $this->formatarQuantidade($total);

        return "{$totalFormatado} {$medida}";
    }

    public function getUnidadeDescricaoAttribute()
    {
        return $this->unidade_formatada;
    }

    protected function formatarQuantidade($valor): string
    {
        $formatado = number_format((float) $valor, 2, '.', '');

        return rtrim(rtrim($formatado, '0'), '.');
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
