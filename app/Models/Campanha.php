<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campanha extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nome',
        'horario',
        'contatos',
        'origem_contatos',
        'ignorar_contatos',
        'mensagem', // Novo campo
        'arquivo',  // Novo campo
        'data',
        'enviar_diariamente',
    ];

    protected $casts = [
        'contatos' => 'array',
        'ignorar_contatos' => 'boolean',
    ];
}
