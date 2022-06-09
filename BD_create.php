<?php
	/*
		BD_create.php?Banco=<banco>.dmy
	*/
	// Existe o par�metro Banco na URL ?
	if(	isset($_GET["Banco"]) ){ 
		$Banco = $_GET["Banco"];
		// O par�metro Banco est� preenchido ?
		if( $Banco != "" ){
			$ext = substr($Banco,-3);
			// A extens�o � dmy ?
			if( !($ext == "dmy") ){
				$Banco = $Banco . ".dmy";
				}
			// O arquivo correspondente ao banco existe ?
			
			if( !file_exists($Banco) ){
				$fp = fopen($Banco, "w");
				//$txt = mb_convert_encoding($txt, 'ISO-8859-1', 'auto');
				$txt = "[ { \"Id\" : \"OK\" }, { \"Nome\" : \"" . $Banco . "\"}]\n";
				fwrite($fp, $txt);
				$txt = "{ \"tabelas\":[ ] }\n";
				fwrite($fp, $txt);
				fclose($fp);
				// Depois de criar confere se foi criado
				if( !file_exists($Banco) ){
					echo "DB_Err 003:N�o consegui criar o banco. Verifique as permiss�es da pasta";
					} else {
					echo "[{ Id : \"OK\" }, { Erro : \"DB_Err 200:Banco criado com sucesso\" }]";
					}
				} else {
				echo "[{ Id : \"Err\" }, { Erro : \"DB_Err 400:Banco j� existe\" }]";
				}
				
			} else {
			echo "[{ Id : \"Err\" }, { Erro : \"DB_Err 002:Nome do banco n�o preenchido. Use ?Banco=xxxx.dmy\" } ]";
			}
		} else {
			echo "[ { Id : \"Err\" }, { Erro : \"DB_Err 001:Sem nome do banco. Use ?Banco=xxxx.dmy\" } ]";
		}
		
	// Lock de Banco
	/*
	$lck = "LCK_" . $_GET["origin"] . "_" .$arq;
	$fp = fopen($lck, 'w');
	fwrite($fp, "SND\r\n");
	fclose($fp);
	*/
?>