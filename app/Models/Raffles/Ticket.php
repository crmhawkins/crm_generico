<?php
namespace App\Models\Raffles;

use App\Models\Raffles\Raffle;
use App\Models\Clients\Client;
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
        return $this->belongsTo(Raffle::class);
    }

    // Relación con el cliente
    public function client()
{
    return $this->belongsTo(Client::class, 'client_id');
}
}
