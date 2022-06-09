<head>
	<link rel="icon"       type="image/ico"       href="favicon.ico">
	<style>
		DIV.hide {display: none;}
	</style>
	<!--
			FUN��ES DA SCRIPT
			
		init								Fun��o disparada quando o fonte est� todo carregado ([1]DOM e [2]PHP)
		addline							Adiciona uma linha � tabela (uma linha de grid de INPUTs)
		displayTable					Mostra a tabela de campos na TELA
		lestru(tabela,S)				L� a estrutura de uma tabela
		loadStru(tabela)				Carrega a estrutura da tabela nos campos do grid (TELA)
		savestru						Salva estes campos da TELA em forma de estrutura no arquivo ".tmy"
		savestrufile(Resultado,S)	Salva a estrutura, despachando a requisi��o para uma script PHP
	-->
	<script type="text/javascript" src="BMyFrmwk.js"></script>
	<script type="text/javascript">
	// Handle para o framework BMy
	var ob = new BMy();
	// L� par�metro "Tabela" na URL
	var url = window.location;
	var sUrl = url.search;
	var pos = url.search.search(/Tabela/);
	var TABELA = sUrl.substr(pos+7,99);
	var S = "";
	// Fun��o disparada quando o fonte est� todo carregado (DOM e PHP)
	function init(){
		var raiz = ob.getById("tbCampos");
		var fragmento = document.createDocumentFragment();
		raiz.innerHTML = displayTable();
		// Carrega a estrutura da TABELA
		loadStru(TABELA);
		return;		
		}
	// Carrega a estrutura da tabela nos campos do grid (TELA)
	function loadStru(tabela){
		var myHeaders = new Headers();
		myHeaders.append('Content-Type','text/plain; charset=iso-8859-1');
		// <tabela>.tmy
		fetch(tabela, myHeaders)
			.then(function (response) {
				return response.text();
			})
			.then(function (result) {
				RES = result;
				ob.getById("tbRes").innerText = RES;
				console.log("R:"+result);
				var arr = result.split("\n");
				// Carrega os campos no grid
				var flds = (JSON.parse(arr[1])).campos;
				console.log(flds);
				for(i=0;i<flds.length;i++){
					console.log(flds[i]);
					ob.getById("Nomestru"+ob.ZeroField(i+1,2)).value = flds[i].Nomestru;
					ob.getById("Nome"+ob.ZeroField(i+1,2)).value = flds[i].Nome;
					ob.getById("Tipo"+ob.ZeroField(i+1,2)).value = flds[i].Tipo;
					ob.getById("Tamanho"+ob.ZeroField(i+1,2)).value = flds[i].Tamanho;
					addline();
					}
				return RES;
				});
		}
	// Mostra a tabela de campos na tela
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
	// Adiciona uma linha � tabela
	function addline(){
		// Handle para tabela
		var htb = ob.getById("tb1");
		var noLinhas = (ob.getByTagName("tbStru","tr")).length;
		var row = htb.insertRow();
		var celula = [], inp = [];
		var nmFld = ['N/A','Nomestru','Nome','Tipo','Tamanho'];
		// Coluna 1-4
		for(k=1;k<=4;k++){
			celula[k] = row.insertCell();
			inp[k] = document.createElement("input");
			inp[k].id = nmFld[k]+ob.ZeroField(noLinhas+1,2);
			inp[k].type = "text";
			celula[k].appendChild(inp[k]);
			}
		}
	// Salva estes campos da TELA em forma de estrutura no arquivo ".tmy"
	function savestru(){
		// Handle para tabela f�sica no DOM
		var htb = ob.getById("tb1");
		var colec = ob.getByTagName("tbStru","tr");
		var noLinhas = colec.length;
		var filhos = "";
		var S = "";
		for(i=0;i<noLinhas;i++){
			filhos = colec[i];
			S += "{ \"Nomestru\" : \"" + filhos.children[0].children[0].value + "\"  ,";
			S += " \"Nome\" : \"" + filhos.children[1].children[0].value + "\"  ,";
			S += " \"Tipo\" : \"" + filhos.children[2].children[0].value + "\"  ,";
			S += " \"Tamanho\" : \"" + filhos.children[3].children[0].value + "\" },";
			}
		S = ob.left(S,S.length-1);
		S += "";
		console.log(S);
		// Primeiramente l� a primeira linha do descritor da tabela (.tmy)
		var strt = lestru(TABELA,S);
		}
	// L� a estrutura de uma tabela
	function lestru(tabela,S){
		var RES = "";
		var myHeaders = new Headers();
		myHeaders.append('Content-Type','text/plain; charset=iso-8859-1');

		fetch(tabela, myHeaders)
			.then(function (response) {
				return response.text();
			})
			.then(function (result) {
				RES = result;
				ob.getById("tbRes").innerText = RES;
				//console.log(result);
				savestrufile(RES,S);
				//return RES;
				});
		return RES;
		}
	// Salva a estrutura, despachando a requisi��o para uma script PHP
	function savestrufile(Resultado,S){
		Resultado = Resultado.replace(/\r/,"");
		var arrRes = Resultado.split("\n");
		console.log("ArrRes: "+arrRes);
		var tab = JSON.parse(arrRes[0]); // Transforma string em objeto
		var obj = JSON.parse(arrRes[1]);
		var NovoArray = [];
		NovoArray.push(tab[0]);
		NovoArray.push(tab[1]);
		NovoArray.push(obj);
		// Array de campos
		var Campos = obj.campos; // N�o ser� usado aqui. Apenas para documenta��o
		// Coleta os campos da tela
		console.log("Flds:"+S);
		var tabela = tab[1].Nome;
		var txtUrl = "TB_fields_add.php?Tabela="+tabela+"&NovoArray="+JSON.stringify(NovoArray)+"&Entrada="+S;
		// Envia URL via fetch para grava��o
		var myHeaders = new Headers();
		myHeaders.append('Content-Type','text/plain; charset=iso-8859-1');
		fetch(txtUrl, myHeaders)
			.then(function (response) {
				return response.text();
			})
			.then(function (result) {
				console.log("Ssf:"+result);
			});		
		}

	// Evento correspondente ao "onload" (carregamento)
	document.addEventListener("DOMContentLoaded", function(e) {
		// Aqui vai o seu c�digo
		init();
		});
	</script>
