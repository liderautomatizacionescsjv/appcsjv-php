<div class="info-detallada">
    <p id="info_nombre"></p>
    <p id="info_codigo"></p>
    <p id="info_documento"></p>
    <p id="info_fechaNacimiento"></p>
    <p id="info_grado"></p>
    <p id="info_correo"></p>
</div>

<div class="reportes">
    <table class="tabla" id="miTabla">
        <thead>
            <tr>
                <th>Estudiante <input type="text" onkeyup="filtrarTabla(0)" placeholder="Buscar"></th>
                <th>Reporte <input type="text" onkeyup="filtrarTabla(1)" placeholder="Buscar"></th>
                <th>Comentario <input type="text" onkeyup="filtrarTabla(2)" placeholder="Buscar"></th>
                <th>Responsable <input type="text" onkeyup="filtrarTabla(3)" placeholder="Buscar"></th>
                <th>Asignatura <input type="text" onkeyup="filtrarTabla(4)" placeholder="Buscar"></th>
                <th>Fecha y Hora <input type="date" onchange="filtrarTabla(5)"></th> <!-- Aquí cambio onkeyup por onchange -->
                <th>Grupo <input type="text" onkeyup="filtrarTabla(6)" placeholder="Buscar"></th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="filas"></tbody>
    </table>
</div>

<script>
    function filtrarTabla(columna) {
        const input = document.querySelectorAll("thead input")[columna];
        let filtro = input.value.trim().toUpperCase();
        const tabla = document.getElementById("miTabla");
        const filas = tabla.getElementsByTagName("tr");

        // Si el filtro es de fecha, formatearlo a YYYY-MM-DD
        if (columna === 5 && filtro) {
            filtro = new Date(filtro).toISOString().split("T")[0]; // Convertir a formato YYYY-MM-DD
        }

        for (let i = 1; i < filas.length; i++) {
            const celdas = filas[i].getElementsByTagName("td");
            if (celdas[columna]) {
                let textoCelda = celdas[columna].textContent || celdas[columna].innerText;

                // Si es la columna de fecha, extraer solo YYYY-MM-DD
                if (columna === 5) {
                    const fechaRegistro = new Date(textoCelda).toISOString().split("T")[0]; // Formatear la fecha del registro
                    filas[i].style.display = fechaRegistro === filtro ? "" : "none";
                } else {
                    filas[i].style.display = textoCelda.toUpperCase().indexOf(filtro) > -1 ? "" : "none";
                }
            }
        }
    }
</script>
