<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'clientes';

    protected $fillable = [
        'nome',
        'email',
        'telefone',
        'cpf_cnpj',
        'tipo',
        'cep',
        'logradouro',
        'numero',
        'bairro',
        'cidade',
        'estado',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function vendas(): HasMany
    {
        return $this->hasMany(Venda::class);
    }
}
