<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KitDigital;
use Illuminate\Http\Request;
use App\Models\Users\UserClient;
use Illuminate\Support\Facades\Hash;

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
    // Validación manual
    if (!$request->has('current_password') || !$request->has('new_password')) {
        return response()->json(['error' => 'Los campos current_password y new_password son obligatorios.'], 400);
    }

    if (strlen($request->new_password) < 6) {
        return response()->json(['error' => 'La nueva contraseña debe tener al menos 6 caracteres.'], 400);
    }

    // Buscar cliente
    $client = UserClient::find($id);
    if (!$client) {
        return response()->json(['error' => 'Cliente no encontrado.'], 404);
    }

    // Verificar contraseña actual
    if (!Hash::check($request->current_password, $client->password)) {
        return response()->json(['error' => 'La contraseña actual no es correcta.'], 400);
    }

    // Actualizar contraseña
    $client->password = bcrypt($request->new_password);
    $client->save();

    return response()->json(['message' => 'Contraseña actualizada correctamente.']);
}

    public function updateClientData(Request $request, $id)
{
    // Validación manual
    $data = $request->all();
    $errors = [];

    if (isset($data['name']) && (!is_string($data['name']) || strlen($data['name']) > 255)) {
        $errors['name'] = 'El campo name debe ser una cadena de texto y no puede exceder 255 caracteres.';
    }

    if (isset($data['email']) && (!filter_var($data['email'], FILTER_VALIDATE_EMAIL))) {
        $errors['email'] = 'El campo email debe ser un correo electrónico válido.';
    }

    if (!empty($errors)) {
        return response()->json(['errors' => $errors], 400);
    }

    // Buscar cliente
    $client = UserClient::find($id);
    if (!$client) {
        return response()->json(['error' => 'Cliente no encontrado.'], 404);
    }

    // Actualizar datos
    $client->fill($request->only(['name', 'email']));
    $client->save();

    return response()->json(['message' => 'Datos actualizados correctamente.', 'client' => $client]);
}

    public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    // Busca al usuario por email
    $user = UserClient::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['error' => 'Credenciales incorrectas.'], 401);
    }

    // Genera un token de acceso
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'Inicio de sesión exitoso.',
        'access_token' => $token,
        'token_type' => 'Bearer',
    ], 200);
}

    public function logout(Request $request)
{
    // Obtener el token del usuario autenticado
    $token = $request->user()->currentAccessToken();

    // Eliminar el token actual
    if ($token) {
        $token->delete();
        return response()->json(['message' => 'Sesión cerrada correctamente.'], 200);
    }

    return response()->json(['error' => 'Token inválido o no encontrado.'], 400);
}

    public function logoutAll(Request $request)
{
    // Obtener al usuario autenticado
    $user = $request->user();

    // Revocar todos los tokens del usuario
    $user->tokens()->delete();

    return response()->json(['message' => 'Sesión cerrada en todos los dispositivos.'], 200);
}

}
