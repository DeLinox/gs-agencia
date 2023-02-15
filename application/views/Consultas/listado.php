<div class="">
	<h3>Comprobantes de <?php echo $rsocial ?></h3>
	<form class="ocform form-inline">
        <div class="nosel">
            <input type="hidden" name="desde" id="desde"/>
            <input type="hidden" name="hasta" id="hasta"/>
            <div class="form-group">
                <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;">
                    <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                    <span></span> <b class="caret"></b>
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="glyphicon glyphicon-search"></i>
                    Filtrar
                </button>
            </div>
			
        </div>
    </form>
	<?php  echo $this->Model_general->genDataTable('mitabla', $columns, true,true); ?>


</div>