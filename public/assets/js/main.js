const user = JSON.parse($('#userData').val());
const baseUrl = $('#baseUrl').val();

$('.datatable').DataTable({
    "language": {
      "sProcessing":     "Procesando...",
      "sLengthMenu":     "Mostrar _MENU_ registros",
      "sZeroRecords":    "No se encontraron resultados",
      "sEmptyTable":     "Ningún dato disponible en esta tabla",
      "sInfo":           "Mostrando del _START_ al _END_ de un total de _TOTAL_ registros",
      "sInfoEmpty":      "Mostrando del 0 al 0 de un total de 0 registros",
      "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
      "sInfoPostFix":    "",
      "sSearch":         "Buscar:",
      "sUrl":            "",
      "sInfoThousands":  ",",
      "sLoadingRecords": "Cargando...",
      "oPaginate": {
        "sFirst":    "Primero",
        "sLast":     "Último",
        "sNext":     "Siguiente",
        "sPrevious": "Anterior"
      },
      "oAria": {
        "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
        "sSortDescending": ": Activar para ordenar la columna de manera descendente"
      }
    },
    "ordering": false,
});

// dar bienvenida al usuario por primera vez
if (!localStorage.getItem("welcome")) {
    setTimeout(function () {
        toastr['success'](
        'Has iniciado sesión correctamente. ¡Ya puedes empezar a explorar!',
        '👋 Bienvenido '+user.name,
        {
            closeButton: true,
            tapToDismiss: false
        }
        );
        // agregar al localstorage para que no se repita
        localStorage.setItem("welcome", true);
    }, 2000);
}

const changeTypeQuest = (valor) => {
    // variar opciones para ser llenados de nuevo
    opcionesPantalla = []

    if (valor == 'Selecciona una de las opciones') {
        $('#distribucionInput').show()
        $('#imagenInput').show()
        let distribucion = $('#distribucion').val()
        if (distribucion == 'Vertical') {
            // pintar drag and drop vertical de opciones
            getDistVertical()
        }

    } else if(valor == 'Ordena según tus prioridades') {
        $('#distribucionInput').hide()
        $('#imagenInput').hide()
        // pintar drag and drop vertical de opciones
        getDistVertical()

    } else if(valor == 'Desliza a la izquierda o derecha') {
        $('#distribucionInput').hide()
        $('#imagenInput').hide()
        // pintar drag and drop horizontal de opciones
        getDistHorizontal()
    } else {
        $('#distribucionInput').hide()
        $('#imagenInput').hide()
        $('#optionsPreview').html('')
    }
}

// variable que tendra las opciones generales de la pantalla
var opcionesPantalla = [];

const addOption = () => {
    let optionDefine = $('#optionDefine').val()

    // validar que no se repita
    if (optionDefine.trim().length === 0) {
        return;
    }

    // poner todo minuscula para evitar duplicados con mayuscula
    validate = optionDefine.trim().toLowerCase();

    // no permitir duplicados
    if (!opcionesPantalla.includes(validate)) {
      // agregar de primero
      opcionesPantalla.push(optionDefine.trim());
    } else {
        toastr['error'](
            'Registro duplicado',
            'Error',
            {
                closeButton: true,
                tapToDismiss: false
            }
        );
        return
    }

    // limpiar campo
    $('#optionDefine').val('')

    // actualizar opciones verticales
    getDistVertical()

    $('#optionDefine').focus()
}

function pressEnter(horizontal = false) {
    if (this.event.keyCode == 13) {
        if (horizontal) {
            addOptionHorizontal()
        } else {
            addOption()
        }
    }
}

// generar distribucion vertical de selecciona una de las opciones
const getDistVertical = () => {
    // cambiar clase del div
    $('#optionsPreview').attr('class', 'col-md-6 col-12');

    let output = `<div id="dd-with-handle">
        <div class="col-md-12 mb-1">
            <div class="input-group">
                <input onkeyup="pressEnter()" type="text" class="form-control" id='optionDefine' placeholder="Agregar nueva opcion...">
                <button class="btn btn-outline-primary waves-effect" type="button" onclick="addOption()">Add</button>
            </div>
        </div>
        <ul class="list-group" id="handle-list-1">`
        
        // generar opciones
        opcionesPantalla.forEach((element, index) => {
            output += `<li class="list-group-item" id="vertical${index}">
                <input type="hidden" name="opciones[]" value="${element}">
                <span class="handle me-50">+ ${element}</span>
                <div class='trashOptionVertical' onclick="removeOption('${index}')">
                    <i data-feather='trash-2'></i>
                </div>
            </li>`
        });

        output += `</ul>
    </div>`;

    $('#optionsPreview').html(output)

    // evitar que el formulario se envie con enter
    document.querySelectorAll('input[type=text]').forEach( node => node.addEventListener('keypress', e => {
        if(e.keyCode == 13) {
            console.log(e)
          e.preventDefault();
        }
    }))

    // refrescar componente drag and drop para que funcione luego de generar el html
    refreshDragula()
}

const removeOption = (element, type = 'vertical') => {
    // remover div visual
    $('#'+type+element).remove();

    // eliminar del array
    opcionesPantalla.splice(element, 1);
}

