const url_real = document.getElementById('urlreal').dataset.url;

document.addEventListener("DOMContentLoaded", function () {
    cargarMisReservas();
    
    if (document.getElementById("calendar")) {
        iniciarCalendario();
    }

    if (document.getElementById("sede")) {
        document.getElementById("sede").addEventListener("change", loadCarros);
    }


    // Asegurar que los elementos existen antes de asignar eventos
    const fechaReserva = document.getElementById("fechaReserva");
    const horaInicio = document.getElementById("horaInicio");
    const horaFin = document.getElementById("horaFin");
    

    if (fechaReserva) {
        // Obtener la fecha de mañana en formato YYYY-MM-DD
        const today = new Date();
        today.setDate(today.getDate() + 1); // Sumamos 1 día para evitar hoy
        const minDate = today.toISOString().split("T")[0];

        // Establecer el valor mínimo en el input
        fechaReserva.setAttribute("min", minDate);
    }


    if (horaInicio && horaFin) {

        horaInicio.setAttribute("min", "07:30");
        horaInicio.setAttribute("max", "15:00");
        horaFin.setAttribute("min", "08:00");
        horaFin.setAttribute("max", "16:00");

        // Evento al cambiar la hora de inicio
        horaInicio.addEventListener("change", function () {
            const inicio = horaInicio.value;
            if (!inicio) return;

            // Convertir a objeto Date para manipular la hora
            let [hora, minutos] = inicio.split(":").map(Number);

            if (hora < 7 || (hora === 7 && minutos < 30)) {
                mostrarNotificacion("⚠️ La hora mínima de reserva es 7:30 AM");
                horaInicio.value = "07:30";
                hora = 7;
                minutos = 30;
            }

            let nuevaHora = hora + 1; // Sumar 1 hora
           
            // Ajustar si sobrepasa las 23:59
            if (nuevaHora > 16) nuevaHora = 16;

            // Formatear a HH:MM
            const nuevaHoraFin = nuevaHora.toString().padStart(2, "0") + ":" + minutos.toString().padStart(2, "0");

            // Asignar la nueva hora fin automáticamente
            horaFin.value = nuevaHoraFin;
            horaFin.setAttribute("min", nuevaHoraFin);
        });

        // Validar que la hora de fin no sea menor a la de inicio
        
        
        horaFin.addEventListener("change", function () {
            if (horaInicio.value && horaFin.value) {
                if (horaFin.value <= horaInicio.value) {
                    mostrarNotificacion("⚠️ La hora de fin debe ser posterior a la hora de inicio.");
                    horaFin.value = ""; // Limpiar el campo si la hora es inválida
                }

                if (horaFin.value > "16:00") {
                    mostrarNotificacion("⚠️ La hora máxima de reserva es hasta las 4:00 PM.");
                    horaFin.value = "16:00"; // Ajustar la hora fin al máximo permitido
                }
            }
        });
    }

    if (fechaReserva && horaInicio && horaFin) {
        fechaReserva.addEventListener("change", verificarDisponibilidad);
        horaInicio.addEventListener("change", verificarDisponibilidad);
        horaFin.addEventListener("change", verificarDisponibilidad);
        console.log("Eventos de disponibilidad registrados correctamente");
    } else {
        console.error("Uno o más elementos de fecha y hora no se encontraron en el DOM.");
    }
});


