<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemRequisicao extends Model
{
    protected $table = 'item_requisicao';

    protected $fillable = [
        'requisicao_id',
        'produto_id',
        'quantidade_pedida',
        'quantidade_entregue',
    ];

    protected $casts = [
        'requisicao_id' => 'integer',
        'produto_id' => 'integer',
        'quantidade_pedida' => 'integer',
        'quantidade_entregue' => 'integer',
    ];

    public function requisicao(): BelongsTo
    {
        return $this->belongsTo(Requisicao::class);
    }

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class);
    }
}
