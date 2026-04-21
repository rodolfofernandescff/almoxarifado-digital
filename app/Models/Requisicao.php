<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Requisicao extends Model
{
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Os itens que estão dentro desta requisição
    public function itens()
    {
        return $this->hasMany(ItemRequisicao::class);
    }
}
