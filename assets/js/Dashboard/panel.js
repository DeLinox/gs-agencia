$(document).ready(function(){
	get_deudas("SOLES");
	get_deudas("DOLARES");
})
function get_deudas($moneda){

	$.ajax({
        type: "POST",
        dataType: "json",
        url: baseurl + "Dashboard/get_dataCobros",
        data: {moneda: $moneda},
        success: function(data) {
            $('#tbl-general'+data.ini).find("tbody").html(data.html);
        }
    });
}
