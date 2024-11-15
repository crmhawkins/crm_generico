<?php

namespace App\Http\Controllers\Raffles;

use App\Http\Controllers\Controller;
use App\Models\Raffles\Raffle;
use App\Models\Raffles\Ticket;
use Illuminate\Http\Request;



class RaffleController extends Controller
{
    public function index()
{
    // Obtener solo los sorteos que están activos (status = 0)
    $raffles = Raffle::where('status', 0)->get();
    return view('raffles.index', compact('raffles'));
}


    public function create()
    {
        return view('raffles.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'year' => 'required|integer',
        'raffle_name' => 'required|string|max:255',
        'ticket_digits' => 'required|integer',
        'start_date' => 'nullable|date',
        'end_date' => 'required|date|after_or_equal:start_date',
    ], [
        'end_date.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
    ]);

    Raffle::create($request->all());
    return redirect()->route('raffles.index')->with('success', 'Sorteo creado correctamente.');
}

    public function show(Raffle $raffle)
    {
        return view('raffles.show', compact('raffle'));
    }

    public function edit(Raffle $raffle)
    {
        return view('raffles.edit', compact('raffle'));
    }

    public function update(Request $request, Raffle $raffle)
{
    $request->validate([
        'year' => 'required|integer',
        'raffle_name' => 'required|string|max:255',
        'ticket_digits' => 'required|integer',
        'start_date' => 'nullable|date',
        'end_date' => 'required|date|after_or_equal:start_date',
    ]);

    $raffle->update($request->all());
    return redirect()->route('raffles.index')->with('success', 'Sorteo actualizado correctamente.');
}

    public function destroy(Raffle $raffle)
    {
        $raffle->delete();
        return redirect()->route('raffles.index')->with('success', 'Sorteo eliminado correctamente.');
    }

    public function assignWinner(Request $request, Raffle $raffle)
    {
        $request->validate([
            'ticket_number' => 'required|integer',
        ]);

        $winnerTicket = Ticket::where('raffle_id', $raffle->id)
            ->where('ticket_number', $request->ticket_number)
            ->first();

        if ($winnerTicket) {
            $raffle->winner_ticket_id = $winnerTicket->id;
            $raffle->save();

            return redirect()->route('raffles.index')->with('success', 'Ganador asignado correctamente.');
        } else {
            return redirect()->route('raffles.index')->with('error', 'Número de ticket no válido o no vendido.');
        }
    }

    public function showWinner(Raffle $raffle)
{
    $ticket = $raffle->winner_ticket_id ? Ticket::find($raffle->winner_ticket_id) : null;
    
    if (!$ticket) {
        return redirect()->route('raffles.index')->with('error', 'No se encontró un ganador para este sorteo.');
    }

    $client = $ticket->client; // Esto debería obtener al cliente asociado al ticket

    return view('raffles.winner', compact('raffle', 'ticket', 'client'));
}


    

    
    public function showTickets(Raffle $raffle)
{
    $ticketsSold = $raffle->tickets()->count();
    $totalTickets = pow(10, $raffle->ticket_digits);
    $tickets = $raffle->tickets;

    return view('raffles.show_tickets', compact('raffle', 'tickets', 'ticketsSold', 'totalTickets'));
}

    
public function updateRaffle(Request $request, Raffle $raffle)
{
    $request->validate([
        'raffle_name' => 'required|string|max:255',
        'year' => 'required|integer',
        'ticket_digits' => 'required|integer',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
    ], [
        'end_date.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',
    ]);

    $raffle->update($request->all());

    return redirect()->route('raffles.index')->with('success', 'Sorteo actualizado correctamente.');
}


public function finalize(Raffle $raffle)
{
    // Cambiar el estado de 'activo' (0) a 'finalizado' (1)
    $raffle->status = 1;  // 1 significa finalizado
    $raffle->save();

    return redirect()->route('raffles.index')->with('success', 'Sorteo finalizado correctamente.');
}


public function showCompleted()
{
    // Obtener todos los sorteos finalizados (status = 1)
    $completedRaffles = Raffle::where('status', 1)->get();
    return view('raffles.completed_raffles', compact('completedRaffles'));
}

public function assignRandomTickets(Request $request)
{
    $request->validate([
        'raffle_id' => 'required|exists:raffles,id',
        'client_id' => 'required|exists:clients,id',
        'quantity' => 'required|integer|min:1',
    ]);

    $raffleId = $request->input('raffle_id');
    $clientId = $request->input('client_id');
    $quantity = $request->input('quantity');

    $raffle = Raffle::findOrFail($raffleId);

    // Obtener todos los números de tickets posibles para el sorteo
    $totalTickets = pow(10, $raffle->ticket_digits) - 1;
    $availableTickets = range(0, $totalTickets);

    // Filtrar los tickets que ya están asignados en el sorteo actual
    $assignedTickets = Ticket::where('raffle_id', $raffleId)->pluck('ticket_number')->toArray();
    $availableTickets = array_diff($availableTickets, $assignedTickets);

    // Asegurar que haya suficientes tickets disponibles
    if (count($availableTickets) < $quantity) {
        return response()->json(['error' => 'No hay suficientes tickets disponibles para asignar.'], 400);
    }

    // Seleccionar tickets aleatorios únicos
    $selectedTickets = array_rand(array_flip($availableTickets), $quantity);

    // Crear los tickets y asignarlos al cliente
    $assignedTicketNumbers = [];
    foreach ($selectedTickets as $ticketNumber) {
        $ticket = Ticket::create([
            'raffle_id' => $raffleId,
            'client_id' => $clientId,
            'ticket_number' => $ticketNumber,
        ]);
        $assignedTicketNumbers[] = $ticketNumber;
    }

    return response()->json([
        'raffle_name' => $raffle->raffle_name,
        'year' => $raffle->year,
        'assigned_tickets' => $assignedTicketNumbers,
    ], 200);
}






}





