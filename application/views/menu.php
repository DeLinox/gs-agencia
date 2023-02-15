<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?=base_url()?>">
				<img class="logo" alt="Logo" src="<?=base_url() ?>assets/img/logo.png">
				<span>AgenSIS</span>
			</a>
            <ul class="nav pull-right hidden-xs">
                <li class="dropdown pull-right">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="glyphicon glyphicon-user"></i> <?= $this->session->userdata('username') ?> <i class="glyphicon glyphicon-menu-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="<?=base_url() ?>configuracion/conf_new_usuario/<?= $this->usua_id ?>" title="Editar Perfil" class="perfil"><i class="fa fa-user fa-fw"></i> Datos del usuario</a></li>
                		<li class="divider"></li>
                		<li><a href="<?php echo base_url() ?>login/salir"><i class="fa fa-sign-out fa-fw"></i> Salir del sistema</a></li>
                	</ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="sidebar">
    <div class="sidebar-nav navbar-collapse collapse">
        <ul class="menu" id="side-menu">
            <?php
                $this->menu->init($this->permisos);
            	$menu = $this->menu->getArray();
                $currentURL = current_url();
				foreach($menu as $mid=>$m):
                    $active = "";
                    if(preg_match("/{$m['base']}/",$currentURL)) $active = "active";
				?>
                <li>
                    <a href="<?php echo base_url().$m['url']; ?>" class="<?php echo $active; ?>">
						<i class="<?php echo $m['icon']; ?>"></i>
						<?php echo $m['name']; ?>
						<?php if(isset($m['more'])): ?>
                        <span class="more glyphicon glyphicon-plus" href="<?php echo base_url().$m['more'] ?>"></span>
                        <?php endif; ?>
					</a>
                </li>
                <?php 
				endforeach;
			?>
        </ul>
    </div>
</div>