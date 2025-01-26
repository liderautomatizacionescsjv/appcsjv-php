<h1>Reservas de Computadores</h1>

<div id="notificacion-container" class="notificacion-container"></div>


<div class="container">
        <!-- Selector de sede -->
    
    <div id="carros-container">
        <label for="sede">Seleccione la Sede:</label>
        <select id="sede" name="sede">
            <option value="" selected>--Seleccione--</option>
            <option value="Medellín">Medellín</option>
            <option value="Retiro">Retiro</option>
        </select>
        <div class="carro-title">Lista de Carros</div>
        <div id="carroSelect"></div> <!-- Aquí se agregarán dinámicamente los carros -->
    </div>
    
    <div id="calendar-container">
        <button class="reservar-button" id="abrirModalReservaBtn" onclick="abrirModalReserva()" disabled>Reservar</button>
        <div id='calendar'></div>
    </div>
</div>

<div id="modalReserva" class="modal">
    <div class="modal-content">
        <span class="close" onclick="cerrarModalReserva()">&times;</span>
        <h2>Reservar Carro</h2>
        <form id="formReserva" action="" method="POST">
            <label for="fechaReserva">Fecha de Reserva:</label>
            <input type="date" id="fechaReserva" name="fechaReserva" required>

            <label for="horaInicio">Hora de Inicio:</label>
            <input type="time" id="horaInicio" name="horaInicio" required>

            <label for="horaFin">Hora de Fin:</label>
            <input type="time" id="horaFin" name="horaFin" required>

            <label for="cantidadComputadores">Cantidad de Computadores:</label>
            <input type="number" id="cantidadComputadores" name="cantidadComputadores" required>

            <label for="grupo">Grupo:</label>
            <input type="text" id="grupo" name="grupo" required>

            <p id="disponibilidad"></p>

            <button type="submit" class="btn-3d" id="btn-reservam">Reservar</button>
        </form>
    </div>
</div>

<!-- Modal para mostrar detalles de la reserva -->

<div id="modalDetalleReserva" class="modal">
    <div class="modal-content">
        <span class="close" onclick="cerrarModalDetalleReserva()">&times;</span>
        <h2>Detalle de la Reserva</h2>
        <p><strong>Carro:</strong> <span id="detalleCarro"></span></p>
        <p><strong>Grupo:</strong> <span id="detalleGrupo"></span></p>
        <p><strong>Responsable:</strong> <span id="detalleResponsable"></span></p>
        <p><strong>Hora de Inicio:</strong> <span id="detalleHoraInicio"></span></p>
        <p><strong>Hora de Fin:</strong> <span id="detalleHoraFin"></span></p>
        <p><strong>Cantidad de Computadores Reservados:</strong> <span id="detalleCantidadComputadores"></span></p>
        <!-- <p><strong>Cantidad de Computadores Disponibles:</strong> <span id="detalleComputadoresDisponibles"></span></p> -->
    </div>
</div>


<script src="/build/js/global.min.js"></script>
<script src="/build/js/reservas.min.js"></script>

