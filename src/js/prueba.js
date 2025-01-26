const usuarioid = document.getElementById('usuario-id').dataset.id;
    console.log("Usuario ID:", usuarioid);

    const modal = document.getElementById("modalAsignacion");
    const btnAbrir = document.getElementById("asignar-grupos");
    const btnCerrar = document.getElementById("cerrar");
    const form = document.getElementById("formAsignacion");

    // ✅ Abrir el modal y cargar opciones
    btnAbrir.addEventListener("click", function () {
        modal.style.display = "flex";
        cargarOpciones();
    });

    // ✅ Cerrar el modal
    btnCerrar.addEventListener("click", function () {
        cerrarModalAsignacion();
    });

    function cerrarModalAsignacion() {
        modal.style.display = "none";
    }

    // ✅ Evento submit para guardar asignación
    form.addEventListener("submit", async function (event) {
        event.preventDefault(); // Evitar recarga de la página

        const grupoid = document.getElementById("selectGrupo").value;
        const cursoid = document.getElementById("selectAsignatura").value;

        if (!grupoid || !cursoid) {
            mostrarNotificacion("⚠️ Debes seleccionar un grupo y una asignatura.");
            return;
        }

        try {
            const respuesta = await fetch("http://localhost:3002/api/guardar-asignacion", {
                method: 'POST',
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    docenteid: usuarioid,
                    grupoid: grupoid,
                    cursoid: cursoid
                })
            });

            const resultado = await respuesta.json();
            console.log("📥 Respuesta de la API:", resultado);

            if (resultado.exito) {
                mostrarNotificacion("✅ Asignación guardada correctamente");
                cerrarModalAsignacion();
                cargarAsignaciones(); // 🔄 Recargar las asignaciones en la UI
            } else {
                mostrarNotificacion("⚠️ Error al registrar la asignación.");
            }
        } catch (error) {
            console.error("⚠️ Error en fetch:", error);
            mostrarNotificacion("⚠️ Error en la conexión con el servidor.");
        }
    });

    // ✅ Cargar opciones de grupos y asignaturas
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
            console.error("⚠️ Error cargando opciones:", error);
        }
    }