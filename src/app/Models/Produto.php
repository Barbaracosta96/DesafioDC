<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'produtos';

    protected $appends = ['estoque_baixo'];

    protected $fillable = [
        'categoria_id',
        'nome',
        'codigo_sku',
        'descricao',
        'preco_custo',
        'preco_venda',
        'quantidade_estoque',
        'estoque_minimo',
        'unidade',
        'imagem',
        'ativo',
    ];

    protected $casts = [
        'preco_custo'        => 'decimal:2',
        'preco_venda'        => 'decimal:2',
        'quantidade_estoque' => 'integer',
        'estoque_minimo'     => 'integer',
        'ativo'              => 'boolean',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function itensVenda(): HasMany
    {
        return $this->hasMany(ItemVenda::class);
    }

    public function movimentacoes(): HasMany
    {
        return $this->hasMany(MovimentacaoEstoque::class);
    }

    public function getEstoqueBaixoAttribute(): bool
    {
        return $this->quantidade_estoque <= $this->estoque_minimo;
    }
}
