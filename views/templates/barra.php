<div class="barra">
    <p>Hola: <?php echo $_SESSION['nombre'] ?? '' ;?></p>
    <a class="boton" href="/logout">Cerrar Sessión</a>
</div>

<?php if(isset($_SESSION['admin'])) { ?>

    <div class="barra-servicios">

        <a href="/admin" class="boton">Ver Citas</a>

        <a href="/servicios" class="boton">Ver Servicios</a>
        
        <a href="/servicios/crear" class="boton">Nuevo Servicio</a>

    </div>

<?php } ?>