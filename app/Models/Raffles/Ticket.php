<?php
namespace App\Models\Raffles;

use App\Models\Users\UserClient;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'tickets';

    protected $fillable = [
        'raffle_id',
        'client_id',
        'ticket_number',
    ];

    // Relación con el sorteo
    public function raffle()
    {
        return $this->belongsTo(Raffle::class, 'raffle_id');
    }

    // Relación con el cliente
    public function client()
    {
        return $this->belongsTo(UserClient::class, 'client_id');
    }
}

