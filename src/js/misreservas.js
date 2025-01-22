
document.addEventListener("DOMContentLoaded", function() {
    cargarMisReservas();
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
            mostrarNotificacion("Reserva eliminada correctamente");
            cargarMisReservas(); // Recargar la tabla
        } else {
            alert("Error al eliminar la reserva");
        }
    } catch (error) {
        console.error("Error al eliminar reserva:", error);
    }
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
