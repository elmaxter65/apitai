@extends('layouts.dashboard')

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-start mb-0">
                    M&oacute;dulo Pantallas
                </h2>
                <div class="breadcrumb-wrapper">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{asset('home')}}">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{asset('pantallas')}}">Pantallas</a>
                        </li>
                        <li class="breadcrumb-item active">
                            Editar
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<form action="{{asset('pantallas/update')}}" method="post" enctype="multipart/form-data" class="row">
    @csrf

    <input type="hidden" id="id" name="id" value="{{$id}}">
    <div class="col-md-6 col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Informaci&oacute;n principal de la pantalla</h4>
            </div>
            <div class="card-body">
                <div class="form form-horizontal">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="pregunta">Pregunta</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" id="pregunta" class="form-control" name="pregunta" placeholder="Pregunta" autofocus required>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="titulo">T&iacute;tulo</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="text" id="titulo" class="form-control" name="titulo" placeholder="T&iacute;tulo" required>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="tipo">Tipo</label>
                                </div>
                                <div class="col-sm-9">
                                    <select class="form-control" name="tipo" id="tipo" onchange="changeTypeQuest(this.value)" required>
                                        <option value="">Seleccionar el tipo</option>
                                        <option value="Selecciona una de las opciones">Selecciona una de las opciones</option>
                                        <option value="Ordena según tus prioridades">Ordena según tus prioridades</option>
                                        <option value="Desliza a la izquierda o derecha">Desliza a la izquierda o derecha</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-12" id="distribucionInput" style="display:none">
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="distribucion">Distribuci&oacute;n</label>
                                </div>
                                <div class="col-sm-9">
                                    <select class="form-control" onchange="changeDistribution(this.value)" name="distribucion" id="distribucion">
                                        <option value="Vertical">Vertical</option>
                                        <option value="Horizontal">Horizontal</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-12" id="imagenInput" style="display:none">
                            <div class="mb-1 row">
                                <div class="col-sm-3">
                                    <label class="col-form-label" for="distribucion">Imagen</label>
                                </div>
                                <div class="col-sm-9">
                                    <input type="file" class="form-control" id="imagen" name="imagen">
                                </div>
                                <div class="imgPreview" style="display: none">
                                    
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="col-sm-9 offset-sm-3">
                            <button type="submit" class="btn btn-primary me-1">Guardar Cambios</button>
                            <button onclick="getPantalla({{$id}})" type="reset" class="btn btn-danger">Restaurar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-12" id="optionsPreview">
        
    </div>
    
</form>
@endsection

@section('scripts')
<script>
    // generar datos de la pantalla segun el id llamando a la funcion getPantalla que esta en main.js
    const id = $('#id').val()
    getPantalla(id);
</script>
@endsection