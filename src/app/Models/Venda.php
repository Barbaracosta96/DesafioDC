<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int         $id
 * @property string      $numero_pedido
 * @property int|null    $cliente_id
 * @property int|null    $user_id
 * @property string      $status
 * @property string|null $forma_pagamento
 * @property float       $subtotal
 * @property float       $desconto
 * @property float       $total
 * @property string|null $observacoes
 * @property Carbon|null $data_venda
 * @property Carbon      $created_at
 * @property Carbon      $updated_at
 * @property Carbon|null $deleted_at
 * @property-read string $status_label
 * @property-read string $forma_pagamento_label
 * @property-read Cliente|null $cliente
 * @property-read \App\Models\User|null $vendedor
 * @property-read \App\Models\User|null $user
 */
class Venda extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vendas';

    protected $appends = ['status_label', 'forma_pagamento_label'];

    protected $fillable = [
        'numero_pedido',
        'cliente_id',
        'user_id',
        'status',
        'forma_pagamento',
        'subtotal',
        'desconto',
        'total',
        'observacoes',
        'data_venda',
    ];

    protected $casts = [
        'subtotal'    => 'decimal:2',
        'desconto'    => 'decimal:2',
        'total'       => 'decimal:2',
        'data_venda'  => 'datetime',
    ];

    public static function gerarNumeroPedido(): string
    {
        $ultimo = static::withTrashed()->max('id') ?? 0;
        return 'PED-' . str_pad($ultimo + 1, 6, '0', STR_PAD_LEFT);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function itens(): HasMany
    {
        return $this->hasMany(ItemVenda::class);
    }

    public function movimentacoes(): HasMany
    {
        return $this->hasMany(MovimentacaoEstoque::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pendente'     => 'Pendente',
            'processando'  => 'Processando',
            'concluido'    => 'Concluído',
            'cancelado'    => 'Cancelado',
            default        => $this->status,
        };
    }

    public function getFormaPagamentoLabelAttribute(): string
    {
        return match ($this->forma_pagamento) {
            'dinheiro'        => 'Dinheiro',
            'cartao_credito'  => 'Cartão de Crédito',
            'cartao_debito'   => 'Cartão de Débito',
            'pix'             => 'PIX',
            default           => $this->forma_pagamento ?? '-',
        };
    }
}
