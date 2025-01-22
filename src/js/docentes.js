// VARIABLES

const estudiante_sel = {
    codigo: '',
    fecha: '',
    grupo: '',
    reporte: ''
}

const hoy = new Date().toISOString().split('T')[0];

let estudiante_global = []
let grupos_global = []
let reportes_global = []
let estadoGrupos = []; // Variable para almacenar el estado de los grupos
let estudianteActual = null;
let tipoReporteActual = "";


// DOM

document.addEventListener("DOMContentLoaded", function () {
    iniciarApp();
    cargarEstadoGrupos(); // Cargaar el estado de los grupos al inicio

     // Agregar evento al buscador
     const buscador = document.getElementById("buscador");
     if (buscador) {
         buscador.addEventListener("keyup", filtrarEstudiantesPorBusqueda);
     }
});


async function cargarEstadoGrupos() {
    try {
        const respuesta = await fetch("http://localhost:3002/api/estado-grupos");
        estadoGrupos = await respuesta.json(); // Guardar el estado de los grupos
        console.log("Estado de los grupos:", estadoGrupos);
    } catch (error) {
        console.error("Error al cargar el estado de los grupos:", error);
    }
}

function iniciarApp(){
    // consultarGrupos();
    consultarApi();
    consultarReportes();
    cargarAsignaciones();
    verificarEstadoGrupo(); // Verificar el estado del grupo seleccionado

}



// CONSULTAS

// async function consultarGrupos(){
//     try {
//         const url = 'http://localhost:3002/api/grupos';
//         const resultado = await fetch(url);
//         grupos_global = await resultado.json();

//         mostrarGrupos(grupos_global);

//     } catch (error) {
//         console.log(error);
//     }
// }

async function cargarAsignaciones() {
    try {
        const url = 'http://localhost:3002/api/asignaciones';
        const respuesta = await fetch(url);
        const asignaciones = await respuesta.json();

        // console.log("Asignaciones recibidas:", asignaciones);

        // Seleccionar los elementos del DOM
        const selectGrupos = document.getElementById("grupos");
        const selectAsignaturas = document.getElementById("asignatura");

        if (!selectGrupos || !selectAsignaturas) {
            console.error("Error: No se encontraron los elementos select en el DOM.");
            return;
        }

        // Limpiar los selectores antes de agregar opciones
        selectGrupos.innerHTML = `<option value="">--Seleccione--</option>`;
        selectAsignaturas.innerHTML = `<option value="">--Seleccione--</option>`;

        // Llenar los selectores con los datos recibidos de la API
        asignaciones.forEach(asignacion => {
            if (asignacion.nombre_grupo) {
                const opcionGrupo = document.createElement("option");
                opcionGrupo.value = asignacion.grupoid;
                opcionGrupo.textContent = asignacion.nombre_grupo;
                selectGrupos.appendChild(opcionGrupo);
            }

            if (asignacion.nombre_asignatura) {
                const opcionAsignatura = document.createElement("option");
                opcionAsignatura.value = asignacion.cursoid;
                opcionAsignatura.textContent = asignacion.nombre_asignatura;
                selectAsignaturas.appendChild(opcionAsignatura);
            }
        });

        // console.log("Opciones de grupos agregadas:", selectGrupos.innerHTML);
        // console.log("Opciones de asignaturas agregadas:", selectAsignaturas.innerHTML);

    } catch (error) {
        console.error("Error al cargar asignaciones:", error);
    }
}







async function consultarApi(){
    try {
        const url = 'http://localhost:3002/api/estudiantes';
        const resultado = await fetch(url);
        estudiante_global = await resultado.json();

        mostrarEstudiantes(estudiante_global);

        document.getElementById("grupos").addEventListener("change", filtrarEstudiantes);

    } catch (error) {
        console.log(error);
    }
}

async function consultarReportes(){
    try {
        const url = 'http://localhost:3002/api/reporte';
        const resultado = await fetch(url);
        reportes_global = await resultado.json();

        mostrarReportes(reportes_global);

        // document.getElementById("grupos").addEventListener("change", filtrarEstudiantes);

    } catch (error) {
        console.log(error);
    }
}


// FUNCIONES DE MUESTRA


