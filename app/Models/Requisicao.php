<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Requisicao extends Model
{
    protected $table = 'requisicoes';

    protected $fillable = [
        'user_id',
        'status',
        'justificativa',
        'observacao_admin',
        'aprovado_por',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'aprovado_por' => 'integer',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function aprovador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aprovado_por');
    }

    public function itens(): HasMany
    {
        return $this->hasMany(ItemRequisicao::class);
    }
}
