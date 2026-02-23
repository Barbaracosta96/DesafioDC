<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimentacaoEstoque extends Model
{
    protected $table = 'movimentacoes_estoque';

    protected $appends = ['tipo_label'];

    protected $fillable = [
        'produto_id',
        'user_id',
        'venda_id',
        'tipo',
        'quantidade',
        'quantidade_anterior',
        'quantidade_posterior',
        'motivo',
    ];

    protected $casts = [
        'quantidade'           => 'integer',
        'quantidade_anterior'  => 'integer',
        'quantidade_posterior' => 'integer',
    ];

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function venda(): BelongsTo
    {
        return $this->belongsTo(Venda::class);
    }

    public function getTipoLabelAttribute(): string
    {
        return match ($this->tipo) {
            'entrada' => 'Entrada',
            'saida'   => 'Saída',
            'ajuste'  => 'Ajuste',
            'estorno' => 'Estorno',
            default   => ucfirst($this->tipo),
        };
    }
}
