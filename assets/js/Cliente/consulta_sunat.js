$(document).ready(function(){
    $('.sunat_consult').click(function(){
        consulta($('#clie_cons_docnum').val(), 'consulta_sunat');
    })
    $('.reniec_consult').click(function(){
        consulta($('#clie_cons_docnum').val(), 'consulta_reniec');
    })
})

function consulta(doc_num, tipo){
    $.ajax({
        data: { "nruc" : doc_num },
        type: "POST",
        dataType: "JSON",
        url: baseurl + "cliente/" + tipo,
    }).done(function( data, textStatus, jqXHR ){
        if(data['success']!="false" && data['success']!=false){
            if(typeof(data['result'])!='undefined'){
                if(tipo == 'consulta_sunat'){
                    $('#frm-newClient').find('input[name="rsocial"]').val(data.result.RazonSocial);
                    $('#frm-newClient').find('input[name="docnum"]').val(data.result.RUC);
                    $('#frm-newClient').find('select[name="documento"]').val('6');
                    $('#frm-newClient').find('input[name="direccion"]').val(data.result.Direccion);
                }else{
                    $('#frm-newClient').find('select[name="documento"]').val('1');
                    $('#frm-newClient').find('input[name="rsocial"]').val(data.result.Paterno+" "+data.result.Materno+", "+data.result.Nombre);
                    $('#frm-newClient').find('input[name="docnum"]').val(data.result.DNI);
                }
            }
        }else{
            if(typeof(data['msg'])!='undefined'){
                alert( data['msg'] );
            }
        }
    }).fail(function( jqXHR, textStatus, errorThrown ){
        alert( "Solicitud fallida:" + textStatus );
    });
}