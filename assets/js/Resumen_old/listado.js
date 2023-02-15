var baseurl
$(document).on('ready', function () {
    baseurl = $("#baseurl").val();
    var url = baseurl + 'Resumen/Listado?json=true';
    var $table;
    var seleccionados;

    function botones(id,estado,$ar){
       esini = (estado==1||estado==6);
       eslis = (estado==1||estado==2||estado==6||estado==6||estado==8||estado==9);
       esval = (estado==3||estado==4);
       html = `<div class='dropdown'>
          <button type='button' class='btn btn-success btn-sm dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
            <span class='glyphicon glyphicon-eye-open'></span>
            <span class='caret'></span>
            <span class='sr-only'>Toggle Dropdown</span>
          </button>
          <ul class='dropdown-menu'>
            <li class="show_editar oculto"><a href='{baseurl}Resumen/edit/{id}' class='editar'><span class='glyphicon glyphicon-pencil'></span> Editar</a></li>
            <li class="show_xml oculto"><a href='{baseurl}Resumen/getxml/{id}'><span class='glyphicon glyphicon-menu-hamburger'></span> Comprobante digital</a></li>
            <li class="show_cdr oculto"><a href='{baseurl}Resumen/getcdr/{id}'><span class='glyphicon glyphicon-cloud-download'></span> Constancia Sunat</a></li>
          </ul>
        </div>
        <span class='show_enviar oculto'><a href='{baseurl}Resumen/enviarSunat/{id}' class='btn btn-primary btn-sm enviar'><span class='glyphicon glyphicon-refresh'></span></a></span>`;

        html = replaceAll(html,"{baseurl}", baseurl);
        html = replaceAll(html,"{id}", id);
        $ar.append(html);
        if(eslis)$ar.find('.show_enviar').show();
        if(esini)$ar.find('.show_editar').show();
        if(esval)$ar.find('.show_xml,.show_cdr').show();

        $ar.find('.enviar').click(function(){

            if(confirm("¿Desea enviar el comprobante a la SUNAT?")){
                $.gs_loader.show();
                $.ajax({
                   type: "POST",
                   dataType: "json",
                   url:$(this).attr('href') ,
                   success: function(data){
                            $.gs_loader.hide();
                            $table.draw('page');
                            if(data.mensaje!='') alert(data.mensaje);
                   },
                   error: function(response) {
                        $table.draw('page');
                        $.gs_loader.hide();
                        alert(response.responseText.replace(/(<([^>]+)>)/ig,""));
                    }
                });
                $(this).addClass('disabled'); 
            }
            return false;
        });

    }  

    var $dt = $('#mitabla'),
            conf = {
                data_source: url,
                cactions: ".ocform",
                order: [[1, "desc"]],
                oncheck: function (row, data, selected) {
                    if (selected.length > 0) {
                        $('.onsel').removeClass('hidden');
                        $('.nosel').addClass('hidden');
                    } else {
                        $('.onsel').addClass('hidden');
                        $('.nosel').removeClass('hidden');
                    }
                    seleccionados = selected;
                },
                onrow: function (row, data) {
                    botones(data.DT_RowId,data.DT_Estado,$(row).find('td .botones'));
                }
            };
	var $this;
    var $dlg;



  



    $('.ocform').submit(function () {
        $table.draw();
        return false;
    })
    
    var start = moment().subtract(29, 'days');
    var end = moment();

    function cb(start, end) {
        $('#reportrange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
        $('#desde').val(start.format('YYYY-MM-DD'));
        $('#hasta').val(end.format('YYYY-MM-DD'));
    }

    $('#reportrange').daterangepicker({
        startDate: start,
        endDate: end,
        locale: {
              format: 'DD/MM/YYYY'
        },
        ranges: {
           'Hoy': [moment(), moment()],
           'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Ultimos 7 Días': [moment().subtract(6, 'days'), moment()],
           'Ultimos 30 Días': [moment().subtract(29, 'days'), moment()],
           'Este Mes': [moment().startOf('month'), moment().endOf('month')],
           'Anterior Mes': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
    }, cb);

    cb(start, end);
    var buton = "<div class='botones'></div>";
    $table = $dt.load_simpleTable(conf, true,buton);
    
});
