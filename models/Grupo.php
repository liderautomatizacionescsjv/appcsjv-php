<?php


namespace Model;

class Grupo extends ActiveRecord {
    protected static $tabla = 'grupos';
    protected static $columnasDB = ['idgrupo', 'nombre', 'completo', 'fecha', 'responsable'];
    protected static $primaryKey = 'idgrupo'; // Define la clave primaria

    public $idgrupo;
    public $nombre;
    public $completo;
    public $fecha;
    public $responsable;

    public function __construct($args = []) {
        $this->idgrupo = $args['idgrupo'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->completo = $args['completo'] ?? '';
        $this->fecha = $args['fecha'] ?? '';
        $this->responsable = $args['responsable'] ?? '';
    }

    public static function actualizarGrupoCompleto($grupoId, $completo, $fecha, $responsable) {
        if (!$grupoId || !$completo || !$fecha || !$responsable) {
            return ["error" => "Datos insuficientes"];
        }

        $grupo = self::find($grupoId); // Buscar usando la clave primaria dinámica
        if (!$grupo) {
            return ["error" => "Grupo no encontrado"];
        }

        $grupo->completo = $completo;
        $grupo->fecha = $fecha;
        $grupo->responsable = $responsable;

        $resultado = $grupo->guardar();
        return ["exito" => $resultado];
    }

    public static function obtenerEstadoGrupos() {
        return self::all();
    }
}
