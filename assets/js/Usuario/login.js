$(document).ready(function(){
    var baseurl = $("#baseurl").val(); 
    $("#loginform").submit(function(){
        // alert("hola");
        $.ajax({
           type: "POST",
            dataType: "json",
           url:baseurl+"login/evaluate" ,
           data: $("#loginform").serialize(), 
           success: function(data)
           {
               		// console.log(data.url);
                if(data.url)
                {
                    $(location).attr('href',data.url);
                }

                if (data.error) {
                    $(location).attr('href',data.url+"?error="+data.error);
					          // $(".login").find('.error').removeClass('hidden').html(data.error);
				        } 
           }
         });
        return false;
    });
    // alert("hola");

});