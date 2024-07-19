let paso = 1;
const pasoInicial = 1;
const pasoFinal = 3;

const cita = {
    id: '',
    nombre: '',
    fecha: '',
    hora: '',
    servicios: []
};

document.addEventListener('DOMContentLoaded', function () {
    iniciarApp();
});

function iniciarApp() {
    mostrarSeccion();       // Muestra y oculta las secciones
    tabs();                 // Cambia la sección cuando se presionan los tabs
    botonesPaginador();     // Agrega o quita los botones al paginador
    paginaSiguiente();
    paginaAnterior();
    consultarAPI();         // Consulta el API en el backend de PHP

    idCliente();            // Añade el id del cliente al objeto de la cita
    nombreCliente();        // Añade el nonbre del cliente al objeto de la cita 
    seleccionarFecha();     // Añade la fecha al objeto de la cita 
    seleccionarHora();      // Añade la hora al objeto de la cita 

    mostrarResumen();       // Muestra el resumen de la cita
}

function mostrarSeccion() {

    // Ocultar la seccion 
    const seccionAnterior = document.querySelector('.mostrar');

    if (seccionAnterior) {
        seccionAnterior.classList.remove('mostrar');
    }

    // Seleccionar la seccion
    const pasoSelector = `#paso-${paso}`;
    const seccion = document.querySelector(pasoSelector);
    seccion.classList.add('mostrar');

    // Remover la clase actual a los anteriores
    const tabAnterior = document.querySelector('.actual');
    if (tabAnterior) {
        tabAnterior.classList.remove('actual');
    }

    // Resaltar el tab actual
    const tab = document.querySelector(`[data-paso="${paso}"]`);
    tab.classList.add('actual');
}

function tabs() {
    const botones = document.querySelectorAll('.tabs button');

    botones.forEach(boton => {
        boton.addEventListener('click', function (evt) {
            evt.preventDefault();

            paso = parseInt(evt.target.dataset.paso);

            mostrarSeccion();
            botonesPaginador();

        });
    });
}

function botonesPaginador() {
    const paginaAnterior = document.querySelector('#anterior');
    const paginaSiguiente = document.querySelector('#siguiente');

    if (paso === 1) {
        paginaAnterior.classList.add('ocultar');
        paginaSiguiente.classList.remove('ocultar');
    } else if (paso === 3) {
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.add('ocultar');
        mostrarResumen();
    } else {
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.remove('ocultar');
    }

    mostrarSeccion();
}

function paginaAnterior() {
    const paginaAnterior = document.querySelector('#anterior');
    paginaAnterior.addEventListener('click', function () {

        if (paso <= pasoInicial) return;

        paso--;

        botonesPaginador();
    })
}

function paginaSiguiente() {
    const paginaSiguiente = document.querySelector('#siguiente');
    paginaSiguiente.addEventListener('click', function () {

        if (paso >= pasoFinal) return;

        paso++;

        botonesPaginador();
    })
}

async function consultarAPI() {
    try {
        const url = `${location.origin}/servicios`;
        const resultado = await fetch(url);
        const servicios = await resultado.json();

        mostrarServicios(servicios);
    } catch (error) {
        console.log(error);
    }
}

async function mostrarServicios(servicios) {
    servicios.forEach(servicio => {
        const { id, nombre, precio } = servicio;
        const nombreServicio = document.createElement('p');
        nombreServicio.classList.add('nombre-servicio');
        nombreServicio.textContent = nombre;

        const precioServicio = document.createElement('p');
        precioServicio.classList.add('precio-servicio');
        precioServicio.textContent = precio;

        const servicioDiv = document.createElement('div');
        servicioDiv.classList.add('servicio');
        servicioDiv.dataset.idServicio = id;
        servicioDiv.onclick = function() {
            seleccionarServicio(servicio)
        };

        servicioDiv.appendChild(nombreServicio);
        servicioDiv.appendChild(precioServicio);

        document.querySelector('#servicios').appendChild(servicioDiv);

    });
}

function seleccionarServicio(servicio) {
    const { servicios } = cita;
    const {id} = servicio;
    const divServicio = document.querySelector(`[data-id-servicio="${id}"]`);

    if(servicios.some(agregado => agregado.id === id)) {
        cita.servicios = servicios.filter(agregado => agregado.id !== id);
        divServicio.classList.remove('seleccionado');
    } else {
        cita.servicios = [...servicios, servicio];
        divServicio.classList.add('seleccionado');
    }
      
}

function idCliente() {
    const id = document.querySelector('#id').value;
    cita.id = id;
}

function nombreCliente() {
    const nombre = document.querySelector('#nombre').value;
    cita.nombre = nombre;
}