function mostrarGrupos(grupos){
    grupos.forEach(grupo => {
        const {nombre, id} = grupo

        const nombreGrupo = document.createElement('OPTION');
        nombreGrupo.textContent = nombre;
        nombreGrupo.value = id;

        document.querySelector('#grupos').appendChild(nombreGrupo)
    })
}


function mostrarReportes(registros) {
    const tabla = document.getElementById('filas'); // Seleccionar el tbody donde agregar las filas
    tabla.innerHTML = ""; // Limpiar la tabla antes de agregar nuevos reportes

    // Obtener el usuario autenticado desde el HTML
    const usuarioActual = document.getElementById("usuario-actual").dataset.nombre || "";

    // Ordenar registros por fecha (descendente)
    registros.sort((a, b) => new Date(b.fechahora) - new Date(a.fechahora));

    registros.forEach(registro => {
        const { id, estudiante, reporte, comentario, responsable, nombre_asignatura, fechahora, nombre_grupo, codigoid } = registro;

        // Crear una nueva fila para el reporte
        const fila = document.createElement('tr');

        // Celda del estudiante (no editable)
        const r_estudiante = document.createElement('td');
        r_estudiante.textContent = estudiante;

        // 🔥 Celda del reporte (Editable con <select>)
        const r_reporte = document.createElement('td');
        if (responsable === usuarioActual) {
            const selectReporte = document.createElement('select');
            selectReporte.innerHTML = `
                <option value="falta" ${reporte === "falta" ? "selected" : ""}>Falta</option>
                <option value="excusa" ${reporte === "excusa" ? "selected" : ""}>Excusa</option>
                <option value="tarde" ${reporte === "tarde" ? "selected" : ""}>Tarde</option>
            `;
            selectReporte.addEventListener('change', () => actualizarReporte(id, selectReporte.value, comentario));
            r_reporte.appendChild(selectReporte);
        } else {
            r_reporte.textContent = reporte;
        }

        // 🔥 Celda del comentario (Editable con <textarea>)
        const r_comentario = document.createElement('td');
        if (responsable === usuarioActual) {
            const textareaComentario = document.createElement('textarea');
            textareaComentario.value = comentario;
            textareaComentario.rows = 2;
            textareaComentario.addEventListener('blur', () => actualizarReporte(id, reporte, textareaComentario.value));
            r_comentario.appendChild(textareaComentario);
        } else {
            r_comentario.textContent = comentario;
        }

        // Celda del responsable
        const r_responsable = document.createElement('td');
        r_responsable.textContent = responsable;

        // Celda de la asignatura
        const r_asignatura = document.createElement('td');
        r_asignatura.textContent = nombre_asignatura;

        // Celda de la fecha
        const r_fecha = document.createElement('td');
        r_fecha.textContent = fechahora;

        // Celda del grupo
        const r_grupo = document.createElement('td');
        r_grupo.textContent = nombre_grupo;

        // Celda del código
        const r_codigo = document.createElement('td');
        r_codigo.textContent = codigoid;

        // Celda para acciones (Eliminar solo si el usuario es el responsable)
        const r_acciones = document.createElement('td');
        if (responsable === usuarioActual) {
            const botonEliminar = document.createElement('button');
            botonEliminar.textContent = "Eliminar";
            botonEliminar.classList.add("btn-eliminar");
            botonEliminar.onclick = () => eliminarReporte(id);
            r_acciones.appendChild(botonEliminar);
        } else {
            r_acciones.textContent = "🔒";
        }

        // Agregar las celdas a la fila
        fila.appendChild(r_estudiante);
        fila.appendChild(r_reporte);
        fila.appendChild(r_comentario);
        fila.appendChild(r_responsable);
        fila.appendChild(r_asignatura);
        fila.appendChild(r_fecha);
        fila.appendChild(r_grupo);
        fila.appendChild(r_acciones);

        // Agregar la fila a la tabla
        tabla.appendChild(fila);
    });
}






