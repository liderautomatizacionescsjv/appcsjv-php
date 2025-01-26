<?php

require_once __DIR__ . '/../app/includes/app.php';

use Controllers\ApiController;
use Controllers\ControlAsistencia;
use Controllers\LoginController;
use Controllers\ReservaController;
use MVC\Router;

$router = new Router();

// Iniciar Sesión

$router->get('/', [LoginController::class, 'login']);
$router->post('/', [LoginController::class, 'login']);
$router->get('/logout', [LoginController::class, 'logout']);

// Recuperar Password
$router->get('/olvide', [LoginController::class, 'olvide']);
$router->post('/olvide', [LoginController::class, 'olvide']);
$router->get('/recuperar', [LoginController::class, 'recuperar']);
$router->post('/recuperar', [LoginController::class, 'recuperar']);

// Crear Cuenta
$router->get('/crear-cuenta', [LoginController::class, 'crear']);
$router->post('/crear-cuenta', [LoginController::class, 'crear']);


// Confirmar cuenta
$router->get('/confirmar-cuenta', [LoginController::class, 'confirmar']);
$router->get('/mensaje', [LoginController::class, 'mensaje']);

// Asistencia
$router->get('/docentes', [ControlAsistencia::class, 'docentes']);
$router->post('/docentes', [ControlAsistencia::class, 'docentes']);
$router->get('/reservas', [ReservaController::class, 'index']);
$router->get('/mis-reservas', [ReservaController::class, 'misReservas']);

// API consulta
$router->get('/api/estudiantes',[ApiController::class, 'index']);
$router->get('/api/grupos',[ApiController::class, 'grupos']);
$router->get('/api/reporte',[ApiController::class, 'reportes']);
$router->get('/api/asignaturas',[ApiController::class, 'asignaturas']);
$router->get('/api/asignaciones', [ApiController::class, 'asignacionesDocente']);
$router->get('/api/estado-grupos', [ApiController::class, 'estadoGrupos']);
$router->get('/api/carros', [ApiController::class, 'obtenerCarros']);
$router->get('/api/reservas', [ApiController::class, 'obtenerReservas']);
$router->get('/api/mis-reservas', [ApiController::class, 'obtenerMisReservas']);
$router->get('/api/disponibilidad', [ApiController::class, 'verificarDisponibilidad']);
$router->get('/api/grupos', [ApiController::class, 'obtenerGrupos']);
$router->get('/api/asignaturas', [ApiController::class, 'obtenerAsignaturas']);





// API POST
$router->post('/api/asistencia',[ApiController::class, 'guardar']);
$router->post('/api/grupo-completo', [ApiController::class, 'grupoCompleto']);
$router->post('/api/reservas/crear', [ApiController::class, 'crearReserva']);
$router->post('/api/eliminar-reserva', [ApiController::class, 'eliminarReserva']);
$router->post('/api/actualizar-asistencia', [ApiController::class, 'actualizar']);
$router->post('/api/guardar-asignacion', [ApiController::class, 'guardarAsignacion']);


// API ELIMINAR
$router->post('/api/eliminar-asistencia', [ApiController::class, 'eliminar']);




$router->comprobarRutas();