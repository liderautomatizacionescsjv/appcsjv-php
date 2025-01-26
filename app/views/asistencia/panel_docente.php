<main>
    <?php include 'navegacion.php';?>
    
    <div id="notificacion-container" class="notificacion-container"></div>

    <!-- Modal para agregar comentario -->
    <div id="modalComentario" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModalComentario()">&times;</span>
            <h2>Agregar Comentario</h2>
            <form id="formComentario">
                <p><strong>Estudiante:</strong> <span id="comentarioEstudiante"></span></p>
                <p><strong>Reporte:</strong> <span id="comentarioReporte"></span></p>

                <label for="comentario">Comentario:</label>
                <textarea id="comentario" name="comentario" rows="4" placeholder="Escribe un comentario..."></textarea>

                <button type="submit" class="btn-3d">Guardar Comentario</button>
            </form>
        </div>
    </div>

    <!-- Modal para asignar grupos -->
    <div id="modalAsignacion" class="modal">
        <div class="modal-content">
            <span class="close" id="cerrar" onclick="cerrarModalAsignacion()">&times;</span>
            <h2>Asignar Grupos ó Asignaturas</h2>

            <form id="formAsignacion">
                <h4>Seleccione la asignatura o grupo que desea asginar:</h4>
                <div class="campo">
                    <label for="selectGrupo">Seleccionar Grupo</label>
                    <select id="selectGrupo" name="grupoid" >
                        <option value="">-- Seleccionar --</option>
                    </select>
                </div>

                <div class="campo">
                    <label for="selectAsignatura">Seleccionar Asignatura</label>
                    <select id="selectAsignatura" name="cursoid">
                        <option value="">-- Seleccionar --</option>
                    </select>
                </div>

                <button type="submit" class="btn-3d">Guardar Asignación</button>
            </form>
        </div>
    </div>



    <div class="contenedor" id="contenedor">
        <div class="fichas" id="fichas">
          
        </div>


        <div class="info-estudiante " id="info-estudiante">
            <?php include 'info_estudiante.php' ?>
        </div>
    </div>
    <script src="/build/js/global.min.js"></script>
<script src="/build/js/docentes.min.js"></script>




</main>
