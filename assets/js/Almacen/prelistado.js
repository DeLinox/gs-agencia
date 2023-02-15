$(document).on('ready', function () {

    var baseurl = $("#baseurl").val();
    var url = baseurl + 'Venta/sopre?json=true';
    var $table;
    var seleccionados;

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
                    $(row).find('td .botones').html("<a href='"+baseurl+"Venta/tofact/"+data.DT_RowId+"' class='enviar btn btn-primary btn-sm'><span class='glyphicon glyphicon-check'></span></a>");
					//$(row).find('td .botones').append("<a href='/sopre2/index.php?c=cobros_cmp&m=comprob_pdf&id="+data.DT_RowId+"' class='btn btn-primary'><span class='glyphicon glyphicon-share-alt'></span></a>");
                }
            };
   
    
    $('.ocform').submit(function () {
        $table.draw();
        return false;
    })
    moment.locale('es');
    var start = moment().subtract(29, 'days');
    var end = moment();

    function cb(start, end) {
        $('#reportrange span').html(start.format('DD/MM/YYYY') + ' - ' + end.format('DD/MM/YYYY'));
        $('#desde').val(start.format('YYYY-MM-DD'));
        $('#hasta').val(end.format('YYYY-MM-DD'));
    }

    $('#reportrange').daterangepicker({
        isRTL: false,
        startDate: start,
        endDate: end,
        language: 'es',
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
