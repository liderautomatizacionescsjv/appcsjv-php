<?php

namespace Model;

class Asignacion extends ActiveRecord{
    protected static $tabla = 'asignacion_docente';
    protected static $columnasDB = ['id', 'docenteid', 'grupoid', 'cursoid', 'nombre_grupo', 'nombre_asignatura'];

    public $id;
    public $docenteid;
    public $grupoid;
    public $cursoid;
    public $nombre_grupo;
    public $nombre_asignatura;

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->docenteid = $args['docenteid'] ?? null;
        $this->grupoid = $args['grupoid'] ?? null;
        $this->cursoid = $args['cursoid'] ?? null;
        $this->nombre_grupo = $args['nombre_grupo'] ?? null;
        $this->nombre_asignatura = $args['nombre_asignatura'] ?? null;
    }

    public static function obtenerAsignacionesDocente($idPersonal) {
        if (!$idPersonal) {
            return [];
        }
    
        // DEBUG: Verificar si el ID está llegando correctamente
        error_log("ID del Docente recibido: " . $idPersonal);
    
        $query = "SELECT 
                    asignacion_docente.id,
                    asignacion_docente.docenteid,
                    grupos.idgrupo AS grupoid,
                    grupos.nombre AS nombre_grupo,  -- 🟢 Incluir el nombre del grupo
                    asignaturas.idasignatura AS cursoid,
                    asignaturas.nombre AS nombre_asignatura  -- 🟢 Incluir el nombre de la asignatura
                  FROM asignacion_docente
                  JOIN grupos ON asignacion_docente.grupoid = grupos.idgrupo
                  JOIN asignaturas ON asignacion_docente.cursoid = asignaturas.idasignatura
                  WHERE asignacion_docente.docenteid = " . self::$db->escape_string($idPersonal);
    
        error_log("Consulta SQL ejecutada: " . $query); // Depuración del query
    
        return self::consultarSQL($query);
    }
    
    
    
}