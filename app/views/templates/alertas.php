<?php
    // 2 Foreach por que el primero se mueve por el ["error"] y el segundo sobre el mensaje
    foreach($alertas as $key =>$mensajes):
        foreach($mensajes as $mensaje):
?>
    <div class="alerta <?php echo $key;?>">
            <?php echo $mensaje;?>
    </div>
<?php
        endforeach;

    endforeach
?>