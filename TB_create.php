<?php
function addTabela($Bank, $Tab){
	$retorno = false;
	echo "<br>aT:".$Tab."<br>";
	// Abre o banco
	$fp = fopen($Bank, "r");
	// Primeira Linha (Cabeçalho banco)
	$linha1 = fgets($fp);
	// Segunda Linha
	$txt = fgets($fp);
	fclose($fp);
	echo "T:".$txt. "<br>";
	// Lê tabelas do Banco
	if( $txt == "" ){
		$retorno = false;
		echo "J0:" . json_decode(str_replace('\n', '', $txt),true). "<br>";
		} else {
		// Existem tabelas
		// Conteúdo da linha em string para JSON
		$arrTab = json_decode(str_replace('\n', '', $txt),true);
		$tabelas = $arrTab['tabelas'];
		echo "J1:" . $tabelas . "<br>";
		foreach( $tabelas as $valor ) {
			echo $valor . "<br>";
			}
		array_push($tabelas, $Tab);
		$arrTab['tabelas'] = $tabelas;
		// JSON to string
		$linha2 = json_encode($arrTab);
		echo "L2:".$linha2 . "<br>";
		// String para JSON
		echo "JD:" . json_decode($linha2,true) . "<br>";
		// Abre o banco
		$fp = fopen($Bank, "w");
		fwrite($fp,$linha1);
		fwrite($fp,$linha2);
		$retorno = true;
		}
	return $retorno;
	}
	/*
		TB_create.php?Banco=Berna.dmy&Tabela=Contatos.tmy
	*/
	// Existe o parâmetro Banco na URL ?
	if(	isset($_GET["Banco"]) ){ 
		$Banco = $_GET["Banco"];
		// O parâmetro Banco está preenchido ?
		if( $Banco != "" ){
			$ext = substr($Banco,-3);
			// A extensão é dmy ?
			if( !($ext == "dmy") ){
				$Banco = $Banco . ".dmy";
				}
			} else {
			echo "[{ Id : \"Err\" }, { Erro : \"DB_Err 002:Nome do banco não preenchido. Use ?Banco=xxxx.dmy\" }]";
			}
		} else {
			echo "[{ Id : \"Err\" }, { Erro : \"DB_Err 001:Sem nome do banco. Use ?Banco=xxxx.dmy\" }]";
		}
	// Existe o parâmetro Tabela na URL ?
	if(	isset($_GET["Tabela"]) ){ 
		$Tabela = $_GET["Tabela"];
		// O parâmetro Tabela está preenchido ?
		if( $Tabela != "" ){
			$ext = substr($Tabela,-3);
			// A extensão é tmy ?
			if( !($ext == "tmy") ){
				$Tabela = $Tabela . ".tmy";
				}
			// O arquivo correspondente a tabela existe ?
			if( !file_exists($Tabela) ){
				// GRAVAÇÃO DA NOVA TABELA
				$fp = fopen($Tabela, "w");
				//$txt = mb_convert_encoding($txt, 'ISO-8859-1', 'auto');
				$txt = "[ { \"Id\" : \"OK\" }, { \"Nome\" : \"" . $Tabela . "\"}]\n";
				fwrite($fp, $txt);
				$txt = "{ \"campos\":[ ] }\n";
				fwrite($fp, $txt);
				fclose($fp);
				/*
					ACRESCENTA TABELA na lista de tabelas no registro do Banco
				*/
				if( addTabela($Banco, $Tabela) ){
					echo "[{ Id : \"Err\" }, { Erro : \"TB_Err 200:Tabela criada com sucesso\" }]";
					} else {
					echo "[{ Id : \"Err\" }, { Erro : [ { \"TB_Err 200\" : \"Tabela criada\" } , { \"DB_Err 500\" : \"Banco danificado, sem referência às tabelas\" } ] } ]";
					}
				// Depois de criar confere se foi criado
				if( !file_exists($Tabela) ){
					echo "TB_Err 003:Não consegui criar a tabela. Verifique as permissões da pasta";
					} else {

					}
				} else {
				echo "[{ Id : \"Err\" }, { Erro : \"TB_Err 400:Tabela já existe\" }]";
				}
			} else {
			echo "[{ Id : \"Err\" }, { Erro : \"TB_Err 002:Nome da tabela não preenchido. Use ?Tabela=xxxx.tmy\" }]";
			}
		} else {
			echo "[{ Id : \"Err\" }, { Erro : \"TB_Err 001:Sem nome da tabela. Use ?Tabela=xxxx.tmy\" }]";
		}


	// Lock de Banco
	/*
	$lck = "LCK_" . $_GET["origin"] . "_" .$arq;
	$fp = fopen($lck, 'w');
	fwrite($fp, "SND\r\n");
	fclose($fp);
	*/
?>