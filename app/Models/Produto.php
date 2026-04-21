<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
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
}
