<div class="modal-dialog" role="document" id="mdl-client">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="gridSystemModalLabel"></h4>
        </div>
        <form>
        <div class="modal-body">
                <div>
                    <label for="serie">Cliente</label>
                    <select id="cliente2" name="cliente2" class="form-control" >
                    </select>
                </div>
                <input type="hidden" id="sclie_id" name="sclie_id">
                <div>
                    <?= form_label('<strong>Razón social</strong>', 'rsocial', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "srsocial", "value" => $cliente->clie_rsocial, "class" => "form-control input-sm")); ?>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= form_label('<strong>Documento</strong>', 'documento', array('class' => 'control-label')); ?>
                        <?= form_dropdown('sdocumento', $docu_options, $cliente->clie_tdoc_id, array('class' => 'form-control input-sm')); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= form_label('<strong>Numero de documento</strong>', 'docnum', array('class' => 'control-label')); ?>
                        <?= form_input(array("name" => "sdocnum", "value" => $cliente->clie_doc_nro, "class" => "form-control input-sm")); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <?= form_label('<strong>TIpo</strong>', 'tipo', array('class' => 'control-label')); ?>
                        <?= form_dropdown('sipo', $tipo, $cliente->clie_tipo, array('class' => 'form-control input-sm')); ?>
                    </div>
                    <div class="col-sm-6">
                        <?= form_label('<strong>Correo</strong>', 'email1', array('class' => 'control-label')); ?>
                        <?= form_input(array("name" => "semail", "value" => $cliente->clie_email, "class" => "form-control input-sm")); ?>
                    </div>
                </div>
                <div>
                    <?= form_label('<strong>Dirección</strong>', 'direccion', array('class' => 'control-label')); ?>
                    <?= form_input(array("name" => "sdireccion", "value" => $cliente->clie_direccion, "class" => "form-control input-sm")); ?>
                </div>	
                
                
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            <button type="submit" class="btn btn-primary aceptar_clie">Aceptar</button>
        </div>
    </form>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $sel = $('select#cliente2').select2({
            placeholder: 'Buscar cliente',
            dropdownParent: $("#mdl-client"),
            allowClear: true,
            width: '100%',
            language: "es",
            minimumInputLength: Infinity,
            ajax: {
                url: baseurl + "contacto/buscar_clie",
                dataType: 'json',
                data: function (params) {
                    return {
                        q: params.term,
                        p: params.page,
                        t: "buscar"
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.items,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 0,
        }).on("select2:select", function (e) {
            console.log(e.params.data)
            $("input[name='srsocial']").val(e.params.data.rsocial)
            $("input[name='scontacto']").val(e.params.data.contacto)
            $("select[name='sdocumento']").val(e.params.data.docu)
            $("input[name='sdocnum']").val(e.params.data.docnum)
            $("input[name='semail']").val(e.params.data.email)
            $("input[name='sdireccion']").val(e.params.data.direccion)
            $("input[name='sclie_id']").val(e.params.data.id)
        });
    })
</script>