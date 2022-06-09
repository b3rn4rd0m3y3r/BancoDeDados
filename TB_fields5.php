<head>
	<link rel="icon"       type="image/ico"       href="favicon.ico">
	<style>
		DIV.hide {display: none;}
	</style>
	<!--
			FUNÇÕES DA SCRIPT
			
		init								Função disparada quando o fonte está todo carregado ([1]DOM e [2]PHP)
		addline							Adiciona uma linha à tabela (uma linha de grid de INPUTs)
		displayTable					Mostra a tabela de campos na TELA
		lestru(tabela,S)				Lê a estrutura de uma tabela
		loadStru(tabela)				Carrega a estrutura da tabela nos campos do grid (TELA)
		savestru						Salva estes campos da TELA em forma de estrutura no arquivo ".tmy"
		savestrufile(Resultado,S)	Salva a estrutura, despachando a requisição para uma script PHP
	-->
	<script type="text/javascript" src="BMyFrmwk.js"></script>
	<script type="text/javascript">
	// Handle para o framework BMy
	var ob = new BMy();
	// Lê parâmetro "Tabela" na URL
	var url = window.location;
	var sUrl = url.search;
	var pos = url.search.search(/Tabela/);
	var TABELA = sUrl.substr(pos+7,99);
	var S = "";
	// Função disparada quando o fonte está todo carregado (DOM e PHP)
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
	// Adiciona uma linha à tabela
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
		// Handle para tabela física no DOM
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
		// Primeiramente lê a primeira linha do descritor da tabela (.tmy)
		var strt = lestru(TABELA,S);
		}
	// Lê a estrutura de uma tabela
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
	// Salva a estrutura, despachando a requisição para uma script PHP
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
		var Campos = obj.campos; // Não será usado aqui. Apenas para documentação
		// Coleta os campos da tela
		console.log("Flds:"+S);
		var tabela = tab[1].Nome;
		var txtUrl = "TB_fields_add.php?Tabela="+tabela+"&NovoArray="+JSON.stringify(NovoArray)+"&Entrada="+S;
		// Envia URL via fetch para gravação
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
		// Aqui vai o seu código
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
	// Primeira Linha (Cabeçalho banco)
	$linha1 = fgets($fp);
	// Segunda Linha
	$txt = fgets($fp);
	fclose($fp);
	// Lê tabelas do Banco
	if( $txt == "" ){
		$retorno = false;
		echo "";
		} else {
		// Existem tabelas
		// Conteúdo da linha em string para JSON
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
// Mostra o formulário principal
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
	if( !file_exists($Banco) ){
		echo "[{ Id : \"Err\" }, { Erro : \"DB_Err 400:Banco NÃO EXISTE\" }]";
		} else {
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
						echo "TB_Err 007:Tabela não existe. Verifique a grafia";
						} else {
						// Acrescenta tabela na lista de tabelas no registro do Banco
						// e mostra formulário para inclusão de campos
						if( lstTabelas($Banco, "testa") ){
							echo "<h1>Tabelas do Banco: " . $Banco . "</h1>";
							echo displayForm(lstTabelas($Banco, "html"));
							//echo "<scr". "ipt type=\"text/javascript\">displayTable();</scr" . "ipt>";
							echo "[{ Id : \"Err\" }, { Erro : \"TB_Err 200:Tabelas lidas com sucesso\" }]";
							} else {
							echo "[{ Id : \"Err\" },{ \"DB_Err 500\" : \"Banco danificado, sem referência às tabelas\" } ] } ]";
							}
						}
				} else {
				echo "[{ Id : \"Err\" }, { Erro : \"TB_Err 002:Nome da tabela não preenchido. Use ?Tabela=xxxx.tmy\" }]";
				}
			} else {
				echo "[{ Id : \"Err\" }, { Erro : \"TB_Err 001:Sem nome da tabela. Use ?Tabela=xxxx.tmy\" }]";
			}
		}
?>
<div id="tbRes" class="hide"></div>