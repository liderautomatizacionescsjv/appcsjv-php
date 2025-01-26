<?php

namespace Model;

class Reserva extends ActiveRecord {
    protected static $tabla = 'reservas';
    protected static $columnasDB = ['id', 'carro', 'grupo', 'responsable', 'horaInicio', 'horaFin', 'cantidadComputadores', 'fecha'];
    protected static $primaryKey = 'id';

    public $id;
    public $carro;
    public $grupo;
    public $responsable;
    public $horaInicio;
    public $horaFin;
    public $cantidadComputadores;
    public $fecha;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->carro = $args['carro'] ?? null;
        $this->grupo = $args['grupo'] ?? '';
        $this->responsable = $args['responsable'] ?? '';
        $this->horaInicio = $args['horaInicio'] ?? '';
        $this->horaFin = $args['horaFin'] ?? '';
        $this->cantidadComputadores = $args['cantidadComputadores'] ?? 0;
        $this->fecha = $args['fecha'] ?? null;
    }

    // Validación de reserva
    public function validarReserva() {
        if (!$this->carro) {
            self::$alertas['error'][] = 'El carro es obligatorio';
        }
        if (!$this->horaInicio || !$this->horaFin) {
            self::$alertas['error'][] = 'Debe seleccionar una hora de inicio y fin';
        }
        if ($this->cantidadComputadores <= 0) {
            self::$alertas['error'][] = 'Debe reservar al menos un computador';
        }
        return self::$alertas;
    }

    public static function obtenerReservasConCarro($carroId = null) {
        $reserva = new self(); // Crear una instancia de Reserva
    
        $joins = [
            [
                "tabla" => "carros",
                "columnaFK" => "carro",
                "columnaPK" => "id",
                "columnas" => ["nombre_carro" => "nombre"] // Extrae el nombre del carro con alias
            ]
        ];
    
        // Definir la condición WHERE
        $whereCondition = $carroId ? "reservas.carro = '" . self::$db->escape_string($carroId) . "'" : "";
    
        // Llamar a la nueva función con el filtro
        return $reserva->joinWithCondition($joins, $whereCondition);
    }
    
    public static function obtenerDisponibilidad($carroId, $fecha, $horaInicio, $horaFin) {
        $query = "SELECT 
                    c.totalpc - COALESCE(SUM(r.cantidadComputadores), 0) AS computadores_disponibles
                  FROM carros c
                  LEFT JOIN reservas r ON c.id = r.carro 
                  AND r.fecha = ?
                  AND (r.horaInicio < ? AND r.horaFin > ?)
                  WHERE c.id = ?
                  GROUP BY c.totalpc";
    
        $stmt = self::$db->prepare($query);
        $stmt->bind_param('sssi', $fecha, $horaFin, $horaInicio, $carroId);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
    
        return [
            "computadoresDisponibles" => $resultado['computadores_disponibles'] ?? 0,
            "estado" => ($resultado['computadores_disponibles'] ?? 0) > 0 ? 'Disponible' : 'No Disponible'
        ];
    }


    public static function obtenerReservasUsuario($responsable) {
        $joins = [
            [
                "tabla" => "carros",
                "columnaFK" => "carro",
                "columnaPK" => "id",
                "columnas" => ["nombre_carro" => "nombre"]
            ]
        ];
    
        // Condición para filtrar por el usuario autenticado
        $whereCondition = "reservas.responsable = '" . self::$db->escape_string($responsable) . "'";
    
        // Llamamos al método de ActiveRecord
        return (new self())->joinWithCondition($joins, $whereCondition);
    }
    
    
    
    
    
    
}