function loadCarros() {
    const sedeSeleccionada = document.getElementById("sede").value;
    const carroSelect = document.getElementById("carroSelect");

    carroSelect.innerHTML = ""; // Limpiar antes de agregar nuevos datos

    if (!sedeSeleccionada) return;

    fetch(`${url_real}/api/carros?sede=${sedeSeleccionada}`)  // Cambiar a `/reservas/carros?sede=Medellín` si usas ReservaController
        .then(response => response.json())
        .then(data => {
            console.log("Datos recibidos de la API:", data);
            if (!Array.isArray(data)) {
                console.error("Error: La API no devolvió un array:", data);
                return;
            }
            if (data.length === 0) {
                carroSelect.innerHTML = "<p>No hay carros disponibles en esta sede.</p>";
                return;
            }

            data.forEach(carro => {
                const div = document.createElement("div");
                div.className = "carro-item";
                div.textContent = `${carro.nombre} (${carro.totalpc} Computadores)`;
                div.dataset.carroId = carro.id;
                div.dataset.capacidad = carro.totalpc;
                div.onclick = () => selectCarro(div);

                carroSelect.appendChild(div);
            });
        })
        .catch(error => console.error("Error al cargar carros:", error));
}




// Iniciar el calendario con FullCalendar
function iniciarCalendario() {
    var calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        nowIndicator: true,
        locale: 'es',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'timeGridWeek,timeGridDay'
        },
        slotMinTime: '07:30:00',
        slotMaxTime: '17:00:00',
        businessHours: {
            daysOfWeek: [1, 2, 3, 4, 5], // Lunes a Viernes
            startTime: '07:00',
            endTime: '17:00',
        },
        weekends: false,
        allDaySlot: false,
    });

    calendar.render();
}

// Función para abrir el modal de reserva
function abrirModalReserva() {
    document.getElementById("modalReserva").style.display = "block";
}

// Función para cerrar el modal de reserva
function cerrarModalReserva() {
    document.getElementById("modalReserva").style.display = "none";
}


function selectCarro(carroElement) {
    // Deseleccionar cualquier carro previamente seleccionado
    document.querySelectorAll('.carro-item').forEach(item => item.classList.remove('selected'));

    // Seleccionar el carro actual
    carroElement.classList.add('selected');

    // Guardar el ID del carro seleccionado
    const carroId = carroElement.dataset.carroId;

    // Habilitar el botón de reservar
    document.getElementById('abrirModalReservaBtn').disabled = false;

    // Cargar el calendario con las reservas de este carro
    loadCalendar(carroId);
}


function loadCalendar(carroId, fechaSeleccionada = null) {
    fetch(`${url_real}/api/reservas?carro_id=${carroId}`)
        .then(response => response.json())
        .then(data => {
            console.log("Reservas recibidas para el calendario:", data);

            if (!Array.isArray(data)) {
                console.error("Error: La API no devolvió un array:", data);
                return;
            }

            const calendarEl = document.getElementById('calendar');

            if (window.calendarInstance) {
                window.calendarInstance.destroy();
            }

            window.calendarInstance = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                nowIndicator: true,
                locale: 'es',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'timeGridWeek,timeGridDay'
                },
                slotMinTime: '07:30:00',  // ✅ Ahora la hora mínima es 7:30 AM
                slotMaxTime: '17:00:00',
                businessHours: {
                    daysOfWeek: [1, 2, 3, 4, 5], // Lunes a Viernes
                    startTime: '07:30',  // ✅ Ajustado a 7:30 AM
                    endTime: '17:00',
                },
                weekends: false,
                allDaySlot: false,
                events: data,

                // 📌 Solo cambiar la fecha si se ha seleccionado una nueva, de lo contrario usa la actual
                initialDate: fechaSeleccionada || new Date().toISOString().split("T")[0], 

                eventClick: function(info) {
                    const event = info.event;
                    abrirModalDetalleReserva({
                        carro: event.extendedProps.carro,
                        grupo: event.extendedProps.grupo,
                        responsable: event.extendedProps.responsable,
                        fecha: event.extendedProps.fecha,
                        horaInicio: event.start.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' }),
                        horaFin: event.end.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' }),
                        cantidadComputadores: event.extendedProps.cantidadComputadores,
                        nombre_carro: event.extendedProps.nombre_carro
                    });
                },

                eventColor: '#ffa81d'
            });

            window.calendarInstance.render();
            
            // 📌 Si hay una fecha seleccionada, mover el calendario a esa fecha
            if (fechaSeleccionada) {
                window.calendarInstance.gotoDate(fechaSeleccionada);
            }
        })
        .catch(error => console.error("Error al cargar reservas en el calendario:", error));
}







