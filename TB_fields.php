<head>
	<script type="text/javascript" src="BMyFrmwk.js"></script>
	<script type="text/javascript">
	var ob = new BMy();
	var S = "";
	function init(){
		var raiz = ob.getById("tbCampos");
		var fragmento = document.createDocumentFragment();
		//fragmento.innerHTML = displayTable();
		raiz.innerHTML = displayTable();
		return;		
		}
	function displayTable(){
		//alert("Cheguei em displayForm");
		S += "<table id=\"tb1\">";
		S += "<thead>";
		S += "<th>Nome Struct</th><th>Nome Campos</th><th>Tipo</th><th>Tamanho</th>";
		S += "</thead>";
		S += "<tbody id=\"tbStru\">";
		S += "<tr><td><input type=text id=Nomestru01></td><td><input type=text id=Nome01></td><td><input type=text id=Tipo01></td><td><input type=text id=Tamanho01></td></tr>";
		S += "</tbody>";
		S += "</table>";
		
		return S;
		}
	// Adiciona uma linha ? tabela
	function addline(){
		// Handle para tabela
		var htb = ob.getById("tb1");
		var noLinhas = (ob.getByTagName("tbStru","tr")).length;
		var row = htb.insertRow();
		// Coluna 1
		var cell1 = row.insertCell();
		var inp1 = document.createElement("input");
		inp1.id = "Nomestru"+ob.ZeroField(noLinhas+1,2);
		inp1.type = "text";
		cell1.appendChild(inp1);
		// Coluna 2
		var cell2 = row.insertCell();
		var inp2 = document.createElement("input");
		inp2.id = "Nome"+ob.ZeroField(noLinhas+1,2);
		inp2.type = "text";
		cell2.appendChild(inp2);
		// Coluna 3
		var cell3 = row.insertCell();
		var inp3 = document.createElement("input");
		inp3.id = "Tipo"+ob.ZeroField(noLinhas+1,2);
		inp3.type = "text";
		cell3.appendChild(inp3);
		// Coluna 4
		var cell4 = row.insertCell();
		var inp4 = document.createElement("input");
		inp4.id = "Tamanho"+ob.ZeroField(noLinhas+1,2);
		inp4.type = "text";
		cell4.appendChild(inp4);
		}
	function savestru(){
		// Handle para tabela
		var htb = ob.getById("tb1");
		var colec = ob.getByTagName("tbStru","tr");
		var noLinhas = colec.length;
		var filhos = "";
		var S = "";
		for(i=0;i<noLinhas;i++){
			S = "";
			filhos = colec[i];
			S += "{ Nomestru : \"" + filhos.children[0].children[0].value + "\" } ,";
			S += "{ Nome : \"" + filhos.children[1].children[0].value + "\" } ,";
			S += "{ Tipo : \"" + filhos.children[2].children[0].value + "\" } ,";
			S += "{ Tamanho : \"" + filhos.children[3].children[0].value + "\" } ";
			console.log(S);
			console.log(filhos);
			}
		
		}
	document.addEventListener("DOMContentLoaded", function(e) {
		// Aqui vai o seu c?digo
		init();
		});
	</script>
</head>
<?php
function lstTabelas($Bank,$tipoProc){
	$retorno = false;
	// Abre o banco
	$fp = fopen($Bank, "r");
	// Primeira Linha (Cabe?alho banco)
	$linha1 = fgets($fp);
	// Segunda Linha
	$txt = fgets($fp);
	fclose($fp);
	//echo "T:".$txt. "<br>";
	// L? tabelas do Banco
	if( $txt == "" ){
		$retorno = false;
		echo "";
		} else {
		// Existem tabelas
		// Conte?do da linha em string para JSON
		$arrTab = json_decode(str_replace('\n', '', $txt),true);
		$tabelas = $arrTab['tabelas'];
		//echo "J1:" . $tabelas . "<br>";
		$Html = "";
		foreach( $tabelas as $valor ) {
			//echo $valor . "<br>";
			$Html .= "<option value=\"" . $valor . "\">" . $valor . "</option>";
			}
		$retorno = true;
		}
	if( $tipoProc == "testa" ){
		return $retorno;
		} else {
		return $Html;
		}
	}
function displayForm($html){
	$cod .= "<form method=\"post\" action =\"\">";
	$cod .= "<select id=tabelas>";
	$cod .= $html;
	$cod .= "</select>";
	$cod .= "</form>";
	$cod .= "<input type=button onclick=\"addline()\" value=\"NOVO campo\">";
	$cod .= "<input type=button onclick=\"savestru()\" value=\"NOVO campo\">";
	$cod .= "<div id=\"tbCampos\"></div>";
	return $cod;
	}
	/*
		TB_create.php?Banco=Berna.dmy&Tabela=Contatos.tmy
	*/
	// Existe o par?metro Banco na URL ?
	if(	isset($_GET["Banco"]) ){ 
		$Banco = $_GET["Banco"];
		// O par?metro Banco est? preenchido ?
		if( $Banco != "" ){
			$ext = substr($Banco,-3);
			// A extens?o ? dmy ?
			if( !($ext == "dmy") ){
				$Banco = $Banco . ".dmy";
				}
			} else {
			echo "[{ Id : \"Err\" }, { Erro : \"DB_Err 002:Nome do banco n?o preenchido. Use ?Banco=xxxx.dmy\" }]";
			}
		} else {
			echo "[{ Id : \"Err\" }, { Erro : \"DB_Err 001:Sem nome do banco. Use ?Banco=xxxx.dmy\" }]";
		}
	if( !file_exists($Banco) ){
		echo "[{ Id : \"Err\" }, { Erro : \"DB_Err 400:Banco N?O EXISTE\" }]";
		} else {
		// Existe o par?metro Tabela na URL ?
		if(	isset($_GET["Tabela"]) ){ 
			$Tabela = $_GET["Tabela"];
			// O par?metro Tabela est? preenchido ?
			if( $Tabela != "" ){
				$ext = substr($Tabela,-3);
				// A extens?o ? tmy ?
				if( !($ext == "tmy") ){
					$Tabela = $Tabela . ".tmy";
					}
				// O arquivo correspondente a tabela existe ?
				if( !file_exists($Tabela) ){
						echo "TB_Err 007:Tabela n?o existe. Verifique a grafia";
						} else {
						// Acrescenta tabela na lista de tabelas no registro do Banco
						// e mostra formul?rio para inclus?o de campos
						if( lstTabelas($Banco, "testa") ){
							echo "<h1>Tabelas do Banco: " . $Banco . "</h1>";
							echo displayForm(lstTabelas($Banco, "html"));
							//echo "<scr". "ipt type=\"text/javascript\">displayTable();</scr" . "ipt>";
							echo "[{ Id : \"Err\" }, { Erro : \"TB_Err 200:Tabelas lidas com sucesso\" }]";
							} else {
							echo "[{ Id : \"Err\" },{ \"DB_Err 500\" : \"Banco danificado, sem refer?ncia ?s tabelas\" } ] } ]";
							}
						}
				} else {
				echo "[{ Id : \"Err\" }, { Erro : \"TB_Err 002:Nome da tabela n?o preenchido. Use ?Tabela=xxxx.tmy\" }]";
				}
			} else {
				echo "[{ Id : \"Err\" }, { Erro : \"TB_Err 001:Sem nome da tabela. Use ?Tabela=xxxx.tmy\" }]";
			}
		}
?>
