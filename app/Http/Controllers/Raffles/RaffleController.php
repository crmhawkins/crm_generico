<?php

namespace App\Http\Controllers\Raffles;

use App\Http\Controllers\Controller;
use App\Models\Raffles\Raffle;
use App\Models\Raffles\Ticket; // Asegúrate de importar el modelo Ticket
use Illuminate\Http\Request;

class RaffleController extends Controller
{
    // Método para listar los sorteos
    public function index()
    {
        $raffles = Raffle::all();
        return view('raffles.index', compact('raffles'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        return view('raffles.create');
    }

    // Guardar un nuevo sorteo
    public function store(Request $request)
    {
        $request->validate([
            'year' => 'required|integer',
            'raffle_name' => 'required|string|max:255',
            'ticket_digits' => 'required|integer',
            'end_date' => 'required|date',
        ]);

        Raffle::create($request->all());
        return redirect()->route('raffles.index')->with('success', 'Sorteo creado correctamente.');
    }

    // Ver un sorteo y sus boletos
    public function show(Raffle $raffle)
    {
        $tickets = $raffle->tickets; // Asegúrate de tener la relación 'tickets' en el modelo Raffle
        return view('raffles.show', compact('raffle', 'tickets'));
    }

    // Mostrar formulario de edición
    public function edit(Raffle $raffle)
    {
        return view('raffles.edit', compact('raffle'));
    }

    // Actualizar un sorteo
    public function update(Request $request, Raffle $raffle)
    {
        $request->validate([
            'year' => 'required|integer',
            'raffle_name' => 'required|string|max:255',
            'ticket_digits' => 'required|integer',
            'end_date' => 'required|date',
        ]);

        $raffle->update($request->all());
        return redirect()->route('raffles.index')->with('success', 'Sorteo actualizado correctamente.');
    }

    // Eliminar un sorteo
    public function destroy(Raffle $raffle)
    {
        $raffle->delete();
        return redirect()->route('raffles.index')->with('success', 'Sorteo eliminado correctamente.');
    }

    // Asignar un ganador manualmente
    public function addWinner(Request $request, Raffle $raffle)
    {
        $request->validate([
            'winner_ticket_id' => 'required|exists:tickets,id',
        ]);

        $raffle->winner_ticket_id = $request->winner_ticket_id;
        $raffle->save();

        return redirect()->route('raffles.index')->with('success', 'Ganador asignado correctamente.');
    }

    public function assignWinner(Request $request, Raffle $raffle)
{
    // Validar que se haya ingresado el número de ticket
    $request->validate([
        'ticket_number' => 'required|integer',
    ]);

    // Buscar el ticket en la base de datos
    $winnerTicket = Ticket::where('raffle_id', $raffle->id)
                          ->where('ticket_number', $request->ticket_number)
                          ->first();

    // Verificar si el ticket existe
    if ($winnerTicket) {
        // Si el ticket existe, asignarlo como ganador
        $raffle->winner_ticket_id = $winnerTicket->id;
        $raffle->save();

        return redirect()->route('raffles.index')->with('success', 'Ganador asignado correctamente.');
    } else {
        // Si el ticket no existe, mostrar mensaje de error
        return redirect()->route('raffles.index')->with('error', 'Número de ticket no válido o no vendido.');
    }
}


}




