<?php
    namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

    class LoginController {
        public static function login(Router $router) {
            $alertas = [];

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $auth = new Usuario($_POST);

                $alertas = $auth -> validarLogin();

                if(empty($alertas)) {
                    // Comprobar el usuario
                    $usuario = Usuario::where('email', $auth->email);

                    if($usuario) {
                        if ($usuario -> comprobarPasswordYVerificado($auth -> password)) {
                            isSession();

                            $_SESSION['id'] = $usuario -> id;
                            $_SESSION['nombre'] = $usuario -> nombre . ' ' . $usuario -> apellido;
                            $_SESSION['email'] = $usuario -> email;
                            $_SESSION['login'] = true;
                            
                            // Redireccionamiento
                            if($usuario -> admin === "1" ) {
                                $_SESSION['admin'] = $usuario -> admin ?? null;

                                header('Location: /admin');
                            } else {
                                header('Location: /cita');
                            }
                        }
                    } else {
                        Usuario::setAlerta('error', 'Usuario o contraseña inválida');
                    }
                }
                
            }

            $alertas = Usuario::getAlertas();

            $router -> render('auth/login', [
                'alertas' => $alertas
            ]);
        }

        public static function logout() {
            isSession();
            $_SESSION = [];

            header('Location: /');
        }
        public static function olvide(Router $router) {
            $alertas = [];

            if($_SERVER['REQUEST_METHOD'] === 'POST') {
                $auth = new Usuario($_POST);

                $alertas = $auth -> validarEmail();

                if(empty($alertas)) {
                    $usuario = Usuario::where('email', $auth -> email);

                    if($usuario && $usuario -> confirmado === "1") {
                        // Generar token
                        $usuario -> crearToken();
                        $usuario -> guardar();

                        $email = new Email($usuario -> email, $usuario -> nombre, $usuario -> token);

                        $email -> enviarInstrucciones();

                        Usuario::setAlerta('exito', 'Revisa tu correo electrónico');
             
                    } else {
                        Usuario::setAlerta('error', 'El usuario es inválido.');
                        
                    }
                }
            }

            $alertas = Usuario::getAlertas();

            $router -> render('auth/olvide-password', [
                'alertas' => $alertas
            ]);
        }
        public static function recuperar(Router $router) {
            $alertas = [];
            $error = false;

            $token = s($_GET['token']);

            // Buscar usuario
            $usuario = Usuario::where('token', $token);

            if(empty($usuario)) {
                Usuario::setAlerta('error', 'Usuario inválido');
                $error = true;
            }

            if($_SERVER['REQUEST_METHOD'] === 'POST') {
                $password = new Usuario($_POST);

                $alertas = $password -> validarPassword();

                if(empty($alertas)) {
                    $usuario -> password = null;
                    $usuario -> password = $password -> password;
                    $usuario -> hashPassword();
                    $usuario -> token = null;
                    
                    $resultado = $usuario -> guardar();

                    if($resultado) {
                        header('Location: /');
                    }
                }
            }

            $alertas = Usuario::getAlertas();

            $router -> render('auth/recuperar-password', [
                'alertas' => $alertas,
                'error' => $error
            ]);
        }
        public static function crear(Router $router) {

            $usuario = new Usuario($_POST);

            // Alertas vacías
            $alertas = [];

            if($_SERVER['REQUEST_METHOD'] === 'POST') {
                $usuario -> validar($_POST);
                $alertas = $usuario -> validarNuewaCuenta();

                // Revisar que alerta esté vacío
                if(empty($alertas)) {
                    // Verificar que el usuario no esté registrado
                    $resultado = $usuario -> existeUsuario();
                    if ($resultado -> num_rows) {
                        $alertas = Usuario::getAlertas();
                    } else {
                        //  Hash password
                        $usuario -> hashPassword();

                        // Generar token
                        $usuario -> crearToken();

                        // Enviar email
                        $email = new Email($usuario -> email, $usuario -> nombre, $usuario -> token);
                     
                        $email -> enviarConfirmacion();

                        // Crear el usuario
                        $resultado = $usuario -> guardar();

                        if($resultado) {
                            header('Location: /mensaje');
                        }
                    }
                }

            }

            $router -> render('auth/crear-cuenta', [
                'usuario' => $usuario,
                'alertas' => $alertas
            ]);
        }

        public static function mensaje(Router $router) {
            $router -> render('auth/mensaje', [

            ]);
        }

        public static function confirmar(Router $router) {

            $alertas = [];

            $token = s($_GET['token']);

            $usuario = Usuario::where('token', $token);

            if(empty($usuario)) {
                // Mostrar mensaje de error
                Usuario::setAlerta('error', 'Token inválido.');
            } else {
                // Modificar usuario a confirmado
                $usuario -> confirmado = "1";

                $usuario -> token = null;
                
                $usuario -> guardar();

                Usuario::setAlerta('exito', 'Cuenta confirmada correctamente!');
            }

            $alertas = Usuario::getAlertas();

            $router -> render('auth/confirmar-cuenta',  [
                'alertas' => $alertas
            ]);
        }

        
    }