function mostrarEstudiantes(estudiantes) {
    estudiantes.forEach(estudiante => {
        const { codigo, documento, fechaNacimiento, nombre_grupo, nombre, estado, fecha , email} = estudiante;

        const nombreEstudiante = document.createElement('H4');
        nombreEstudiante.classList.add('nombre-estudiante');
        nombreEstudiante.textContent = nombre;

        const estadoEstudiante = document.createElement('P');
        estadoEstudiante.classList.add('estado-estudiante');
        estadoEstudiante.textContent = estado ? estado : '';
        if (estado && fecha === hoy) {
            estadoEstudiante.classList.remove('oculto');
        } else {
            estadoEstudiante.classList.add('oculto');
        }

        // Función para crear botones con lápiz
        function crearBotonConLapiz(texto, clase, novedad) {
            const boton = document.createElement('BUTTON');
            boton.classList.add('boton-reporte', clase);
            boton.textContent = texto;
            boton.onclick = function (event) {
                event.stopPropagation();
                registrarNovedad(estudiante, novedad, false); // Registrar sin modal
            };

            const lapiz = document.createElement('SPAN');
            lapiz.classList.add('lapiz-icono');
            lapiz.innerHTML = "✏️";
            lapiz.onclick = function (event) {
                event.stopPropagation();
                registrarNovedad(estudiante, novedad, true); // Registrar con modal
            };

            boton.appendChild(lapiz);
            return boton;
        }

        // Crear botones
        const botonFalta = crearBotonConLapiz('Falta', 'btn-falta', 'falta');
        const botonTarde = crearBotonConLapiz('Tarde', 'btn-tarde', 'tarde');
        const botonExcusa = crearBotonConLapiz('Excusa', 'btn-excusa', 'excusa');

        const ficha = document.createElement('DIV');
        ficha.classList.add('ficha');
        ficha.dataset.idFicha = codigo;
        ficha.onclick = function () {
            removerSeleccion();
            ficha.classList.add('seleccionado');
            verEstudiante(nombre, codigo, documento, fechaNacimiento, nombre_grupo, email);
        };

        const acciones = document.createElement('DIV');
        acciones.classList.add('acciones');
        acciones.appendChild(botonFalta);
        acciones.appendChild(botonTarde);
        acciones.appendChild(botonExcusa);

        const info = document.createElement('DIV');
        info.classList.add('info');
        info.appendChild(nombreEstudiante);
        info.appendChild(estadoEstudiante);

        ficha.appendChild(info);
        ficha.appendChild(acciones);

        document.querySelector('#fichas').appendChild(ficha);
    });
}



function mostrarNotificacion(mensaje) {
    const contenedor = document.getElementById("notificacion-container");

    // Crear la notificación
    const notificacion = document.createElement("div");
    notificacion.classList.add("notificacion");

    // Agregar contenido con ícono y texto
    notificacion.innerHTML = `<i>✅</i> ${mensaje}`;

    // Agregar la notificación al contenedor
    contenedor.appendChild(notificacion);

    // Eliminar la notificación después de 4 segundos
    setTimeout(() => {
        notificacion.remove();
    }, 4000);
}


document.getElementById("ver-todos").addEventListener("click", function () {
    const grupoSeleccionado = document.getElementById("grupos").value;

    if (!grupoSeleccionado) {
        mostrarNotificacion("⚠️ Debes seleccionar un grupo para ver los registros.");
        return;
    }

    // Quitar selección de estudiante
    removerSeleccion();

    // Filtrar solo reportes de ese grupo
    let reportesGrupo = reportes_global.filter(reporte => String(reporte.grupo) === String(grupoSeleccionado));

    mostrarReportes(reportesGrupo);
});




// ----------------------- FILTROS --------------------



function filtrarEstudiantes() {
    const grupoSeleccionado = document.getElementById("grupos").value; // Obtener el grupo seleccionado

    console.log(grupoSeleccionado)
    // Filtrar estudiantes solo si se selecciona un grupo
    let estudiantesFiltrados = grupoSeleccionado
        ? estudiante_global.filter(est => est.grupoid === grupoSeleccionado)
        : estudiante_global; // Si no hay selección, mostrar todos

    // Limpiar el contenedor antes de agregar los nuevos resultados
    document.querySelector("#fichas").innerHTML = "";

    // Mostrar los estudiantes filtrados
    mostrarEstudiantes(estudiantesFiltrados);
}




