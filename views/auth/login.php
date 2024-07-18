<h1 class="nombre-pagina">Login</h1>
<p class="descripcion-pagina">Inicia sesión con tus datos</p>

<?php include_once __DIR__ . "/../templates/alertas.php"; ?>

<form method="POST" action="/" class="formulario">

    <div class="campo">

        <label for="email">Email</label>
        <input type="email" id="email" name="email" 
               placeholder="Digita tu email"
               >

    </div>

    <div class="campo">

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Digita tu password">

    </div>

    <input type="submit" class="boton" value="Iniciar Sesión">

</form>

<div class="acciones">

    <a href="/crear-cuenta">¿Aún no tienes una cuenta? Crear una </a>
    <a href="/olvide">¿Olvidates tu contraseña? </a>

</div>