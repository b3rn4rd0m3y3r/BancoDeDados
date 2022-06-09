<?php
	/*
		BD_open.php?Banco=Berna.dmy
	*/
	if(	isset($_GET["Banco"]) ){ 
		$Banco = $_GET["Banco"];
		if( $Banco != "" ){
			$ext = substr($Banco,-3);
			if( !($ext == "dmy") ){
				$Banco = $Banco . ".dmy";
				}
			echo "Banco: " . $Banco . "<br>";
			if( file_exists($Banco) ){
				$fp = fopen($Banco, "r");
				$txt = fgets($fp);
				echo $txt;
				fclose($fp);
				} else {
				echo "[{ Id : \"Err\" }, { Erro : \"DB_Err 005:Banco nуo existe. Verifique a digitaчуo do nome.\" }]";
				}
			} else {
			echo "[{ Id : \"Err\" }, { Erro : \"DB_Err 002:Nome do banco nуo preenchido. Use ?Banco=xxxx.dmy\" }]";
			}
		} else {
			echo "[{ Id : \"Err\" }, { Erro : \"DB_Err 001:Sem nome do banco. Use ?Banco=xxxx.dmy\" }]";
		}
?>