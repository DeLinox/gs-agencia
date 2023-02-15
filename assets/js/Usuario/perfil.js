$(document).ready(function(){
    var baseurl = $("#baseurl").val();
    // NUMERO DE DOCUMENTO
  $(".password").attr('disabled', true); 
  $("#act_password").click(function() {  
        if($("#act_password").is(':checked')) {  
            // alert("Está activado"); 
            $(".password").attr('disabled', false); 
        } else {  
            $(".password").attr('disabled', true); 
            $(".password").val(""); 
            // alert("No está activado"); 
        }  
    });  
});

