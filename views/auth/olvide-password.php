<h1 class="nombre-pagina">Olvidé la contraseña</h1>
<p class="descripcion-pagina">Restablece tu contraseña escribiendo tu correo electrónico a continuación</p>

<?php include_once __DIR__ . "/../templates/alertas.php"; ?>

<form class="formulario" action="/olvide" method="POST">

    <div class="campo">

        <label for="email">Correo Electrónico</label>
        <input type="email" id="email" name="email" placeholder="Digitu su correo electrónico">

    </div>

    <input type="submit" value="Enviar Instrucciones" class="boton">

</form>

<div class="acciones">

    <a href="/">¿Ya tienes una cuenta? Inicia Sesión</a>
    <a href="/crear-cuenta">¿Aún no tienes una cuenta? Crear una </a>
    
</div>