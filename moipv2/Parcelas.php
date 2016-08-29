<?php 
require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'../../../init.php');
require_once(dirname(__FILE__).'/moipv2.php');
$moipv2 = new Moipv2();
$max = Configuration::get('MOIPV2_CARTAO_NUMBER');

$MOIPV2_CARTAO_PARCEL2 = Configuration::get('MOIPV2_CARTAO_PARCEL2');
$MOIPV2_CARTAO_PARCEL3 = Configuration::get('MOIPV2_CARTAO_PARCEL3');
$MOIPV2_CARTAO_PARCEL4 = Configuration::get('MOIPV2_CARTAO_PARCEL4');
$MOIPV2_CARTAO_PARCEL5 = Configuration::get('MOIPV2_CARTAO_PARCEL5');
$MOIPV2_CARTAO_PARCEL6 = Configuration::get('MOIPV2_CARTAO_PARCEL6');
$MOIPV2_CARTAO_PARCEL7 = Configuration::get('MOIPV2_CARTAO_PARCEL7');
$MOIPV2_CARTAO_PARCEL8 = Configuration::get('MOIPV2_CARTAO_PARCEL8');
$MOIPV2_CARTAO_PARCEL9 = Configuration::get('MOIPV2_CARTAO_PARCEL9');
$MOIPV2_CARTAO_PARCEL10 = Configuration::get('MOIPV2_CARTAO_PARCEL10');
$MOIPV2_CARTAO_PARCEL11 = Configuration::get('MOIPV2_CARTAO_PARCEL11');
$MOIPV2_CARTAO_PARCEL12 = Configuration::get('MOIPV2_CARTAO_PARCEL12');
$oauth = Configuration::get('MOIPV2_OAUTH_DEV');
$valor = 100;
$Method = Tools::getValue('Method');

if($Method == 'cart'){
	$valor = Tools::getValue('price_order');

		$array  = array(
												'2' => getParcelas($valor,$MOIPV2_CARTAO_PARCEL2, 2),
												'3' => getParcelas($valor,$MOIPV2_CARTAO_PARCEL3, 3),
												'4' => getParcelas($valor,$MOIPV2_CARTAO_PARCEL4, 4),
												'5' => getParcelas($valor,$MOIPV2_CARTAO_PARCEL5, 5),
												'6' => getParcelas($valor,$MOIPV2_CARTAO_PARCEL6, 6),
												'7' => getParcelas($valor,$MOIPV2_CARTAO_PARCEL7, 7),
												'8' => getParcelas($valor,$MOIPV2_CARTAO_PARCEL8, 8),
												'9' => getParcelas($valor,$MOIPV2_CARTAO_PARCEL9, 9),
												'10' => getParcelas($valor,$MOIPV2_CARTAO_PARCEL10, 10),
												'11' => getParcelas($valor,$MOIPV2_CARTAO_PARCEL11, 11),
												'12' => getParcelas($valor,$MOIPV2_CARTAO_PARCEL12, 12)
					 );
	if($max != 12){
		$count = $max - 1;
		
		while ($count <= 12) {
			
			unset($array[$count]);
			$count++;
		}
	}
	echo Tools::jsonEncode($array);	
} else {
	$parcela = Tools::getValue('installmentCount');
	$order_ammount = Tools::getValue('order_ammount');
	$array  = array(
						'1' => $valor,
						'2' => getJuros($valor,$MOIPV2_CARTAO_PARCEL2, 2),
						'3' => getJuros($valor,$MOIPV2_CARTAO_PARCEL3, 3),
						'4' => getJuros($valor,$MOIPV2_CARTAO_PARCEL4, 4),
						'5' => getJuros($valor,$MOIPV2_CARTAO_PARCEL5, 5),
						'6' => getJuros($valor,$MOIPV2_CARTAO_PARCEL6, 6),
						'7' => getJuros($valor,$MOIPV2_CARTAO_PARCEL7, 7),
						'8' => getJuros($valor,$MOIPV2_CARTAO_PARCEL8, 8),
						'9' => getJuros($valor,$MOIPV2_CARTAO_PARCEL9, 9),
						'10' => getJuros($valor,$MOIPV2_CARTAO_PARCEL10, 10),
						'11' => getJuros($valor,$MOIPV2_CARTAO_PARCEL11, 11),
						'12' => getJuros($valor,$MOIPV2_CARTAO_PARCEL12, 12),
					);
	if($max != 12){
		$count = $max - 1;
		
		while ($count <= 12) {
			
			unset($array[$count]);
			$count++;
		}
	}
	$installmentAmmount = $array[$parcela];

#	echo $installmentAmmount;
}
 

function getJuros($valor, $juros, $parcela) {
	if($juros){
		$principal = $valor;
		$taxa =  $juros/100;
		$valjuros = ($principal * $taxa)/(1 - (pow(1/(1+$taxa), $parcela)));
		$juros = Tools::convertPrice($valjuros);	
	} else{
		$juros = 0;
	}
	
	return number_format($juros, 2, '', '');
}


function getParcelas($valor, $juros, $parcela) {
	if($juros){
		$principal = $valor;
		$taxa =  $juros/100;
		$valParcela = ($principal * $taxa)/(1 - (pow(1/(1+$taxa), $parcela)));
		return Tools::displayPrice($valParcela);
	} else {

		return Tools::displayPrice($valor);
	}
}



?>