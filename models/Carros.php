<?php

namespace Model;

class Carros extends ActiveRecord {
    protected static $tabla = 'carros';
    protected static $columnasDB = ['id', 'nombre', 'totalpc', 'sede', 'capacidad'];
    protected static $primariKey = 'id';

    public $id;
    public $nombre;
    public $totalpc;
    public $sede;
    public $capacidad;


    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? null;
        $this->totalpc = $args['totalpc'] ?? '';
        $this->sede = $args['sede'] ?? '';
        $this->capacidad = $args['capacidad'] ?? '';
       
    }
}