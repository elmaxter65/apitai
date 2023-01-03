<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Pantalla};
use Illuminate\Support\Facades\File;

class PantallasController extends Controller
{
    public function index(){
        $consulta = Pantalla::all();
        return view('pantallas.index', compact('consulta'));
    }

    public function create(){
        return view('pantallas.create');
    }

    public function edit($id){
        return view('pantallas.edit', compact('id'));
    }

    public function store(Request $request){
        // validar si el titulo no existe
        $verificar = Pantalla::whereTitulo($request->titulo)->first();

        if($verificar){
            return redirect(asset('pantallas'))->with('error', 'Registro duplicado! ya existe una pantalla con el mismo titulo.');
        }

        if ($request->hasFile('imagen')) {
            $binarioImagen = base64_encode(file_get_contents($request->file('imagen')));
            $extension = $request->file('imagen')->extension();
            $imagen = 'data:image/' . $extension . ';base64,' . $binarioImagen;
        } else {
            $imagen = null;
        }

        // recorrer opciones en busca de imagenes cuando la distribucion es horizontal
        if ($request->tipo == 'Desliza a la izquierda o derecha' || $request->distribucion == 'Horizontal') {
            $distribucion = 'Horizontal';
            $opciones = array();
            $i = 0;
            foreach ($request->opciones as $key => $value) {
                $jsonParse = json_decode($value);

                // guardar imagen en el servidor
                if ($jsonParse->img) {
                    $i++;
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
                    array_push($opciones, array(
                        'descripcion' => $jsonParse->descripcion,
                        'img' => asset('').'uploads/options/'.$imageName
                    ));
                }
            }
            $opciones = json_encode($opciones);
        } else {
            $distribucion = 'Vertical';
            $opciones = json_encode($request->opciones);
        }

        // crear pantalla
        $insertar = Pantalla::create([
            'pregunta' => $request->pregunta,
            'titulo' => $request->titulo,
            'tipo' => $request->tipo,
            'distribucion' => $distribucion,
            'imagen' => $imagen,
            'opciones' => $opciones
        ]);

        return redirect(asset('pantallas'))->with('success', 'Se ha registrado una nueva pantalla exitosamente.');
    }

    public function update(Request $request){
        // validar si el titulo no existe
        $verificar = Pantalla::whereTitulo($request->titulo)->where('id', '!=', $request->id)->first();

        if($verificar){
            return redirect(asset('pantallas'))->with('error', 'Registro duplicado! ya existe una pantalla con el mismo titulo.');
        }

        // traer registro actual de la base de datos
        $update = Pantalla::find($request->id);
        
        // validar si esta cambiando la imagen
        if ($request->hasFile('imagen')) {
            $binarioImagen = base64_encode(file_get_contents($request->file('imagen')));
            $extension = $request->file('imagen')->extension();
            $imagen = 'data:image/' . $extension . ';base64,' . $binarioImagen;
            // actualizar registro
            $update->imagen = $imagen;
        } 

        // verificar si el registro antiguo era con imagenes horizontales y el nuevo sigue siendo horizontal
        // ya que tenemos que hacer un poco de cosas para trabajar con las imagenes viejas y nuevas
        if ($update->distribucion == 'Horizontal' && $request->distribucion == 'Horizontal') {
            # esta condicion es porque si el registro viejo y el nuevo son horizontales ambos
            // guardar imagenes en un array para validar despues con los nuevos
            $convertOld = json_decode($update->opciones);
            $viejos = array();
            foreach ($convertOld as $key => $value) {
                array_push($viejos, $value->descripcion);
            }
        
            // recorrer array nuevo
            $requestAll = array();
            $opciones = array();
            $i = 0;
            foreach ($request->opciones as $key => $value) {
                $jsonParse = json_decode($value);

                // agregar al array de request descripcion
                array_push($requestAll, $jsonParse->descripcion);

                // validar si ya existe
                if (in_array($jsonParse->descripcion, $viejos)) {
                    // si existe solo lo agregamos tal cual en el array de opciones
                    array_push($opciones, $jsonParse);
                } else {
                    // guardar imagen en el servidor
                    $i++;
                    array_push($opciones, Pantalla::saveImage($jsonParse, $i));
                }

            }

            // obtener los que fueron eliminados para borrar la imagen del servidor
            foreach ($convertOld as $key => $value) {
                if (!in_array($value->descripcion, $requestAll)) {
                    // parsear la url para obtener el path sin el dominio
                    $parseUrl = parse_url($value->img);
                    File::delete(public_path($parseUrl['path']));
                }
            }
            // guardar opciones actuales
            $update->opciones = json_encode($opciones);

        } elseif($update->distribucion == 'Horizontal' && $request->distribucion == 'Vertical'){
            // aqui debemos eliminar las imagenes que antes era horizontal
            $eliminar = json_decode($update->opciones);
            foreach ($eliminar as $key => $value) {
                // parsear la url para obtener el path sin el dominio
                $parseUrl = parse_url($value->img);
                File::delete(public_path($parseUrl['path']));
            }
            $update->opciones = json_encode($request->opciones);

        } elseif($request->distribucion == 'Horizontal') {
            // si llegamos aqui es porque solo tenemos que guardar las imagenes horizontales de las opciones
            $opciones = array();
            $i = 0;
            foreach ($request->opciones as $key => $value) {
                $jsonParse = json_decode($value);
                // guardar imagen en el servidor
                $i++;
                array_push($opciones, Pantalla::saveImage($jsonParse, $i));
            }
            $update->opciones = json_encode($opciones);

        } else {
            // si llegamos hasta aca es que sigue siendo una configuracion vertical que no requiere imagenes
            $update->opciones = json_encode($request->opciones);
        }

        // guardar cambios
        $update->pregunta = $request->pregunta;
        $update->titulo = $request->titulo;
        $update->tipo = $request->tipo;
        $update->distribucion = $request->distribucion;
        $update->save();

        return redirect(asset('pantallas'))->with('success', 'Se ha actualizado una pantalla exitosamente.');
    }

    public function destroy($id){
        $consulta = Pantalla::find($id);
        // si la distribucion es horizontal primero debemos eliminar las imagenes antes de eliminar el registro 
        if ($consulta->distribucion == 'Horizontal') {
            $opciones = json_decode($consulta->opciones);
            foreach ($opciones as $key => $value) {
                // parsear la url para obtener el path sin el dominio
                $parseUrl = parse_url($value->img);
                File::delete(public_path($parseUrl['path']));
            }
        }

        // eliminar registro de la base de datos
        $consulta->delete();

        return redirect(asset('pantallas'))->with('success', 'Se ha eliminado una pantalla exitosamente.');
    }
}
