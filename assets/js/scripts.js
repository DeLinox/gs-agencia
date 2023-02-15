var nameurl = "";
var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};
$(document).ready(function() {
    $('.dropdown.filter .dropdown-menu').on({
        "click":function(e){
          e.stopPropagation();
        }
    });

    $('.dropdown.filter .dropdown-menu select').change(function(){
        var text =[];
        $('.dropdown.filter .dropdown-menu select').each(function(i,item){
            if($(item).val()!='')text.push($(this).find('option:selected').text());
        })
        $('#desFilter ').text(text.join(' | '));
    })

    $('.menu li>a span').click(function() {
        $(location).attr('href', $(this).attr('href'));
        return false;
    })
	
	$('.dropdown.filter .dropdown-menu select').change();


    baseurl = $("#baseurl").val();
    nameurl = $("#nameurl").val();

    procesarFormularioPerfil = function(dlg) {
        $(dlg).find('form').submit(function() {
            $(this).formPost(true, {}, function(data) {
                if (data.exito == false) {
                    $(dlg).find('.error').removeClass('hidden').html(data.mensaje);
                } else {
                    dlg.modal('hide');
                    alert(data.mensaje);
                }
            });
            return false;

            /*            
            $(dlg).find('.error').addClass('hidden')
            $.ajax({
                type: "POST",
                dataType: "json",
                url: baseurl + "usuario/guardar_perfil",
                data: $("#perfil_form").serialize(),
                success: function(data) {
                    if (data.exito == false) {
                        $(dlg).find('.error').removeClass('hidden').html(data.mensaje);
                    } else {
                        dlg.modal('hide');
                    }
                }
            });
            return false;
            */
        });
    };
    $('.perfil').click(function() {
        console.log($(this).attr('title'))
        $(this).load_dialog({
            title: $(this).attr('title'),
            script: baseurl + 'assets/js/Usuario/form.js',
            loaded: procesarFormularioPerfil
        });
        return false;
    });


});