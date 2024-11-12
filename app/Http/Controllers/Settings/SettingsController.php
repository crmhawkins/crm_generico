<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Company\CompanyDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class SettingsController extends Controller
{
    public function index()
    {
        $configuracion = CompanyDetails::first();
        return view('settings.index', compact('configuracion'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'price_hour' => 'required|numeric',
            'logo' => 'nullable|image',
            'company_name' => 'required|string|max:255',
            'nif' => 'required|string|max:50',
            'address' => 'required|string|max:255',
            'bank_account_data' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'certificado' => 'nullable|file',
            'contrasena' => 'nullable|string|min:6',
            'postCode' => 'nullable|string|max:255',
            'town' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
        ]);

        $data = $request->only([
            'price_hour', 'company_name', 'nif', 'address', 'bank_account_data', 'telephone', 'email','contrasena','postCode', 'town','province',
        ]);

        // Guardar logo
        if ($request->hasFile('logo')) {
            $photo = $request->file('logo');
            $path = public_path('assets/images/logo/logo.png');
            $manager = new ImageManager(new Driver());
            $image = $manager->read($photo);
            $image->toPng()->save($path);
            $data['logo'] = $path;
        }

        // Guardar certificado
        if ($request->hasFile('certificado')) {
            $certificado = $request->file('certificado');
            $certName = random_int(0, 99999) . '-cert.' . $certificado->getClientOriginalExtension();
            $path = $certificado->storeAs('assets', $certName, 'public');
            $data['certificado'] = $path;
        }

        CompanyDetails::create($data);

        return redirect()->route('configuracion.index')->with('success', 'Configuración creada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $configuracion = CompanyDetails::findOrFail($id);

        $request->validate([
            'price_hour' => 'required|numeric',
            'logo' => 'nullable|image',
            'company_name' => 'required|string|max:255',
            'nif' => 'required|string|max:50',
            'address' => 'required|string|max:255',
            'bank_account_data' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'certificado' => 'nullable|file',
            'contrasena' => 'nullable|string|min:6',
            'postCode' => 'nullable|string|max:255',
            'town' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
        ]);

        $data = $request->only([
            'price_hour', 'company_name', 'nif', 'address', 'bank_account_data', 'telephone', 'email', 'contrasena','postCode', 'town','province',
        ]);

         // Guardar logo
         if ($request->hasFile('logo')) {
            if ($configuracion->logo) {
                Storage::disk('public')->delete($configuracion->logo);
            }
            $photo = $request->file('logo');
            $path = public_path('assets/images/logo/logo.png');
            $manager = new ImageManager(new Driver());
            $image = $manager->read($photo);
            $image->toPng()->save($path);
            $data['logo'] = $path;
        }

        // Guardar certificado
        if ($request->hasFile('certificado')) {
            if ($configuracion->certificado) {
                Storage::disk('public')->delete($configuracion->certificado);
            }
            $certificado = $request->file('certificado');
            $certName = random_int(0, 99999) . '-cert.' . $certificado->getClientOriginalExtension();
            $path = $certificado->storeAs('assets', $certName, 'public');
            $data['certificado'] = $path;
        }

        $configuracion->update($data);

        return redirect()->route('configuracion.index')->with('success', 'Configuración actualizada correctamente.');
    }
}
