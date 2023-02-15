<form class="form-horizontal" action="?c=<?=$this->clase?>&m=guardar_datos" method="post">
	<?php
	$s1 = $this->formVertical(array(
		array('type' => 'text', 'name' => 'login', 'text' => 'Usuario', 'value' => $usua_login, 'class'=>'full'),
		//array('type' => 'password', 'name' => 'password', 'text' => 'Password', 'value' => $usua_password, 'class'=>'full'),
                // $this->formComponente(
                //     array('text' => 'Cambiar Contraseña', 'name' => 'editarpass', 'id' => 'editarpass','type' => 'checkbox', 'class' => 'min ml10','value' => $editar ),
                //     $this->formInput(
                //         array('text' => '', 'name' => 'password', 'class' => 'medium ml20', 'type' => 'password', 'id' => 'numero')
                //     )
                // ),
		array('type' => 'text', 'name' => 'email', 'text' => 'Correo electrónico', 'value' => $usua_email, 'class'=>'full')
		
                
        ));

	$s2 = $this->formVertical(array(
		array('type' => 'text', 'name' => 'documento', 'text' => 'Nro. Documento', 'value' => $usua_documento, 'class' => 'full'),
		array('type' => 'text', 'name' => 'nombres', 'text' => 'Nombres', 'value' => $usua_nombres, 'class'=>'full'),
		array('type' => 'text', 'name' => 'apellidos', 'text' => 'Apellidos', 'value' => $usua_apellidos, 'class'=>'full'),
		array('type' => 'text', 'name' => 'fecha_nacimiento', 'id' => 'fecha_nacimiento', 'class'=>'fecha full', 'text' => 'Fecha Nac.', 'value' => $fecha_nacimiento),
        ));

	echo $this->formHSection("Datos generales del Usuario",array($s1, $s2));

	$s3 = $this->formVertical(array(
		array('type' => 'password', 'name' => 'password_actual', 'text' => 'Contraseña anterior', 'value' => '', 'class'=>'full')
                
        ));

	$s4 = $this->formVertical(array(
		array('type' => 'password', 'name' => 'password_nuevo', 'text' => 'Nueva contraseña', 'value' => '', 'class'=>'full'),
		array('type' => 'password', 'name' => 'password_nuevo2', 'text' => 'Repetir contraseña', 'value' => '', 'class'=>'full')
        ));
	echo $this->formHSection("Cambiar Contraseña",array($s3, $s4));
	?>
</form>