function seleccionarFecha() {
    const inputFecha = document.querySelector('#fecha');
    inputFecha.addEventListener('input', function(evt) {
        const dia = new Date(evt.target.value).getUTCDay();
        if([6, 0].includes(dia)) {
            evt.target.value = '';
            mostrarAlerta('Fines de semana no permitidos', 'error', '.formulario');
        } else {
            cita.fecha = evt.target.value;
        }
    });
}

function seleccionarHora() {
    const inputHora = document.querySelector('#hora');
    inputHora.addEventListener('input', function(evt) {
        const horaCita = evt.target.value;
        const hora = horaCita.split(':')[0];
        if(hora < 10 || hora > 18) {
            evt.target.value = '';
            mostrarAlerta('Hora no válida', 'error', '.formulario');
        } else {
            cita.hora = horaCita;
        }
    });
}

function mostrarResumen() {
    const resumen = document.querySelector('.contenido-resumen');
    
    // Limpiar el contenido del resume
    while(resumen.firstChild) {
        resumen.removeChild(resumen.firstChild);
    }

    if(Object.values(cita).includes('') || cita.servicios.length === 0 ) {
        mostrarAlerta('Faltan datos de servicio, fecha u hora', 'error', '.contenido-resumen', false);
        return;
    }

    // Formatear el div del resumen
    const { nombre, fecha, hora, servicios } = cita;

    // Encabezado  para serviciosn en resumen
    const headingServicios = document.createElement('h3');
    headingServicios.textContent = 'Resumen de Servicios';
    resumen.appendChild(headingServicios);

    servicios.forEach(servicio => {
        const {id, nombre, precio} = servicio;

        const contenedorServicio = document.createElement('div');
        contenedorServicio.classList.add('contenedor-servicio');

        const textoServicio = document.createElement('p');
        textoServicio.textContent = nombre;

        const precioServicio = document.createElement('p');
        precioServicio.innerHTML = `<span>Precio:</span> ${precio}`;

        contenedorServicio.appendChild(textoServicio);
        contenedorServicio.appendChild(precioServicio);

        resumen.appendChild(contenedorServicio);
    });

     // Encabezado  para serviciosn en resumen
     const headingCita = document.createElement('h3');
     headingCita.textContent = 'Resumen de Cita';
     resumen.appendChild(headingCita);

      const nombreCliente = document.createElement('p');
      nombreCliente.innerHTML = `<span>Nombre:</span> ${nombre}`;
  
      // Formatear fecha
      const fechaObj = new Date(fecha);
      const mes = fechaObj.getMonth();
      const dia = fechaObj.getDate() + 2;
      const year = fechaObj.getFullYear();

      const fechaUTC = new Date(Date.UTC(year, mes, dia));
      const opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
      const fechaFormateada = fechaUTC.toLocaleDateString('es-CR', opciones);
      
      const fechaCita = document.createElement('p');
      fechaCita.innerHTML = `<span>Fecha:</span> ${fechaFormateada}`;
  
      const horaCita = document.createElement('p');
      horaCita.innerHTML = `<span>Hora:</span> ${hora} horas`;

    //  Botón para crear cita
    const botonReservar = document.createElement('button');
    botonReservar.classList.add('boton');
    botonReservar.textContent = 'Reservar Cita';
    botonReservar.onclick = reservarCita;

    resumen.appendChild(nombreCliente);
    resumen.appendChild(fechaCita);
    resumen.appendChild(horaCita);
    resumen.appendChild(botonReservar);
}

async function reservarCita() {
    // Formatear el div del resumen
    const { nombre, fecha, hora, servicios, id } = cita;
    const idServicios = servicios.map(servicio => servicio.id);

    const datos = new FormData();
    datos.append('usuarioId', id);
    datos.append('fecha', fecha);
    datos.append('hora', hora);
    datos.append('servicios', idServicios);

    try {
        // Petición hacia la API
        const url = `${location.origin}/api/citas`;

        const respuesta = await fetch(url, { 
            method: 'POST',
            body: datos
        });

        const resultado = await respuesta.json();

        if(resultado.resultado) {
            Swal.fire({
                icon: 'success',
                title: 'Cita Creada',
                text: 'Cita creada satisfactoriamente',
                button: 'Ok'
            }).then( () => {
                setTimeout(() => {
                    window.location.reload();
                }, 3000);
            });
        }
    } catch(error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Se presentó un error'
        });
    }
   

}

function mostrarAlerta(mensaje, tipo, elemento, desaparece = true) {
    const alertaPrevia = document.querySelector('.alerta');

    if (alertaPrevia) {
        alertaPrevia.remove();
    }

    const alerta = document.createElement('div');
    alerta.textContent = mensaje;
    alerta.classList.add('alerta');
    alerta.classList.add(tipo);

    const formulario = document.querySelector(elemento);
    formulario.appendChild(alerta);

    if (desaparece) {
        setTimeout(() => {
            alerta.remove();
        }, 3000);
    }
   
}