<?php 

    namespace Model;

    class Servicio extends ActiveRecord {
        public static $tabla = 'servicios';
        public static $columnasDB = ['id', 'nombre', 'precio'];

        public $id;
        public $nombre;
        public $precio;

        public function __construct($args = [])
        {
            $this -> id = $args['id'] ?? null;
            $this -> nombre = $args['nombre'] ?? '';
            $this -> precio = $args['precio'] ?? 0;
        }

        public function validar() {
            if(!$this -> nombre) {
                self::$alertas['error'][] = 'El nombre del servicio es requerido'; 
            }

            if(!$this -> precio) {
                self::$alertas['error'][] = 'El precio del servicio es requerido'; 
            }
            if(!is_numeric($this -> precio)) {
                self::$alertas['error'][] = 'El precio no es válido'; 
            }

            return self::$alertas;
        }
    }