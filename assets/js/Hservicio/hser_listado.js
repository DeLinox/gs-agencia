var url = "";
$(document).on('ready', function() {
    url = $("#nameurl").val() + '?json=true';
    
    $(".datepicker").daterangepicker({
        singleDatePicker: true,
        locale: {
            format: 'DD/MM/YYYY'
        }
    });   

    hserv_listado();

    $('.ocform input').change(hserv_listado);

    $('.reporte_excel').click(function(){
        window.location.href = baseurl + "Hservicio/reporte_excel_hojaserv?fecha="+$('input[name="fecha"]').val();
    })

});
function change_date(){
    
}

function fn_tbl() {
    console.log("fn_tbl");
    $('.change_estado').on('click', change_estado);
}

function change_estado() {
    console.log("change estado");
    $row = $(this).parent().parent();
    if(confirm("Cambiar estado?")){
        $.ajax({
            dataType: "json",
            url: baseurl+"Hservicio/hser_change_estado",
            data: {id : $(this).attr("data-id")},
            type: "POST",
            success: function(resp){
                if(resp.exito){
                    if(resp.estado == 1){
                        $row.addClass("row_llegada");
                    }else{
                        $row.removeClass("row_llegada");
                    }
                }else{
                    alert("Ocurrio un error, intententelo mas tarde");
                }
            }
        })
    }
}
function hserv_listado() {
    $.ajax({
        dataType: "json",
        url: url,
        data: $(".ocform").serialize(),
        type: "POST",
        success: function(resp){
            $.each(resp.resp, function(i, elem) {
                var identicador = "#serv"+elem.serv_id;
                if(elem.html != ''){
                    $(identicador).find("tbody").html(elem.html);
                    console.log("siguientes");
                }else{
                    $(identicador).find("tbody").html("<tr><th colspan='6'>No hay reservas para este servicio</th></tr>");
                }
            })
            $('#servPriv').find('tbody').html(resp.privados.html);
            fn_tbl();
        }
    })
}