const getDistHorizontal = () => {
    // cambiar clase del div
    $('#optionsPreview').attr('class', 'row col-md-12 col-12');

    let output = `<div class="col-md-6 col-12 mb-1">
        <div class="input-group">
            <input type="file" id="img" class="form-control" accept="image/*">
        </div>
    </div>
    <div class="col-md-6 col-12 mb-1">
        <div class="input-group">
            <input onkeyup="pressEnter(true)" type="text" id="descriptionHorizontal" class="form-control" placeholder="descripcion...">
            <button onclick="addOptionHorizontal()" class="btn btn-outline-primary waves-effect" type="button">Add</button>
        </div>
    </div>
    <section id="draggable-cards" class='mt-2'>
        <div class="row" id="card-drag-area">`

        opcionesPantalla.forEach((element, index) => {
            output += `<div class="col-xl-2 col-md-6 col-sm-12 draggable" id="horizontal${index}">
                <input type="hidden" name="opciones[]" value='${JSON.stringify(element)}'>
                <div class="card">
                    <div class='trashOptionHorizontal' onclick="removeOption('${index}', 'horizontal')">
                        <i data-feather='x'></i>
                    </div>
                    <div class="card-header">
                        <img src="${element.img}">
                    </div>
                    <div class="card-body">
                        ${element.descripcion}
                    </div>
                </div>
            </div>`
        });
            
        output += `</div>
    </section>`;

    $('#optionsPreview').html(output)

    // refrescar componente drag and drop para que funcione luego de generar el html
    refreshDragula()
}

// convertir archivo a base64
const toBase64 = file => new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = () => resolve(reader.result);
    reader.onerror = error => reject(error);
});

const addOptionHorizontal = () =>{
    //Toma el archivo elegido por el input 
    let file = document.getElementById("img").files[0];
    let descripcion = $('#descriptionHorizontal').val()

    // validar que hay una imagen y una descripcion
    if(file == undefined || descripcion == 0){
        toastr['error'](
            'Debes seleccionar una imagen y escribir una descripcion',
            'Error',
            {
                closeButton: true,
                tapToDismiss: false
            }
        );

        return
    }

    // convertir archivo
    toBase64(file).then(function(result) {
        // agregar elemento al array de opcionesPantallas
        opcionesPantalla.push({descripcion: descripcion.trim(), img: result})

        // renderizar distribucion horizontal
        getDistHorizontal()
    });    
}

const changeDistribution = (valor) => {
    // variar opciones para ser llenados de nuevo
    opcionesPantalla = [];

    if (valor == 'Horizontal') {
        $('#imagenInput').hide()
        // pintar drag and drop vertical de opciones
        getDistHorizontal()
    } else {
        $('#imagenInput').show()
        // pintar drag and drop vertical de opciones
        getDistVertical()
    }
}

const refreshDragula = () => {
    // Draggable Cards
    dragula([document.getElementById('card-drag-area')]);

    // Sortable Lists
    dragula([document.getElementById('basic-list-group')]);
    dragula([document.getElementById('multiple-list-group-a'), document.getElementById('multiple-list-group-b')]);

    // Cloning
    dragula([document.getElementById('badge-list-1'), document.getElementById('badge-list-2')], {
        copy: true
    });

    // With Handles

    dragula([document.getElementById('handle-list-1'), document.getElementById('handle-list-2')], {
        moves: function (el, container, handle) {
        return handle.classList.contains('handle');
        }
    });

    // refrescar iconos
    feather.replace();

    // evitar que el formulario se envie con enter
    document.querySelectorAll('input[type=text]').forEach( node => node.addEventListener('keypress', e => {
        if(e.keyCode == 13) {
          e.preventDefault();
        }
    }))
}

$('#imagen').on('change', function(){
    let file = document.getElementById("imagen").files[0];
    // convertir archivo
    toBase64(file).then(function(result) {
        // imprimir imagen al usuario preview
        $('.imgPreview').show()
        $('.imgPreview').html(`<img id="imgPreview" src="${result}">`)
    });
})

const getPantalla = (id) => {
    fetch(baseUrl+'api/getPantalla/'+id)
    .then(response => response.json())
    .then(data => {
        // guardar las opciones en la variable global
        opcionesPantalla = JSON.parse(data.opciones);

        console.log(opcionesPantalla)

        // imprimir informacion principal en los inputs
        $('#pregunta').val(data.pregunta);
        $('#titulo').val(data.titulo);
        $("#tipo option[value='"+data.tipo+"']").attr("selected",true);
        $('#distribucion').val(data.distribucion);

        // mostrar imagen principal si tiene
        if (data.imagen) {
            $('.imgPreview').show()
            $('.imgPreview').html(`<img id="imgPreview" src="${data.imagen}" alt="">`)
        } else {
            $('.imgPreview').hide()
        }
        // mostrar distribucion e imagen si lo requiere
        let valor = data.tipo;
        if (valor == 'Selecciona una de las opciones') {
            $('#distribucionInput').show()
            $('#imagenInput').show()
            let distribucion = $('#distribucion').val()
            if (distribucion == 'Vertical') {
                // pintar drag and drop vertical de opciones
                getDistVertical()
            } else {
                getDistHorizontal()
            }

        } else if(valor == 'Ordena según tus prioridades') {
            $('#distribucionInput').hide()
            $('#imagenInput').hide()
            // pintar drag and drop vertical de opciones
            getDistVertical()

        } else if(valor == 'Desliza a la izquierda o derecha') {
            $('#distribucionInput').hide()
            $('#imagenInput').hide()
            // pintar drag and drop horizontal de opciones
            getDistHorizontal()
        } else {
            $('#distribucionInput').hide()
            $('#imagenInput').hide()
            $('#optionsPreview').html('')
        }
    });
}