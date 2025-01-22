<?php

namespace Model;

class Asistencia extends ActiveRecord {
    protected static $tabla = 'asistencia';
    protected static $columnasDB = ['id', 'estudiante', 'reporte', 'comentario', 'asignaturaid', 'grupo', 'responsable', 'codigoid', 'fechahora'];
    protected static $primaryKey = 'id';
    public $id;
    public $estudiante;
    public $reporte;
    public $comentario;
    public $asignaturaid;
    public $grupo;
    public $responsable;
    public $codigoid;
    public $fechahora;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->estudiante = $args['estudiante'] ?? '';
        $this->reporte = $args['reporte'] ?? '';
        $this->comentario = $args['comentario'] ?? '';
        $this->asignaturaid = $args['asignaturaid'] ?? '';
        $this->grupo = $args['grupo'] ?? '';
        $this->responsable = $args['responsable'] ?? $_SESSION['nombre'] ?? '';
        $this->codigoid = $args['codigoid'] ?? '';
        $this->fechahora = date('Y-m-d H:i:s'); // Captura la fecha y hora actual
    }

    
}
