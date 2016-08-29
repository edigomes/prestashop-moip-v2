<?php 
sleep(30);
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/moipv2.php');



$inputJSON = file_get_contents('php://input');
$input= json_decode( $inputJSON, TRUE );

$order_identificador = $input['resource']['order']['ownId'];
$order_status = $input['resource']['order']['status'];

if($order_status == "AUTHORIZED"){
	$tatus = Configuration::get('MOIPV2_STATUS_1');
} elseif ($order_status == "CANCELLED") {
	$tatus = Configuration::get('MOIPV2_STATUS_5');
} else{
	break;
}

			$order_identificador_id = Db::getInstance()->getValue('SELECT `id_order` FROM `ps_orders` WHERE `id_cart` LIKE "'.$order_identificador.'"');
			Logger::addLog("Retorno do pedido:".$order_identificador_id ,1);
			Logger::addLog("Cart id:".$order_identificador ,1);
			Logger::addLog($inputJSON,1);
 			$order = new Order($order_identificador_id);
            $history = new OrderHistory();
            $history->id_order = intval($order->id);
            $history->changeIdOrderState($tatus, intval($order->id));
?>