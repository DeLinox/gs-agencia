<?php
class Numl{
	var $resultado;
	
	function Convertir($num,$tipo=false){
		$num = intval($num);
		$MinNum = 0;
		$MaxNum = 4294967295;
		
		$Numeros = array("CERO", "UN0", "DOS", "TRES", "CUATRO", "CINCO", "SEIS", "SIETE", "OCHO", "NUEVE", "DIEZ", "ONCE", "DOCE", "TRECE", "CATORCE", "QUINCE", "DIECISEIS", "DIECISIETE", "DIECIOCHO", "DIECINUEVE");
		$Dieces = array("CERO", "DIEZ", "VEINTE", "TREINTA", "CUARENTA", "CINCUENTA", "SESENTA", "SETENTA", "OCHENTA", "NOVENTA");
		
		if($num >= $MinNum && $num <= $MaxNum) $valor = $this->NumeroRecurso($num, 0);
		else $valor = "Error";
		
		if($valor=="UNO"&&$tipo==true) $valor = "PRIMER";
		
		return $valor;
	}
	
	
	function NumeroRecurso($N,$P){
		$Numeros = array("CERO", "UNO", "DOS", "TRES", "CUATRO", "CINCO", "SEIS", "SIETE", "OCHO", "NUEVE", "DIEZ", "ONCE", "DOCE", "TRECE", "CATORCE", "QUINCE", "DIECISEIS", "DIECISIETE", "DIECIOCHO", "DIECINUEVE");
		$Numeros1 = array("CERO", "UN", "DOS", "TRES", "CUATRO", "CINCO", "SEIS", "SIETE", "OCHO", "NUEVE", "DIEZ", "ONCE", "DOCE", "TRECE", "CATORCE", "QUINCE", "DIECISEIS", "DIECISIETE", "DIECIOCHO", "DIECINUEVE");
		$Dieces = array("CERO", "DIEZ", "VEINTE", "TREINTA", "CUARENTA", "CINCUENTA", "SESENTA", "SETENTA", "OCHENTA", "NOVENTA");
		$Dieces1 = array("CERO", "DIEZ", "VEINT", "TREINT", "CUARENT", "CINCUENT", "SESENT", "SETENT", "OCHENT", "NOVENT");
		$Cienes = array("CERO", "CIENTO", "DOSCIENTOS", "TRESCIENTOS", "CUATROCIENTOS", "QUINIENTOS", "SEISCIENTOS", "SETECIENTOS", "OCHOCIENTOS", "NOVECIENTOS");
		$Result = "";
		
		$N = floor($N);
		
		if($N==0) $Result = "";
		else if($N>=1&&$N<=19)
			if($P==1000) $Result = $Numeros1[$N];
			else $Result = $Numeros[$N];
		else if($N>=20&&$N<=29)
			if($N%10<>0) $Result = $Dieces1[floor($N/10)]."I".$this->NumeroRecurso($N%10,$P);
			else $Result = $Dieces[floor($N/10)]." ".$this->NumeroRecurso($N%10,$P);
		else if($N>=30&&$N<=99)
			if($N%10<>0) $Result = $Dieces[floor($N/10)]." Y ".$this->NumeroRecurso($N%10,$P);
			else $Result = $Dieces[floor($N/10)]." ".$this->NumeroRecurso($N%10,$P);
		else if($N>=100&&$N<=999)
			if($N/100==1)
				if($N==100) $Result = "CIEN"." ".$this->NumeroRecurso($N%100,$P);
				else $Result = $Cienes[floor($N/100)]." ".$this->NumeroRecurso($N%100,$P);
			else
				$Result = $Cienes[floor($N/100)]." ".$this->NumeroRecurso($N%100,$P);
		else if($N>=1000&&$N<=1999)
			$Result = "MIL ".$this->NumeroRecurso($N%1000,$P);
		else if($N>=2000&&$N<=20999)
			$Result = $this->NumeroRecurso($N/1000,$P)." MIL ".$this->NumeroRecurso($N%1000,$P);
		else if($N>=21000&&$N<=91999)
			$Result = $this->NumeroRecurso($N/1000,1000)." MIL ".$this->NumeroRecurso($N%1000,$P);
		else if($N>=100000&&$N<=999999)
			$Result = $this->NumeroRecurso($N/1000,$P)." MIL ".$this->NumeroRecurso($N%1000,$P);
		else if($N>=1000000&&$N<=999999999)
			$Result = $this->NumeroRecurso($N/1000000,$P)." MILLONES ".$this->NumeroRecurso($N%1000000,$P);
		else if($N>=1000000000&&$N<=4294967295)
			$Result = $this->NumeroRecurso($N/1000000000,$P)." BILLONES ".$this->NumeroRecurso($N%1000000000,$P);
		
		return $Result;
	}
	
	
	function Numl($valor=''){
		if(is_numeric($valor))
			return $this->Convertir($valor);
		return false;
	}
}
?>