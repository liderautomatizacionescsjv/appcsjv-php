<?php 
    

    if(!isset($_SESSION)){
        session_start();
    }
    
    $auth = $_SESSION['login'] ?? false;


    if(!isset($inicio)){
        $inicio = false;
    }


?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplicativo Asistencia</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/es.js'></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link href="https://fonts.googleapis.com/css2?family=Krub:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;1,200;1,300;1,400;1,500;1,600;1,700&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="build/css/app.css">
   


</head>
<body>
    <p id="urlreal" style="display: none;" data-url="<?php echo ($_ENV['APP_URL'])?>">
        

    </p>
    <?php if(isset($_SESSION['nombre'])): ?>
        <p id="usuario-actual" data-nombre="<?php echo $_SESSION['nombre']; ?>" style="display: none;"></p>
        <p id="usuario-id" data-id="<?php echo $_SESSION['usuarioid']; ?>" style="display: none;"></p>
    <?php endif; ?>

    <div class="contenedor-app">
    <header class="header">
        <div class="encabezado <?php echo $auth ? 'encabezado-sesion' : 'centrado'; ?>">
        <?php if($auth): ?>
             <!-- Menú de hamburguesa -->
             <div class="menu-barras">
                <button class="menu-icon" aria-label="Abrir menú">
                    ☰
                </button>
                <!-- Fondo opaco -->
                <div class="menu-overlay"></div>
                <nav class="menu-deslizante">
                    <button class="menu-cerrar" aria-label="Cerrar menú">✖</button>
                    <a href="/docentes" class="<?php echo ($_SERVER['REQUEST_URI'] === '/docentes') ? 'activo' : ''; ?>">Docentes</a>
                    <a href="/reservas" class="<?php echo ($_SERVER['REQUEST_URI'] === '/reservas') ? 'activo' : ''; ?>">Reservas</a>
                    <a href="/mis-reservas" class="<?php echo ($_SERVER['REQUEST_URI'] === '/mis-reservas') ? 'activo' : ''; ?>">Mis Reservas</a>
                    <a href="/logout">Cerrar Sesión</a>
                </nav>
            </div>
        <?php endif; ?>
            <img class="logo" src="build/img/logo.png" alt="logo" loading="lazy">
            <h1 class="titulos">
                Colegio San José de las Vegas
            </h1>
            <?php if($auth): ?>
                <div class="session">
                    <p>HOLA <?php echo htmlspecialchars($_SESSION['nombre']); ?></p>
                    <a href="/logout">Cerrar Sesión</a>
                </div>
            <?php endif; ?>
        </div>
    </header>

        <!-- Imagen de fondo independiente cuando no hay sesión -->
        <?php if(!$auth): ?>
            <div class="imagen-fondo"></div>
        <?php endif; ?>


        <div class="app">
            <?php echo $contenido; ?>
        </div>

    </div>

            
</body>
</html>