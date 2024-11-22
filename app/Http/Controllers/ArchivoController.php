<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Archivo;
use Aws\S3\S3Client;

class ArchivoController extends Controller
{
    public function upload(Request $req)
    {
        $result = [];

        if ($req->hasFile('files')) {
            foreach ($req->file('files') as $file) {
                $filePath = $file->store('Archivos', 's3');

                $url = env('AWS_URL') . '/' . $filePath;

                $archivo = new Archivo();
                $archivo->ruta = $filePath;
                $archivo->nombre_original = $file->getClientOriginalName();
                $archivo->save();

                $result[] = [
                    'id' => $archivo->id,
                    'ruta' => $url, 
                    'nombre_original' => $archivo->nombre_original,
                ];
            }
        }

        return ['result' => $result];
    }

    public function download(Request $req, $id)
    {
        // Encuentra el archivo en la base de datos
        $archivo = Archivo::findOrFail($id);

        // Construye la URL pública
        $url = env('AWS_URL') . '/' . $archivo->ruta;

        // Devuelve la URL al cliente
        return response()->json(['download_url' => $url]);
    }
    
}