function abrirModalReserva() {
    const selectedCarro = document.querySelector('.carro-item.selected');
    if (!selectedCarro) {
        alert('Debes seleccionar un carro antes de reservar.');
        return;
    }

    // Guardar el carro en el formulario de reserva
    document.getElementById('formReserva').dataset.carroId = selectedCarro.dataset.carroId;

    document.getElementById("modalReserva").style.display = "block";
}


function limpiarFormularioReserva() {
    document.getElementById('formReserva').reset();

    // Restablecer los valores específicos manualmente si es necesario
    document.getElementById('fechaReserva').value = "";
    document.getElementById('horaInicio').value = "";
    document.getElementById('horaFin').value = "";
    document.getElementById('cantidadComputadores').value = "";
    document.getElementById('grupo').value = "";
    document.getElementById('disponibilidad').textContent = ""; // Limpiar mensaje de disponibilidad

    console.log("Formulario de reserva reiniciado.");
}


function abrirModalDetalleReserva(reserva) {
    console.log("Reserva seleccionada:", reserva); // 🛠️ Depuración
    document.getElementById('detalleCarro').innerText = reserva.nombre_carro;
    document.getElementById('detalleGrupo').innerText = reserva.grupo;
    document.getElementById('detalleResponsable').innerText = reserva.responsable;
    document.getElementById('detalleHoraInicio').innerText = reserva.horaInicio;
    document.getElementById('detalleHoraFin').innerText = reserva.horaFin;
    document.getElementById('detalleCantidadComputadores').innerText = reserva.cantidadComputadores;
    document.getElementById('modalDetalleReserva').style.display = 'block';
}

function cerrarModalDetalleReserva() {
    document.getElementById('modalDetalleReserva').style.display = 'none';
}


function verificarDisponibilidad() {
    const carroElement = document.querySelector('.carro-item.selected');
    const carroId = carroElement ? carroElement.dataset.carroId : null;
    const fecha = document.getElementById('fechaReserva').value;
    const horaInicio = document.getElementById('horaInicio').value;
    const horaFin = document.getElementById('horaFin').value;
    const disponibilidadMensaje = document.getElementById('disponibilidad');
    const boton = document.getElementById('btn-reservam')

    console.log("Ejecutando verificarDisponibilidad");
    console.log("Carro ID:", carroId);
    console.log("Fecha:", fecha);
    console.log("Hora Inicio:", horaInicio);
    console.log("Hora Fin:", horaFin);

    if (!carroId || !fecha || !horaInicio || !horaFin) {
        console.warn("Algunos valores son nulos o no han sido seleccionados.");
        boton.disabled = true;
        disponibilidadMensaje.textContent = "Seleccione una fecha y hora válida.";
        return;
    }

    fetch(`${url_real}/api/disponibilidad?carro_id=${carroId}&fecha=${fecha}&horaInicio=${horaInicio}&horaFin=${horaFin}`)
        .then(response => response.json())
        .then(data => {
            console.log("Respuesta de API:", data);

            if (data.error) {
                disponibilidadMensaje.textContent = data.error;
                return;
            }

            const disponibles = data.computadoresDisponibles;
            console.log(disponibles)
            if (disponibles > 0) {
                disponibilidadMensaje.textContent = `Computadores disponibles: ${disponibles}`;
                document.getElementById('cantidadComputadores').max = disponibles;
                boton.disabled = false;
            } else {
                disponibilidadMensaje.textContent = "Este carrito no está disponible en este horario";
                boton.disabled = true;
            }
        })
        .catch(error => {
            console.error("Error al verificar disponibilidad:", error);
            reservarBtn.disabled = true;
        });
}



// NOTIFICACION

