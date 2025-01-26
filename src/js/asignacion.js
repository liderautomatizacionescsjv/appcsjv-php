document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("modalAsignacion");
    const btnAbrir = document.getElementById("asignar-grupos");
    const btnCerrar = document.querySelector(".close");
    const form = document.getElementById("formAsignacion");

    // Abrir el modal
    btnAbrir.addEventListener("click", function () {
        modal.style.display = "flex";
        cargarOpciones();
    });

    // Cerrar el modal
    btnCerrar.addEventListener("click", function () {
        cerrarModalAsignacion();
    });

    function cerrarModalAsignacion() {
        modal.style.display = "none";
    }

    // Cargar opciones de grupos y asignaturas
    async function cargarOpciones() {
        try {
            const respuestaGrupos = await fetch("/api/grupos");
            const grupos = await respuestaGrupos.json();

            const selectGrupo = document.getElementById("selectGrupo");
            selectGrupo.innerHTML = '<option value="">-- Seleccionar --</option>';
            grupos.forEach(grupo => {
                selectGrupo.innerHTML += `<option value="${grupo.idgrupo}">${grupo.nombre}</option>`;
            });

            const respuestaAsignaturas = await fetch("/api/asignaturas");
            const asignaturas = await respuestaAsignaturas.json();

            const selectAsignatura = document.getElementById("selectAsignatura");
            selectAsignatura.innerHTML = '<option value="">-- Seleccionar --</option>';
            asignaturas.forEach(asignatura => {
                selectAsignatura.innerHTML += `<option value="${asignatura.idasignatura}">${asignatura.nombre}</option>`;
            });
        } catch (error) {
            console.error("Error cargando opciones:", error);
        }
    }

    // Enviar asignación al servidor
    form.addEventListener("submit", async function (event) {
        event.preventDefault();

        const grupoid = document.getElementById("selectGrupo").value;
        const cursoid = document.getElementById("selectAsignatura").value;

        if (!grupoid || !cursoid) {
            alert("Debe seleccionar un grupo y una asignatura");
            return;
        }

        try {
            const respuesta = await fetch("/api/guardar-asignacion", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ grupoid, cursoid })
            });

            const resultado = await respuesta.json();

            if (resultado.exito) {
                alert("Asignación guardada correctamente");
                cerrarModalAsignacion();
            } else {
                alert("Error al guardar la asignación");
            }
        } catch (error) {
            console.error("Error guardando la asignación:", error);
        }
    });
});
