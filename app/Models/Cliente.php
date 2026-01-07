<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nome',
        'email',
        'status'
    ];

    public function vendas()
    {
        return $this->hasMany(Venda::class);
    }

    public function scopeAtivo($query)
    {
        return $query->where('status', 'ativo');
    }

    public function getStatusFormatadoAttribute()
    {
        return $this->status == 'ativo' ? 'Ativo' : 'Bloqueado';
    }

    public function getStatusClasseAttribute()
    {
        return $this->status == 'ativo' ? 'success' : 'danger';
    }

    public function podeComprar()
    {
        return $this->status == 'ativo';
    }

    public function ultimasVendas($limite = 5)
    {
        return $this->vendas()->latest()->limit($limite);
    }
}
