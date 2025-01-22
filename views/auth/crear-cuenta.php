<div class="contenedor_login">
    <div class="login_card">
        <h1 class="titulo">Crear Cuenta</h1>
        <p class="subtitulo">Llena el siguiente formulario para crear tu cuenta</p>
        <?php 
            include_once __DIR__ . '/../templates/alertas.php';


        ?>
        <form action="/crear-cuenta" class="formulario" method="POST">
            <div class="campo">
                <input 
                    class="form-control"
                    type="email"
                    id="email"
                    placeholder="Correo institucional"
                    name="email"
                    required

                >
            </div>

            <div class="campo">
                <input 
                    class="form-control"
                    type="password"
                    id="password"
                    placeholder="Contraseña"
                    name="password"
                    required
                >
            </div>

            <a class="enlace" href="/olvide">¿Olvidó su contraseña?</a>

            <input class="boton centrado" type="submit" value="Crear">
        </form>

        <p class="registro">¿Ya tienes una cuenta?
        <a href="/"> Inicia Sesión</a>

        </p>
    </div>
</div>