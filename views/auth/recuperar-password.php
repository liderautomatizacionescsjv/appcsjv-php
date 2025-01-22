
<div class="contenedor_login">
    <div class="login_card">
        <h1 class="titulo">Recuperar Contraseña</h1>
        <p class="subtitulo">Coloca tu nueva contraseña a continuación</p>
        <?php 
            include_once __DIR__ . '/../templates/alertas.php';
        ?>


        <?php if($error) return; ?>
        <form  class="formulario" method="POST">

            <div class="campo">
                <input 
                    class="form-control"
                    type="password"
                    id="password"
                    placeholder="Tu nueva contraseña"
                    name="password"
                    required
                >
            </div>


            <input class="boton centrado" type="submit" value="Guardar Nueva Contraseña">
        </form>

        <p class="registro">
                Ya tienes una cuenta? <a href="/">Inicia Sesión</a>
        </p>
        <p class="registro">
            ¿Aún no tienes una cuenta? <a href="/crear-cuenta">Crear una</a>
        </p>

        </p>
    </div>
</div>