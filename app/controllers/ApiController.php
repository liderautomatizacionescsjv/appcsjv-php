<?php

namespace Controllers;

use Model\Asignacion;
use Model\Asignaturas;
use Model\Asistencia;
use Model\Carros;
use Model\Estudiante;
use Model\Grupo;
use Model\Reserva;

class ApiController{

    public static function comprobarSesion() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    
        header("Content-Type: application/json");
        echo json_encode([
            "session_id" => session_id(),
            "session_data" => $_SESSION
        ]);
        exit;
    }
    

    public static function index(){
        
        // $estudiantes = Estudiante::all();

        // echo json_encode($estudiantes);
        $joins = [
            [
                "tabla" => "grupos",
                "columnaFK" => "grupoid",
                "columnaPK" => "idgrupo",
                "columnas" => ["nombre_grupo" => "nombre"]
            ]
        ];
    
        $estudiantes = Estudiante::join($joins);
        
        echo json_encode($estudiantes);
    }

    // GRUPOS-------------

    public static function grupoCompleto() {
    
        $grupoId = $_POST['grupo'] ?? null;
        $completo = $_POST['completo'] ?? null;
        $fecha = $_POST['fecha'] ?? null;
        $responsable = $_SESSION['nombre'] ?? null;



        $resultado = Grupo::actualizarGrupoCompleto($grupoId, $completo, $fecha, $responsable);
        echo json_encode($resultado);
    }

    
    public static function estadoGrupos() {
        $grupos = Grupo::obtenerEstadoGrupos();
        echo json_encode($grupos);
    }


    public static function obtenerGrupos() {
        $grupos = Grupo::all();
        echo json_encode($grupos);
    }
    
    public static function obtenerAsignaturas() {
        $asignaturas = Asignaturas::all();
        echo json_encode($asignaturas);
    }


    public static function asignacionesDocente() {

        $idPersonal = $_SESSION['usuarioid'] ?? null;
        

        if (!$idPersonal) {
            echo json_encode(["error" => "Usuario no autenticado"]);
            return;
        }
    
        // Obtener las asignaciones desde el modelo
        $asignaciones = Asignacion::obtenerAsignacionesDocente($idPersonal);


    
        echo json_encode($asignaciones);

    }
    
    public static function guardarAsignacion() {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    
        header("Content-Type: application/json");
    
        // Obtener los datos de la solicitud POST
        $datos = json_decode(file_get_contents("php://input"), true);
    
        if (!$datos) {
            echo json_encode(["error" => "No se recibieron datos correctamente"]);
            error_log("Error al decodificar los datos JSON: " . json_last_error_msg()); 
            return;
        }
    
        error_log("Datos recibidos: " . print_r($datos, true));
    
        $grupoid = $datos['grupoid'] ?? null;
        $cursoid = $datos['cursoid'] ?? null;
        $docenteid = $datos['docenteid'] ?? null;
        $nombreGrupo = $datos['nombre_grupo'] ?? null;
        $nombreAsignatura = $datos['nombre_asignatura'] ?? null;
    
        if (!$grupoid || !$cursoid || !$nombreGrupo || !$nombreAsignatura) {
            echo json_encode(["error" => "Datos insuficientes"]);
            return;
        }
    
        error_log("Datos recibidos: Grupo - $grupoid ($nombreGrupo), Curso - $cursoid ($nombreAsignatura), Docente - $docenteid");
    
        $asignacion = new Asignacion([
            "docenteid" => $docenteid,
            "grupoid" => $grupoid,
            "cursoid" => $cursoid,
            "nombre_grupo" => $nombreGrupo,
            "nombre_asignatura" => $nombreAsignatura
        ]);
    
        $resultado = $asignacion->guardar();
    
        if ($resultado) {
            echo json_encode(["exito" => "Asignación guardada correctamente"]);
        } else {
            error_log("Error al guardar en la base de datos");
            echo json_encode(["error" => "Error al guardar en la base de datos"]);
        }
    }
    
    
    
    


    // CARROS ---------------------

    
    public static function obtenerCarros() {
        $sede = $_GET['sede'] ?? '';

        if (!$sede) {
            echo json_encode(["error" => "Sede no especificada"]);
            return;
        }

        $carros = Carros::where_multiple('sede', $sede);

        // 🔍 Verificar si la consulta devuelve resultados
        if (!$carros || empty($carros)) {
            echo json_encode([]);
            return;
        }

        echo json_encode($carros, JSON_UNESCAPED_UNICODE);
    }

    

    


    public static function reportes(){
        $asistencia = new Asistencia();
        
        $joins = [
            [
                "tabla" => "asignaturas",
                "columnaFK" => "asignaturaid",
                "columnaPK" => "idasignatura",
                "columnas" => ["nombre_asignatura" => "nombre"]
            ],
            [
                "tabla" => "grupos",
                "columnaFK" => "grupo",
                "columnaPK" => "idgrupo",
                "columnas" => ["nombre_grupo" => "nombre"]
            ]
        ];
    
        $resultado = $asistencia->join($joins);
        echo json_encode($resultado);
    }
    


    public static function guardar(){

        $asistencia = new Asistencia($_POST);

        $resultado = $asistencia->guardar();
       
        echo json_encode($resultado);

    }

    public static function eliminar() {
        $id = $_POST['id'] ?? null;
    
        if (!$id) {
            echo json_encode(["error" => "ID no proporcionado"]);
            return;
        }
    
        // Crear una instancia del modelo y asignarle el ID
        $registro = new Asistencia();
        $registro->id = $id;
    
        // Llamar al método eliminar()
        $resultado = $registro->eliminar();
    
        // Devolver respuesta en formato JSON
        echo json_encode([
            "resultado" => $resultado,
            "mensaje" => $resultado ? "Registro eliminado correctamente" : "Error al eliminar"
        ]);
    }

    public static function actualizar() {
        $id = $_POST['id'] ?? null;
        $reporte = $_POST['reporte'] ?? null;
        $comentario = $_POST['comentario'] ?? null;
    
        if (!$id || !$reporte) {
            echo json_encode(["error" => "Datos insuficientes"]);
            return;
        }
    
        // Buscar el registro en la base de datos
        $registro = Asistencia::find($id);
    
        if (!$registro) {
            echo json_encode(["error" => "Registro no encontrado"]);
            return;
        }
    
        // Verificar que el usuario autenticado es el responsable del reporte
        if ($registro->responsable !== $_SESSION['nombre']) {
            echo json_encode(["error" => "No autorizado"]);
            return;
        }
    
        // Actualizar el reporte y comentario
        $registro->reporte = $reporte;
        $registro->comentario = $comentario;
    
        // Guardar en la base de datos
        $resultado = $registro->guardar();
    
        echo json_encode(["exito" => $resultado]);
    }
    

    // CREAR RESERVA
    public static function crearReserva() {
        // Verificar si el usuario está autenticado
        if (!isset($_SESSION['nombre'])) {
            echo json_encode(["error" => "Usuario no autenticado"]);
            return;
        }
    
        // Obtener los datos del POST
        $datos = json_decode(file_get_contents("php://input"), true);
    
        if (!$datos) {
            echo json_encode(["error" => "No se recibieron datos"]);
            return;
        }
    
        // Agregar el nombre del usuario en sesión como responsable
        $datos['responsable'] = $_SESSION['nombre'];
    
        // Crear una nueva instancia del modelo con los datos recibidos
        $reserva = new Reserva($datos);
    
        // Validar la reserva
        $alertas = $reserva->validarReserva();
    
        if (!empty($alertas)) {
            echo json_encode(["error" => "Error en la validación", "alertas" => $alertas]);
            return;
        }
    
        // Guardar la reserva en la base de datos
        $resultado = $reserva->guardar();
    
        if ($resultado) {
            echo json_encode(["status" => "success", "message" => "Reserva creada con éxito", "reserva_id" => $reserva->id]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error al guardar la reserva"]);
        }
    }

    // Obtener reservas

    public static function obtenerReservas() {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json");
    
        $carroId = $_GET['carro_id'] ?? '';
    
        if (!$carroId) {
            echo json_encode(["error" => "Carro no especificado"]);
            return;
        }
    
        // Obtener las reservas del carro seleccionado
        $reservas = Reserva::obtenerReservasConCarro($carroId);
    
        // Si no hay reservas, devolver un array vacío
        if (!$reservas || empty($reservas)) {
            echo json_encode([]);
            return;
        }
    
        // Formatear las reservas para FullCalendar
       // Formatear las reservas para FullCalendar
        $eventos = [];
        foreach ($reservas as $reserva) {
            $fecha = $reserva['fecha'] ?? '1970-01-01'; // Asegurar que la fecha está presente
            $horaInicio = $reserva['horaInicio'] ?? '00:00:00';
            $horaFin = $reserva['horaFin'] ?? '00:00:00';
        
            $eventos[] = [
                "title" => "Grupo " . $reserva['grupo'], 
                "start" => date("Y-m-d\TH:i:s", strtotime("$fecha $horaInicio")), // Combinar fecha con hora de inicio
                "end" => date("Y-m-d\TH:i:s", strtotime("$fecha $horaFin")), // Combinar fecha con hora de fin
                "extendedProps" => [
                    "carro" => $reserva['carro'] ?? 'Desconocido',
                    "grupo" => $reserva['grupo'] ?? 'Sin grupo',
                    "responsable" => $reserva['responsable'] ?? 'No asignado',
                    "cantidadComputadores" => $reserva['cantidadComputadores'] ?? 0,
                    "nombre_carro" => $reserva['nombre_carro'] ?? 'Sin carro',
                    "fecha" => $fecha // 📌 Se envía la fecha correcta
                ]
            ];
        }
        
        
        // debuguear($eventos);
    
        echo json_encode($eventos, JSON_UNESCAPED_UNICODE);
    }


    // Obtener mis reservas-------------------------------------------

    public static function obtenerMisReservas() {
        // Verificar si el usuario está autenticado
        if (!isset($_SESSION['nombre'])) {
            echo json_encode(["error" => "Usuario no autenticado"]);
            return;
        }
    
        // Obtener el nombre del usuario autenticado
        $responsable = $_SESSION['nombre'];
    
        // Obtener reservas del usuario autenticado
        $misReservas = Reserva::obtenerReservasUsuario($responsable);
    
        echo json_encode($misReservas);
    }
    

    // Eliminar la reserva:
    public static function eliminarReserva() {
    // Verificar si el usuario está autenticado
    if (!isset($_SESSION['nombre'])) {
        echo json_encode(["error" => "Usuario no autenticado"]);
        return;
    }

    // Obtener el ID de la reserva
    $id = $_POST['id'] ?? null;
    if (!$id) {
        echo json_encode(["error" => "ID de reserva no proporcionado"]);
        return;
    }

    // Buscar la reserva
    $reserva = Reserva::find($id);
    if (!$reserva) {
        echo json_encode(["error" => "Reserva no encontrada"]);
        return;
    }

    // Verificar que el usuario autenticado sea el creador de la reserva
    if ($reserva->responsable !== $_SESSION['nombre']) {
        echo json_encode(["error" => "No autorizado para eliminar esta reserva"]);
        return;
    }

    // Eliminar la reserva
    $resultado = $reserva->eliminar();

    echo json_encode([
        "resultado" => $resultado,
        "mensaje" => $resultado ? "Reserva eliminada correctamente" : "Error al eliminar la reserva"
    ]);
}

    
    
    public static function verificarDisponibilidad() {
        header("Content-Type: application/json");
    
        $carroId = $_GET['carro_id'] ?? null;
        $fecha = $_GET['fecha'] ?? null;
        $horaInicio = $_GET['horaInicio'] ?? null;
        $horaFin = $_GET['horaFin'] ?? null;
    
        if (!$carroId || !$fecha || !$horaInicio || !$horaFin) {
            echo json_encode(["error" => "Faltan parámetros"]);
            return;
        }
    
        $disponibilidad = Reserva::obtenerDisponibilidad($carroId, $fecha, $horaInicio, $horaFin);
        echo json_encode($disponibilidad);
    }
    
    
    
    
}