function filtrarReportesPorEstudiante(codigoEstudiante) {
    console.log("Código seleccionado:", codigoEstudiante);
    console.log("Lista de reportes:", reportes_global);

    let reportesFiltrados = reportes_global.filter(reporte => String(reporte.codigoid) === String(codigoEstudiante));

    console.log("Reportes filtrados:", reportesFiltrados); // Esto nos dirá si hay coincidencias

    mostrarReportes(reportesFiltrados);
}

function filtrarTabla(columna) {
    // Obtener el valor del input
    const input = document.querySelectorAll("thead input")[columna];
    const filtro = input.value.toUpperCase();
    const tabla = document.getElementById("miTabla");
    const filas = tabla.getElementsByTagName("tr");
    
    // Iterar sobre las filas del cuerpo de la tabla
    for (let i = 1; i < filas.length; i++) {
      const celda = filas[i].getElementsByTagName("td")[columna];
      if (celda) {
        const textoCelda = celda.textContent || celda.innerText;
        filas[i].style.display = textoCelda.toUpperCase().indexOf(filtro) > -1 ? "" : "none";
      }
    }
  }

  function filtrarEstudiantesPorBusqueda() {
    const textoBusqueda = document.getElementById("buscador").value.toLowerCase().trim();

    // Filtrar estudiantes por nombre, apellidos, código o documento
    let estudiantesFiltrados = estudiante_global.filter(estudiante => {
        const nombreCompleto = `${estudiante.nombre}`.toLowerCase();
        const codigo = `${estudiante.codigo}`.toLowerCase();
        const documento = `${estudiante.documento}`.toLowerCase();

        return nombreCompleto.includes(textoBusqueda) ||
               codigo.includes(textoBusqueda) ||
               documento.includes(textoBusqueda);
    });

    // Limpiar el contenedor antes de mostrar los resultados filtrados
    document.querySelector("#fichas").innerHTML = "";

    // Mostrar los estudiantes filtrados
    mostrarEstudiantes(estudiantesFiltrados);
}




// ------------------------ FUNCIONALIDADES ------------------


function removerSeleccion() {
    const fichas = document.querySelectorAll('.ficha.seleccionado');
    fichas.forEach(ficha => ficha.classList.remove('seleccionado'));
}


function verEstudiante(nombre, codigo, documento, fechaNacimiento, nombreGrupo, email){
    
    let info_nombre = document.getElementById('info_nombre');
    let info_codigo = document.getElementById('info_codigo');
    let info_documento = document.getElementById('info_documento');
    let info_fechaNacimiento = document.getElementById('info_fechaNacimiento');
    let info_grado = document.getElementById('info_grado');
    let info_correo = document.getElementById('info_correo');


    info_nombre.innerHTML = `<strong>Nombre: </strong>${nombre}`;
    info_codigo.innerHTML = `<strong>Código: </strong>${codigo}`;
    info_documento.innerHTML = `<strong>Documento: </strong>${documento}`;
    info_fechaNacimiento.innerHTML = `<strong>Fecha de Nacimiento: </strong>${fechaNacimiento}`;
    info_grado.innerHTML = `<strong>Grupo: </strong>${nombreGrupo}`;
    info_correo.innerHTML = `<strong>Correo: </strong>${email}`;


    // ACTUALIZAR estudiante_sel con el código del estudiante seleccionado
    estudiante_sel.codigo = codigo;

    // console.log("Código del estudiante seleccionado:", codigo);
    // console.log("Valor actualizado en estudiante_sel:", estudiante_sel.codigo);
    // Filtrar la tabla de reportes según el estudiante seleccionado
    filtrarReportesPorEstudiante(codigo);
}


// Calendario

function iniciarCalendario() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        events: '/api/reservas'
    });

    calendar.render();
}

// Función para abrir el modal
function abrirModalReserva() {
    document.getElementById("modalReserva").style.display = "block";
}

// Función para cerrar el modal
function cerrarModalReserva() {
    document.getElementById("modalReserva").style.display = "none";
}






// REGISTROS

