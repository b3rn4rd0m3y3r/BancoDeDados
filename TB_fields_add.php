<?php
//echo file_get_contents( 'php://input' );
$Tabela = $_GET["Tabela"];
// Entrada dos campos preenchidos
$NovosCampos = $_GET["Entrada"];
// Campos jс existentes
$Novo = $_GET["NovoArray"];
// String para JSON
$jNovo = json_decode($Novo);
$Nome = $jNovo;
$CamposExistentes = $Nome[2]->campos;
echo var_dump($CamposExistentes);
// Novos campos convertidos para JSON
 $arrNewFlds = json_decode($NovosCampos,true);
echo $NovosCampos;
print_r($arrNewFlds);
// Gravaчуo
$txt = "[" . json_encode($Nome[0]) . "," . json_encode($Nome[1]). "]\n";
$fp = fopen($Tabela, "w");
fwrite($fp, $txt);
$txt = "{ \"campos\": [" . $NovosCampos . "] }";
fwrite($fp, $txt);
fclose($fp);
?>