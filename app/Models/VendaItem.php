<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendaItem extends Model
{
    use HasFactory;

    protected $table = 'venda_itens';

    protected $fillable = [
        'venda_id',
        'produto_id',
        'quantidade',
        'preco_unitario'
    ];

    protected $casts = [
        'preco_unitario' => 'decimal:2',
        'quantidade' => 'integer'
    ];


    public function venda()
    {
        return $this->belongsTo(Venda::class);
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }

    public function getSubtotalAttribute()
    {
        return $this->quantidade * $this->preco_unitario;
    }

    public function getSubtotalFormatadoAttribute()
    {
        return 'R$' . number_format($this->subtotal, 2, ',', '.');
    }

    public function getPrecoFormatadoAttribute()
    {
        return 'R$' . number_format($this->preco_unitario, 2, ',', '.' );
    }
}
