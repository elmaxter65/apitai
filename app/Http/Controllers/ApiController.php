<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pantalla;

class ApiController extends Controller
{
    public function getPantalla($id){
        $consulta = Pantalla::find($id);
        return Response($consulta);
    }
}