function mostrarNotificacion(mensaje) {
    const contenedor = document.getElementById("notificacion-container");

    // Crear la notificación
    const notificacion = document.createElement("div");
    notificacion.classList.add("notificacion");

    // Agregar contenido con ícono y texto
    notificacion.innerHTML = `<i>✅</i> ${mensaje}`;


    // Agregar la notificación al contenedor
    contenedor.appendChild(notificacion);

    // Mostrar con animación
    setTimeout(() => {
        notificacion.style.opacity = "1";
        notificacion.style.transform = "translateY(0)";
    }, 100);

    // Eliminar la notificación después de 4 segundos con animación
    setTimeout(() => {
        notificacion.style.opacity = "0";
        notificacion.style.transform = "translateY(-20px)";
        setTimeout(() => {
            notificacion.remove();
        }, 300);
    }, 4000);
}




// ----------------- RESERVAR ----------------


document.getElementById('formReserva').addEventListener('submit', function(event) {
    event.preventDefault(); // Evita que recargue la página

    const selectedCarro = document.querySelector('.carro-item.selected');
    if (!selectedCarro) {
        alert('Debes seleccionar un carro antes de reservar.');
        return;
    }

    const fechaReserva = document.getElementById('fechaReserva').value; // Obtener la fecha seleccionada

    const data = {
        carro: selectedCarro.dataset.carroId,
        fecha: fechaReserva,  // Guardar la fecha
        horaInicio: document.getElementById('horaInicio').value,
        horaFin: document.getElementById('horaFin').value,
        cantidadComputadores: document.getElementById('cantidadComputadores').value,
        grupo: document.getElementById('grupo').value
    };

    console.log(data);

    fetch(`${url_real}/api/reservas/crear`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.status === 'success') {
            mostrarNotificacion("Reserva realizada con éxito ✅");
            limpiarFormularioReserva();
            cerrarModalReserva();

            // 📌 Ahora el calendario se actualizará y se moverá automáticamente a la fecha seleccionada
            loadCalendar(selectedCarro.dataset.carroId, fechaReserva); 

        } else {
            alert('Error al reservar: ' + result.message);
            mostrarNotificacion("Error al realizar la reserva ❌");
        }
    })
    .catch(error => console.error("Error al realizar la reserva:", error));
});


async function cargarMisReservas() {
    try {
        const respuesta = await fetch("/api/mis-reservas");
        const reservas = await respuesta.json();

        const tabla = document.getElementById("tabla-reservas");
        tabla.innerHTML = ""; // Limpiar tabla antes de agregar nuevas filas

        if (reservas.error) {
            tabla.innerHTML = `<tr><td colspan="7">${reservas.error}</td></tr>`;
            return;
        }

        reservas.forEach(reserva => {
            const fila = document.createElement("tr");

            fila.innerHTML = `
                <td>${reserva.nombre_carro}</td>
                <td>${reserva.grupo}</td>
                <td>${reserva.fecha}</td>
                <td>${reserva.horaInicio}</td>
                <td>${reserva.horaFin}</td>
                <td>${reserva.cantidadComputadores}</td>
                <td>
                    <button class="btn-eliminar" onclick="eliminarReserva(${reserva.id})">Eliminar</button>
                </td>
            `;

            tabla.appendChild(fila);
        });
    } catch (error) {
        console.error("Error cargando reservas:", error);
    }
}

async function eliminarReserva(id) {
    if (!confirm("¿Estás seguro de eliminar esta reserva?")) return;

    try {
        const formData = new FormData();
        formData.append("id", id);

        const respuesta = await fetch("/api/eliminar-reserva", {
            method: "POST",
            body: formData
        });

        const resultado = await respuesta.json();

        if (resultado.resultado) {
            alert("Reserva eliminada correctamente");
            cargarMisReservas(); // Recargar la tabla
        } else {
            alert("Error al eliminar la reserva");
        }
    } catch (error) {
        console.error("Error al eliminar reserva:", error);
    }
}