// Función para registrar novedad directamente o con comentario
async function registrarNovedad(estudiante, reporte, usarModal = false) {
    estudianteActual = estudiante;
    tipoReporteActual = reporte;

    // Si `usarModal` es verdadero, abrimos el modal para ingresar comentario
    if (usarModal) {
        abrirModalComentario(estudiante, reporte);
        return;
    }

    // Si no se usa el modal, registrar la novedad de inmediato sin comentario
    await enviarNovedad(estudiante, reporte, "");
}

async function actualizarReporte(id, nuevoReporte, nuevoComentario) {
    if (!id) return;

    const datos = new FormData();
    datos.append('id', id);
    datos.append('reporte', nuevoReporte);
    datos.append('comentario', nuevoComentario);

    try {
        const respuesta = await fetch("http://localhost:3002/api/actualizar-asistencia", {
            method: "POST",
            body: datos
        });

        const resultado = await respuesta.json();

        if (resultado.exito) {
            mostrarNotificacion("✅ Registro actualizado correctamente");
        } else {
            mostrarNotificacion("⚠️ Error al actualizar el registro.");
        }
    } catch (error) {
        console.error("Error en la actualización:", error);
        mostrarNotificacion("⚠️ Error en la actualización.");
    }
}


// Función para abrir el modal de comentarios
function abrirModalComentario(estudiante, reporte) {
    estudianteActual = estudiante;
    tipoReporteActual = reporte;

    document.getElementById("comentarioEstudiante").textContent = estudiante.nombre;
    document.getElementById("comentarioReporte").textContent = reporte;
    document.getElementById("comentario").value = ""; // Limpiar comentario

    document.getElementById("modalComentario").style.display = "block";
}

// Función para cerrar el modal de comentarios
function cerrarModalComentario() {
    document.getElementById("modalComentario").style.display = "none";
}

// Evento para enviar la novedad con comentario cuando se envíe el formulario del modal
document.getElementById("formComentario").addEventListener("submit", async function (event) {
    event.preventDefault(); // Evitar recarga de la página

    const comentario = document.getElementById("comentario").value.trim();
    if (!comentario) {
        mostrarNotificacion("⚠️ Debes escribir un comentario.");
        return;
    }

    await enviarNovedad(estudianteActual, tipoReporteActual, comentario);
    cerrarModalComentario();
});

// Función para enviar la novedad a la API
// Función para enviar la novedad a la API
// 🔥 Función para enviar la novedad a la API
async function enviarNovedad(estudiante, reporte, comentario) {
    if (!estudiante || !reporte) {
        mostrarNotificacion("⚠️ Ocurrió un error al registrar la novedad.");
        return;
    }

    const grupoSeleccionado = document.getElementById("grupos").value;
    const asignaturaSeleccionada = document.getElementById("asignatura").value;

    if (!grupoSeleccionado || !asignaturaSeleccionada) {
        mostrarNotificacion("⚠️ Debes seleccionar un grupo y una asignatura antes de registrar.");
        return;
    }

    const datos = new FormData();
    datos.append('estudiante', estudiante.nombre);
    datos.append('codigoid', estudiante.codigo);
    datos.append('reporte', reporte);
    datos.append('comentario', comentario);
    datos.append('grupo', grupoSeleccionado);
    datos.append("asignaturaid", asignaturaSeleccionada);

    try {
        console.log("📤 Enviando datos a la API...", Object.fromEntries(datos)); // 🛠 DEPURACIÓN

        const respuesta = await fetch("http://localhost:3002/api/asistencia", {
            method: 'POST',
            body: datos
        });

        const resultado = await respuesta.json();
        console.log("📥 Respuesta de la API:", resultado); // 🛠 DEPURACIÓN

        // ✅ Validar que la API devuelva exito: true
        if (resultado.resultado === true) {
            mostrarNotificacion("✅ Registro exitoso");

            // 🛠 DEPURACIÓN: Comprobar reportes antes de actualizar
            console.log("🔄 Cargando reportes después del registro...");

            await consultarReportes(); // Recargar reportes
            filtrarReportesPorEstudiante(estudiante.codigo); // Mostrar solo los del estudiante

            // 🔄 Actualizar la información del estudiante en info-detallada
            verEstudiante(estudiante.nombre, estudiante.codigo, estudiante.documento, estudiante.fechaNacimiento, estudiante.grupoid, estudiante.email);
        } else {
            console.error("⚠️ Error en la API:", resultado);
            mostrarNotificacion("⚠️ Error al registrar la novedad.");
        }
    } catch (error) {
        console.error("⚠️ Error en fetch:", error);
        mostrarNotificacion("⚠️ Error al registrar la novedad.");
    }
}



