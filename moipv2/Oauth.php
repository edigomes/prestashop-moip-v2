<?php
/*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @deprecated 1.5.0 This file is deprecated, use moduleFrontController instead
 */

@error_reporting(E_ALL);
@ini_set("display_errors",1);

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/moipv2.php');



$context = Context::getContext();
	
extract($_GET);
$moipv2 = new Moipv2();




	
	if($token == Configuration::get('MOIPV2_KEY_TOKEN')){
		if(Configuration::get('MOIPV2_ENDPOINT') == 1){
			$oauth = getOauthAcess($code);
			$decode_json = json_decode($oauth, true);
			$oauth_access = $decode_json['access_token'];
			Configuration::updateValue('MOIPV2_OAUTH_DEV', $oauth_access);
			
			$public_key_json = getKey($oauth_access);
			
			Configuration::updateValue('MOIPV2_PUBLICKEY_DEV', $public_key_json);
			
			EnableWebHooks();

			if($oauth) {
				echo "<h1>Autorização realizada com sucesso, por favor, realize os testes na loja.";
			}
			
		} else {
			$oauth = getOauthAcess($code);
			$decode_json = json_decode($oauth, true);
			$oauth_access = $decode_json['access_token'];
			Configuration::updateValue('MOIPV2_OAUTH_PROD', $oauth_access);
			
			$public_key_json = getKey($oauth_access);
			
			Configuration::updateValue('MOIPV2_PUBLICKEY_PROD', $public_key_json);
			
			EnableWebHooks();
			if($oauth) {
				echo "<h1>Autorização realizada com sucesso, por favor, realize os testes na loja.";
			}
		}
	}


 	function getOauthAcess($code) {
		$documento = 'Content-Type: application/json; charset=utf-8';
		$moipv2 = new Moipv2();
		if (Configuration::get('MOIPV2_ENDPOINT') == 1) {
		 $url = "https://sandbox.moip.com.br/oauth/accesstoken";
			$header = "Authorization: Basic OE9LTFFGVDVYUVpYVTdDS1hYNDNHUEpPTUlKUE1TTUY6TlQwVUtPWFM0QUxOU1ZPWEpWTlhWS1JMRU9RQ0lUSEk1SERLVzNMSQ==";
			$array_json = array(
		    	'appId' => 'APP-0KPSXJOVUGFI',
		    	'appSecret' => '5g66zd5lcfk2j8vbb7q74ahu5owv2z5',
				'redirectUri' => 'http://moip.o2ti.com/prestashop/redirect/',
				'grantType' => 'authorization_code',
				'code' => $code
			);
			$json = json_encode($array_json);

		}
		  else {
		      $url = "https://api.moip.com.br/oauth/accesstoken";
		        $header = "Authorization: Basic RVZDSEJBVU1LTTBVNEVFNFlYSUE4Vk1DMEtCRVBLTjI6NE5FQ1A2MkVLSThIUlNNTjNGR1lPWk5WWVpPTUJEWTBFUUhLOU1ITw==";
		        $array_json = array(
			        	'appId' => 'APP-4WORRHSEHO5U',
			        	'appSecret' => '977hq8wowu9mfi0bjsmkc0j71179oxa',
						'redirectUri' => 'http://moip.o2ti.com/prestashop/redirect/',
						'grantType' => 'authorization_code',
						'code' => $code
		        	);
		        	$json = json_encode($array_json);
		}
		$result = array();
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 6000);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array($header, $documento));
		curl_setopt($ch,CURLOPT_USERAGENT,'MoipPrestashop/2.0.0');

		$res = curl_exec($ch);
		
		curl_close($ch);
		 	
		return $res;
	}
	
	 function getKey($oauth) {
		$documento = 'Content-Type: application/json; charset=utf-8';
		if (Configuration::get('MOIPV2_ENDPOINT') == 1) {
		      $url = "https://sandbox.moip.com.br/v2/keys/";
		   $header = "Authorization: OAuth " . $oauth;
		} else {
		    $url = "https://api.moip.com.br/v2/keys/";
		    $header = "Authorization: OAuth " . $oauth;
		}
		$result = array();
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array($header));
		curl_setopt($ch,CURLOPT_USERAGENT,'MoipPrestashop/2.0.0');
		$responseBody = curl_exec($ch);
		curl_close($ch);
		$responseBody = json_decode($responseBody, true);
		$_key = $responseBody['keys']['encryption'];
		return $_key;
	}

	function EnableWebHooks(){
			$status_controller = array("ORDER.*","REFUND.REQUESTED");
			$webhooks = array(
				"events" => $status_controller,
				"target" =>  _PS_BASE_URL_.__PS_BASE_URI__."/modules/moipv2/Webhooks.php",
				"media" => "WEBHOOK"
			);

			if (Configuration::get('MOIPV2_ENDPOINT') == 1) {
	          	$url = "https://sandbox.moip.com.br/v2/preferences/notifications/";
	        	$oauth = Configuration::get('MOIPV2_OAUTH_DEV');
                $header = "Authorization: OAuth {$oauth}";
                $documento = "Content-Type: application/json";
		    } else {
	        	$url = "https://api.moip.com.br/v2/preferences/notifications/";
				$oauth = Configuration::get('MOIPV2_OAUTH_PROD');
                $header = "Authorization: OAuth {$oauth}";
                $documento = "Content-Type: application/json";
		    }

		    $json = json_encode($webhooks);
		    
			$result = array();
	    	$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$url);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0); 
			curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array($header, $documento));
			curl_setopt($ch, CURLOPT_USERAGENT,'MoipPrestashop/2.0.0');
			
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			$res = curl_exec($ch);
			$info = curl_getinfo($ch);
		 	curl_close($ch);
		 	$responseBody = json_decode($res, true);

		 	$result = array('header' => array($header, $documento),
		 					'url' => $url,
		 					"json_send" => $webhooks,
		 					"responseBody" => $responseBody,
		 					"responseCode" => $info

		 					);
		 	return $json_debug = json_encode($result);
		   
	}
?>

