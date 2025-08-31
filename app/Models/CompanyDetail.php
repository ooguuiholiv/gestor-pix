<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyDetail extends Model
{
    use HasFactory;

    protected $table = 'company_details';

    protected $fillable = [
        'user_id',
        'company_name',
        'company_whatsapp',
        'access_token',
        'company_logo',
        'pix_manual',
        'referral_balance',
        'api_session',
        'public_key', // Adicionado
        'site_id',    // Adicionado
        'evolution_api_url', // Adicionado
        'evolution_api_key', // Adicionado
        'not_gateway', // Adicionado
        'notification_url', // Adicionado
        'favicon', // Adicionado

    ];

    // Define o relacionamento com o modelo User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