async function eliminarReporte(id) {
    if (!confirm("¿Estás seguro de que deseas eliminar este reporte?")) {
        return;
    }

    try {
        const url = "http://localhost:3002/api/eliminar-asistencia"; // Asegúrate de que la ruta sea correcta

        const formData = new FormData();
        formData.append("id", id);

        const respuesta = await fetch(url, {
            method: "POST",
            body: formData
        });

        if (!respuesta.ok) {
            throw new Error("Error al eliminar el reporte");
        }

        const resultado = await respuesta.json();
        console.log("Reporte eliminado:", resultado);

        // Volver a cargar los reportes desde la API
        await consultarReportes();

        // console.log("Código actual en estudiante_sel:", estudiante_sel.codigo);

        // Verificar si estudiante_sel.codigo tiene un valor válido antes de filtrar
        if (estudiante_sel.codigo) {
            filtrarReportesPorEstudiante(estudiante_sel.codigo);
        } else {
            console.warn("No hay estudiante seleccionado para filtrar reportes.");
        }

        // Mostrar notificación
        mostrarNotificacion("Reporte eliminado con éxito ❌");

    } catch (error) {
        console.error("Error al eliminar reporte:", error);
        mostrarNotificacion("Error al eliminar el reporte ❌");
    }
}


// Reporte grupo completo

// Función para verificar el estado del grupo seleccionado
function verificarEstadoGrupo() {
    const grupoId = document.getElementById("grupos").value;
    const botonGrupoCompleto = document.getElementById("grupo-completo");

    // Buscar si el grupo tiene el estado "completo" para la fecha actual
    const grupo = estadoGrupos.find(
        (g) => g.idgrupo == grupoId && g.completo === "completo" && g.fecha === hoy
    );

    // Deshabilitar o habilitar el botón según el estado del grupo
    if (grupo) {
        botonGrupoCompleto.disabled = true;
        botonGrupoCompleto.classList.add('deshabilitado')
        mostrarNotificacion(`El grupo está completo hoy 👍🏻`);
    } else {
        botonGrupoCompleto.disabled = false;
    }
}

// Escucha el cambio en el selector de grupos
document.getElementById("grupos").addEventListener("change", verificarEstadoGrupo);

// Función para marcar un grupo como completo
document.getElementById("grupo-completo").addEventListener("click", async function () {
    const grupoId = document.getElementById("grupos").value;
    const fechaActual = new Date().toISOString().split("T")[0]; // Fecha en formato YYYY-MM-DD

    if (!grupoId) {
        mostrarNotificacion("Seleccione un grupo");
        return;
    }

    const formData = new FormData();
    formData.append("grupo", grupoId);
    formData.append("completo", "completo");
    formData.append("fecha", fechaActual);

    try {
        const respuesta = await fetch("http://localhost:3002/api/grupo-completo", {
            method: "POST",
            body: formData,
        });

        const resultado = await respuesta.json();

        if (resultado.exito) {
            mostrarNotificacion("Grupo marcado como completo 👍🏻");
            document.getElementById("grupo-completo").disabled = true; // Desactivar el botón
            await cargarEstadoGrupos(); // Recargar el estado de los grupos
        } else {
            alert("Error al actualizar el grupo");
        }
    } catch (error) {
        console.error("Error al marcar grupo completo:", error);
    }
});

// MODAL

// function abrirModalComentario(estudiante, reporte) {
//     estudianteActual = estudiante;
//     tipoReporteActual = reporte;

//     document.getElementById("comentarioEstudiante").textContent = estudiante.nombre;
//     document.getElementById("comentarioReporte").textContent = reporte;
//     document.getElementById("comentario").value = ""; // Limpiar el campo de comentario

//     document.getElementById("modalComentario").style.display = "block";
// }

// function cerrarModalComentario() {
//     document.getElementById("modalComentario").style.display = "none";
// }