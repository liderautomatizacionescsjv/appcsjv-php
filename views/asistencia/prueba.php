<div class="contenedor">
    <?php if (!empty($estudiantes)) { ?>
        <div class="estudiantes">
            <?php foreach ($estudiantes as $estudiante) { ?>
                <!-- FORMULARIO PARA SELECCIONAR ESTUDIANTE (GET) -->
                <form action="" method="GET" class="ficha-form" onsubmit="guardarFichaSeleccionada(this)">
                    <input type="hidden" name="grupos" value="<?php echo htmlspecialchars($_GET['grupos'] ?? '', ENT_QUOTES); ?>">
                    <input type="hidden" name="codigo_alumno" value="<?php echo $estudiante->codigo; ?>">

                    <button type="submit" class="ficha <?php echo (isset($estudianteSeleccionado) && $estudianteSeleccionado->codigo == $estudiante->codigo) ? 'seleccionado' : ''; ?>">
                        <div class="info-ficha">
                            <p><?php echo $estudiante->nombre; ?></p>
                            <p>Estado:</p>
                        </div>

                        <!-- DIV para evitar que los formularios POST se envíen en GET -->
                        <div class="acciones">
                            <h2 class="no-margin">Acciones</h2>

                            <form method="POST" class="form-post" action="">
                                <input type="hidden" name="id" value="">
                                <input type="hidden" name="tipo" value="falta">
                                <button type="submit" class="boton">Falta</button>
                            </form>

                            <form method="POST" class="form-post" action="">
                                <input type="hidden" name="id" value="">
                                <input type="hidden" name="tipo" value="tarde">
                                <button type="submit" class="boton">Tarde</button>
                            </form>

                            <form method="POST" class="form-post" action="">
                                <input type="hidden" name="id" value="">
                                <input type="hidden" name="tipo" value="excusa">
                                <button type="submit" class="boton">Excusa</button>
                            </form>
                        </div>
                    </button>
                </form>
            <?php } ?>
        </div>
    <?php } else { ?>
        <p>No hay estudiantes en este grupo.</p>
    <?php } ?>
</div>


<main>
    <?php include 'navegacion.php';?>

    <div class="contenedor">
        <?php if (!empty($estudiantes)) { ?>
            <div class="estudiantes">
                <?php foreach ($estudiantes as $estudiante) { ?>
                    <div class="ficha <?php echo (isset($estudianteSeleccionado) && $estudianteSeleccionado->codigo == $estudiante->codigo) ? 'seleccionado' : ''; ?>">
                        <!-- Información del estudiante -->
                         <div class="info-ficha">
                            <p><?php echo $estudiante->nombre; ?></p>
                            <p>Estado:</p>
                         </div>
                        
                      

                        <!-- Sección de acciones -->
                        <div class="acciones">
                            <form method="POST" action="" class="form-post">
                                <input type="hidden" name="codigo_alumno" value="<?php echo $estudiante->codigo; ?>">
                                <input type="hidden" name="reporte" value="Falta">
                                <input type="submit" class="boton" value="Falta">
                            </form>

                            <form method="POST" action="" class="form-post">
                                <input type="hidden" name="codigo_alumno" value="<?php echo $estudiante->codigo; ?>">
                                <input type="hidden" name="reporte" value="Tarde">
                                <input type="submit" class="boton" value="Tarde">
                            </form>

                            <form method="POST" action="" class="form-post">
                                <input type="hidden" name="codigo_alumno" value="<?php echo $estudiante->codigo; ?>">
                                <input type="hidden" name="reporte" value="Excusa">
                                <input type="submit" class="boton" value="Excusa">
                            </form>

                            <form action="" method="GET" class="form-post" onsubmit="guardarFichaSeleccionada(this)">
                            <input type="hidden" name="grupos" value="<?php echo htmlspecialchars($_GET['grupos'] ?? '', ENT_QUOTES); ?>">
                            <input type="hidden" name="codigo_alumno" value="<?php echo $estudiante->codigo; ?>">
                            <button class="boton toggle-info" data-id="<?php echo $estudiante->codigo; ?>">Ver Info</button>
                            
                            </form>

                            
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } else { ?>
            <p>No ha seleccionado el grupo</p>
        <?php } ?>

        <div class="info-estudiante " id="info-estudiante">
            <?php include 'info_estudiante.php' ?>
        </div>
    </div>
</main>


<?php if ($estudianteSeleccionado) { ?>
                <div class="info-detallada">

                    <p><strong>Nombre:</strong> <?php echo $estudianteSeleccionado->nombre; ?></p>
                    <p><strong>Fecha de Nacimiento:</strong> <?php echo $estudianteSeleccionado->fechaNacimiento; ?></p>
                    <p><strong>Documento:</strong> <?php echo $estudianteSeleccionado->documento; ?></p>
                    <p><strong>Código:</strong> <?php echo $estudianteSeleccionado->codigo; ?></p>
                    <p><strong>Grado:</strong> <?php echo $estudianteSeleccionado->grupoid; ?></p>
                </div>
            <?php } else { ?>
                <p>Seleccione un estudiante para ver su información.</p>
            <?php } ?>


            <div class="campo">
                <label for="grupos">Seleccione el grupo</label>
                <select name="grupos" id="grupos" onchange="this.form.submit()" >
                    <option value="">--Seleccione--</option>
                    <option value="1" <?php echo (isset($_GET['grupos']) && $_GET['grupos'] == "1") ? "selected" : ""; ?>>1-A</option>
                    <option value="2-B/M" <?php echo (isset($_GET['grupos']) && $_GET['grupos'] == "2-B/M") ? "selected" : ""; ?>>2-B</option>
                </select>
            </div>


            <tbody>
            <?php if ($estudianteSeleccionado) { ?>
                <tr>
                    <td><?php echo $estudianteSeleccionado->nombre; ?></td>
                    <td>Falta</td>
                    <td>Enfermo</td>
                    <td>Inglés</td>
                    <td>05/1/2025</td>
                    <td><?php echo $estudianteSeleccionado->grupoid; ?></td>
                    <td>Eliminar</td>
                </tr>
            <?php } ?>
        </tbody>
