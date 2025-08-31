<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanoRenovacao extends Model
{
    use HasFactory;

    // Define a tabela associada ao modelo
    protected $table = 'planos_renovacao';

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'nome',
        'descricao',
        'preco',
        'detalhes', // Adicionado para refletir a nova coluna
        'botao',    // Adicionado para refletir a nova coluna
        'limite',   // Adicionado para refletir a nova coluna
        'creditos', // Adicionado para refletir a nova coluna
        'duracao',  // Adicionado para refletir a nova coluna
    ];

    public function users()
{
    return $this->hasMany(User::class, 'plano_id');
}
}
