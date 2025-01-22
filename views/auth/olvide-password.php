

<div class="contenedor_login">
    <div class="login_card">
    <?php include_once __DIR__ . '/../templates/alertas.php'?>

        <h1 class="titulo">Olvidé mi Contraseña</h1>
        <p class="subtitulo">Reestablece tu password escribiendo tu email a continuación</p>

        <form class="formulario" method="POST">
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

            
           

            <input type="submit" class="boton centrado" value="Enviar Instrucciones">
            

        </form>
        <p class="registro">
                Ya tienes una cuenta? <a href="/">Inicia Sesión</a>
        </p>
        <p class="registro">
            ¿Aún no tienes una cuenta? <a href="/crear-cuenta">Crear una</a>
        </p>
    </div>
</div>

