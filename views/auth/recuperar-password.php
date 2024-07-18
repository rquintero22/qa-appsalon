<h1 class="nombre-pagina">Recuperar contraseña</h1>
<p class="descripcion-pagina">Coloca tu nueva contraseña a continuación</p>

<?php 

    include_once __DIR__ . "/../templates/alertas.php"; 

    if($error) return;

?>

<form method="POST" class="formulario">

    <div class="campo">

        <label for="password">Contraseña</label>
        <input type="password" name="password" id="password" placeholder="Digita tu contraseña">

    </div>

    <input type="submit" value="Guardar contraseña" class="boton">

</form>

<div class="acciones">

    <a href="/">¿Ya tienes una cuenta? Iniciar Sesión</a>
    <a href="/crear-cuenta">¿Aún no tienes una cuenta? Obtener una</a>

</div>