
<fieldset class="navegacion">
    <legend>Selectores</legend>
    
    <div class="campo">
        <label for="grupos">Seleccione el grupo</label>
        <select name="grupos" id="grupos"  >
            <!-- <option value="">--Seleccione--</option>
            <option value="1" >1-A</option>
            <option value="2">2-B</option> -->
        </select>
    </div>

    

    <div class="campo">
        <label for="asignatura">Seleccione la asignatura</label>
        <select name="asignatura" id="asignatura" require>
            <!-- <option value="">--Seleccione--</option>
            <option value="Sociales">Sociales</option> -->
        </select>
    </div>

    <div class="campo">
        <label for="buscador">Buscar</label>
        <input type="text" placeholder="Buscar..." id="buscador" name="buscador">
    </div>

    <div class="botones-container">
        <button id="grupo-completo" class="boton centrado">Grupo Completo</button>
        <button id="ver-todos" class="boton centrado ajustado">Ver todos los registros</button>
        <button id="asignar-grupos" class="boton centrado ajustado">Asignar Grupos</button> <!-- Nuevo botón -->
    </div>



</fieldset>
