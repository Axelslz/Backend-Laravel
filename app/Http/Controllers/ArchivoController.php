<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Archivo;

class ArchivoController extends Controller
{
    public function upload(Request $req)
    {
        $result = [];
   
        if ($req->hasFile('files')) {
            foreach ($req->file('files') as $file) {
                $filePath = $file->store('Archivos');

                $archivo = new Archivo();
                $archivo->ruta = $filePath;
                $archivo->nombre_original = $file->getClientOriginalName();
                $archivo->save();

                $result[] = [
                    'id' => $archivo->id,
                    'ruta' => $filePath,
                    'nombre_original' => $archivo->nombre_original,
                ];
            }
        }

        return ['result' => $result];
    }

    public function download(Request $req, $id)
    {
        $archivo = Archivo::findOrFail($id);
        $filePath = storage_path('app/' . $archivo->ruta);

        return response()->download($filePath);
    }
}