<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Produto extends Model
{
    use LogsActivity;
    protected $fillable = [
        'categoria_id',
        'nome',
        'descricao',
        'estoque_atual',
        'estoque_minimo',
        'unidade_medida',
    ];

    protected $casts = [
        'categoria_id' => 'integer',
        'estoque_atual' => 'integer',
        'estoque_minimo' => 'integer',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function itensRequisicao(): HasMany
    {
        return $this->hasMany(ItemRequisicao::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Produto {$eventName}");
    }
}
