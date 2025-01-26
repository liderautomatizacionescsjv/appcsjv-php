<div class="contenedor_login">
    <div class="login_card">
        <h1 class="titulo">APLICATIVO ASISTENCIA</h1>
        <p class="subtitulo">Conéctate con tu correo institucional y contraseña.</p>
        <?php include_once __DIR__ . '/../templates/alertas.php'?>

        <form action="/" class="formulario" method="POST">
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

            <input class="boton centrado" type="submit" value="Ingresar">
        </form>

        <p class="registro">
            ¿Aún no estás inscrito? <a href="/crear-cuenta">Regístrate aquí</a>
        </p>
    </div>
</div>