<style type="text/css">
body{
	background-image: url("<?= base_url()?>/assets/img/fondo.jpg");
	-webkit-background-size: cover;
	-moz-background-size: cover;
  	-o-background-size: cover;
  	background-size: cover;
}
.panel-heading img{width: 100%; height: auto;}	
@media (min-width: 992px){
.col-md-offset-4 {
    margin-left: 36.33333333%;
}
}
.panel-primary>.panel-heading {
    background: none;
    padding: 10px 40px;
}
.panel {
    background-color: rgba(1,1,1,.7);
}
#btn-fblogin{
    width: 100%;
    background: #cdcdcd;
    color: #000;
}
.panel-primary>.panel-heading {
    border-color: transparent;
}
	
</style>
<div class="mainbox col-md-3 col-md-offset-4 col-sm-8 col-sm-offset-2" style="margin-top:12%;" id="loginbox">                    
	<div class="panel panel-primary">
		<div class="panel-heading">
			<!--<div class="panel-title text-center">Acceso a Sistema</div>-->
                        <img src="<?= base_url() ?>/assets/img/logojumbo.png">
		</div>     
		<div class="panel-body login">
			<?php if(isset($_GET["error"])):  ?>
			<div class="alert alert-danger error" role="alert">
				<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
				Error: <?php echo $_GET["error"]; ?>
			</div>
			<?php endif ?>
			<form method="post" action="<?php echo base_url() ?>login/verificar_login">
				<?php if (isset($_GET["t"])): ?>
					<div class="alert alert-danger" role="alert">
					  <strong><?= $_GET["t"] ?></strong>
					</div>	
				<?php endif ?>
				<div class="form-group">
					<input type="text" placeholder="Usuario" name="usuario" class="form-control" id="login-usuario">                                        
				</div>
				
				<div class="form-group">
					<input type="password" placeholder="Password" name="password" class="form-control" id="login-password">                 
				</div>
				<?php 
				if(isset($_COOKIE["captcha_count"])): 
						if($_COOKIE["captcha_count"]>3):
				?>
				<div class="text-center">
					<?php echo $cap['image']; ?>
				</div>
				<div class="form-group">
					<label for="login-capcha">Capcha</label>
					<input type="text" placeholder="captcha" name="captcha" class="form-control" id="login-captcha">                 
				</div>
				<?php 
					endif;
				endif;
				?>
				
				<input type="submit" class="btn btn-primary" href="#" id="btn-fblogin" value="Iniciar Sesión">
			</form>
		</div>                     
	</div>  
</div>