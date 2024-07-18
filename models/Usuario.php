<?php
 
 namespace Model;

 class Usuario extends ActiveRecord {
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'apellido', 'email', 'password', 
                                    'telefono', 'admin', 'confirmado', 'token'];

    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $telefono;
    public $admin;
    public $confirmado;
    public $token;

    public function __construct($args = []) 
    {
        $this -> id = $args['id'] ?? null;
        $this -> nombre = $args['nombre'] ?? '';
        $this -> apellido = $args['apellido'] ?? '';
        $this -> email = $args['email'] ?? '';
        $this -> password = $args['password'] ?? '';
        $this -> telefono = $args['telefono'] ?? '';
        $this -> admin = $args['admin'] ?? 0;
        $this -> confirmado = $args['confirmado'] ?? 0;
        $this -> token = $args['token'] ?? '';
    }

    // Mensajes de validación para la creación de una cuenta
    public function validarNuewaCuenta() {
        if (!$this -> nombre) {
            self::$alertas['error'][] = 'El nombre es Obligatorio';
        }
        if (!$this -> apellido) {
            self::$alertas['error'][] = 'El apellido es Obligatorio';
        }
        if (!$this -> email) {
            self::$alertas['error'][] = 'El correo electrónico es Obligatorio';
        }
        if (!$this -> password) {
            self::$alertas['error'][] = 'La contraseña es Obligatoria';
        }
        if (strlen($this -> password) < 6) {
            self::$alertas['error'][] = 'La contraseña debe contener al menos 6 carácteres';
        }
        if (!$this -> telefono) {
            self::$alertas['error'][] = 'El telefono es Obligatorio';
        }
        
        return self::$alertas;
    }

    public function validarLogin()
    {
        if(!$this -> email) {
            self::$alertas['error'][] = 'El correo electrónico es requerido.';
        }   
        if(!$this -> password) {
            self::$alertas['error'][] = 'La contraseña es requerida.';
        }   

        return self::$alertas;
    }

    public function validarEmail() {
        if(!$this -> email) {
            self::$alertas['error'][] = 'El correo electrónico es requerido.';
        }   

        return self::$alertas;
    }

    public function validarPassword() {
        if(!$this -> password) {
            self::$alertas['error'][] = 'El usuario o contraseña inválida.';
        }   

        if(strlen($this -> password) < 6) {
            self::$alertas['error'][] = 'La contraseña debe contener al menos 6 carácteres.';
        }

        return self::$alertas;
    }

    // Valida si el usuario existe
    public function existeUsuario() {
        $query = "SELECT * FROM " . self::$tabla . " WHERE email = '" . $this -> email . "' LIMIT 1";

        $resultado = self::$db -> query($query);

        if($resultado -> num_rows) {
            self::$alertas['error'][] = 'El usuario ya se encuentra registrado!';
        }

        return $resultado;
    }

    public function hashPassword() {
        $this -> password = password_hash($this -> password, PASSWORD_BCRYPT);
    }

    public function crearToken() {
        $this -> token = uniqid();
    }

    public function comprobarPasswordYVerificado($password) {
        $resultado = password_verify($password, $this->password);

        if(!$resultado || !$this -> confirmado) {
            self::$alertas['error'][] = 'Usuario o contraseña inválida.';
        } else {
            return true;
        }

    }

 }