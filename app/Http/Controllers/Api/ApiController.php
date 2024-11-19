<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KitDigital;
use Illuminate\Http\Request;
use App\Models\Users\UserClient;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    public function getayudas(Request $request){

        $kitDigitals = KitDigital::where('estado', 18 )->where(function($query) {
            $query->where('enviado', '!=', 1)
                  ->orWhereNull('enviado');
        })->get();
        // $kitDigitals = KitDigital::where(function($query) {
        //     $query->where('enviado', '!=', 1)
        //           ->orWhereNull('enviado');
        // })->get();

        return $kitDigitals;

    }
    public function updateAyudas($id){
        $kitDigital = KitDigital::find($id);
        $kitDigital->enviado = 1;
        $kitDigital->save();

        return response()->json(['success' => $id]);
    }

    public function updateMensajes(Request $request)
    {
       // Storage::disk('local')->put('Respuesta_Peticion_ChatGPT-Model.txt', $request->all() );
            $ayuda = KitDigital::find($request->ayuda_id);

            $ayuda->mensaje = $request->mensaje;
            $ayuda->mensaje_interpretado = $request->mensaje_interpretado;
            $actualizado = $ayuda->save();

        if($actualizado){
            return response()->json([
                'success' => true,
                'ayudas' => 'Actualizado con exito',
                'result'=> $ayuda
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'ayudas' => 'Error al Actualizar.'
            ], 200);
        }

    }

    public function crearCliente(Request $request)
    {
        $data = $request->all();

        if (!isset($data['name']) && $data['name'] == '') return response()->json('El campo NAME es obligatorio y no puede estar vacio', 400);
        if (!isset($data['email']) && $data['email'] == '') return response()->json('El campo EMAIL es obligatorio y no puede estar vacio', 400);
        if (!isset($data['password']) && $data['password'] == '') return response()->json('El campo PASSWORD es obligatorio y no puede estar vacio', 400);
        if (!isset($data['password_confirmation']) && $data['password_confirmation'] == '') return response()->json('El campo PASSWORD CONFIRMATION es obligatorio y no puede estar vacio', 400);
        if ($data['password'] !=  $data['password_confirmation']) return response()->json('El password no es igual a su confirmacion.', 400);

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        $crearUsuarioCliente = UserClient::create($data);
        
        if ($crearUsuarioCliente) return response()->json('El cliente se ha creado correctamente.', 201);
        
        return response()->json('Error en el servidor, contacta con el administrador.', status: 500);

    }

    public function updatePassword(Request $request, $id)
{
    $request->validate([
        'current_password' => 'required',
        'new_password' => 'required|min:6|confirmed',
    ]);

    $client = UserClient::find($id);

    if (!$client) {
        return response()->json(['error' => 'Cliente no encontrado.'], 404);
    }

    if (!Hash::check($request->current_password, $client->password)) {
        return response()->json(['error' => 'La contraseña actual no es correcta.'], 400);
    }

    $client->password = bcrypt($request->new_password);
    $client->save();

    return response()->json(['message' => 'Contraseña actualizada correctamente.']);
}

    public function updateClientData(Request $request, $id)
{
    $request->validate([
        'name' => 'nullable|string|max:255',
        'email' => 'nullable|email|unique:users_clients,email,' . $id,
        'phone' => 'nullable|string|max:15',
    ]);

    $client = UserClient::find($id);

    if (!$client) {
        return response()->json(['error' => 'Cliente no encontrado.'], 404);
    }

    $client->fill($request->only(['name', 'email', 'phone']));
    $client->save();

    return response()->json(['message' => 'Datos actualizados correctamente.', 'client' => $client]);
}

    public function resetPassword(Request $request)
{
    $request->validate([
        'token' => 'required',
        'new_password' => 'required|min:6|confirmed',
    ]);

    $reset = DB::table('password_resets')->where('token', $request->token)->first();

    if (!$reset) {
        return response()->json(['error' => 'Token inválido o expirado.'], 400);
    }

    $client = UserClient::where('email', $reset->email)->first();

    if (!$client) {
        return response()->json(['error' => 'Cliente no encontrado.'], 404);
    }

    $client->password = bcrypt($request->new_password);
    $client->save();

    // Eliminar el token usado
    DB::table('password_resets')->where('token', $request->token)->delete();

    return response()->json(['message' => 'Contraseña restablecida correctamente.']);
}

}