</head>
<?php
// Mostra a lista de tabelas
function lstTabelas($Bank,$tipoProc){
	$retorno = false;
	// Abre o banco
	$fp = fopen($Bank, "r");
	// Primeira Linha (Cabe�alho banco)
	$linha1 = fgets($fp);
	// Segunda Linha
	$txt = fgets($fp);
	fclose($fp);
	// L� tabelas do Banco
	if( $txt == "" ){
		$retorno = false;
		echo "";
		} else {
		// Existem tabelas
		// Conte�do da linha em string para JSON
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
// Mostra o formul�rio principal
function displayForm($html){
	$cod .= "<form method=\"post\" action =\"\">";
	$cod .= "<select id=tabelas>";
	$cod .= $html;
	$cod .= "</select>";
	$cod .= "</form>";
	$cod .= "<input type=button onclick=\"addline()\" value=\"NOVO campo\">&nbsp;";
	$cod .= "<input type=button onclick=\"savestru()\" value=\"GRAVA\">";
	$cod .= "<div id=\"tbCampos\"></div>";
	return $cod;
	}
	/*
		TB_fields4.php?Banco=<banco>.dmy&Tabela=<tabela>.tmy
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
			} else {
			echo "[{ Id : \"Err\" }, { Erro : \"DB_Err 002:Nome do banco n�o preenchido. Use ?Banco=xxxx.dmy\" }]";
			}
		} else {
			echo "[{ Id : \"Err\" }, { Erro : \"DB_Err 001:Sem nome do banco. Use ?Banco=xxxx.dmy\" }]";
		}
	if( !file_exists($Banco) ){
		echo "[{ Id : \"Err\" }, { Erro : \"DB_Err 400:Banco N�O EXISTE\" }]";
		} else {
		// Existe o par�metro Tabela na URL ?
		if(	isset($_GET["Tabela"]) ){ 
			$Tabela = $_GET["Tabela"];
			// O par�metro Tabela est� preenchido ?
			if( $Tabela != "" ){
				$ext = substr($Tabela,-3);
				// A extens�o � tmy ?
				if( !($ext == "tmy") ){
					$Tabela = $Tabela . ".tmy";
					}
				// O arquivo correspondente a tabela existe ?
				if( !file_exists($Tabela) ){
						echo "TB_Err 007:Tabela n�o existe. Verifique a grafia";
						} else {
						// Acrescenta tabela na lista de tabelas no registro do Banco
						// e mostra formul�rio para inclus�o de campos
						if( lstTabelas($Banco, "testa") ){
							echo "<h1>Tabelas do Banco: " . $Banco . "</h1>";
							echo displayForm(lstTabelas($Banco, "html"));
							//echo "<scr". "ipt type=\"text/javascript\">displayTable();</scr" . "ipt>";
							echo "[{ Id : \"Err\" }, { Erro : \"TB_Err 200:Tabelas lidas com sucesso\" }]";
							} else {
							echo "[{ Id : \"Err\" },{ \"DB_Err 500\" : \"Banco danificado, sem refer�ncia �s tabelas\" } ] } ]";
							}
						}
				} else {
				echo "[{ Id : \"Err\" }, { Erro : \"TB_Err 002:Nome da tabela n�o preenchido. Use ?Tabela=xxxx.tmy\" }]";
				}
			} else {
				echo "[{ Id : \"Err\" }, { Erro : \"TB_Err 001:Sem nome da tabela. Use ?Tabela=xxxx.tmy\" }]";
			}
		}
?>
<div id="tbRes" class="hide"></div>