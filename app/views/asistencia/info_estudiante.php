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
    const filas = document.querySelectorAll("#miTabla tbody tr");

    filas.forEach(fila => {
        const celdas = fila.getElementsByTagName("td");

        if (celdas[columna]) {
            let textoCelda = "";

            // 🔥 Si la celda contiene un <select>, obtener la opción seleccionada
            const select = celdas[columna].querySelector("select");
            if (select) {
                textoCelda = select.options[select.selectedIndex].text.toUpperCase();
            } else {
                textoCelda = celdas[columna].textContent.trim().toUpperCase();
            }

            // 🔥 Si estamos filtrando la fecha (columna 5)
            if (columna === 5) {
                const fechaTexto = celdas[columna].textContent.trim();
                if (fechaTexto) {
                    const fechaRegistro = new Date(fechaTexto).toISOString().split("T")[0];
                    fila.style.display = fechaRegistro === filtro ? "" : "none";
                    return;
                }
            }

            // 🔥 Aplicar filtro: Si el texto contiene el valor ingresado, mostrar la fila
            fila.style.display = textoCelda.includes(filtro) ? "" : "none";
        }
    });
}




</script>

