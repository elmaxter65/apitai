<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pantalla extends Model
{
    use HasFactory;

    protected $guarded = [];  

    public static function saveImage($jsonParse, $i){
        // guardar imagen en el servidor
        if ($jsonParse->img) {
            $data = $jsonParse->img;
            $extension = explode('/', mime_content_type($data))[1];

            list($type, $data) = explode(';', $data);
            list(, $data)      = explode(',', $data);
            $data = base64_decode($data);
            $imageName = time().$i.'.'.$extension;

            // validar si no existe la carpeta la creamos
            $path = public_path().'/uploads/options';
            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }
            
            file_put_contents($path.'/'.$imageName, $data);

            // agregar al array de opciones ya procesado
            return array(
                'descripcion' => $jsonParse->descripcion,
                'img' => asset('').'uploads/options/'.$imageName
            );
        }

        return null;
    }
}
