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
 * @since 1.5.0
 */
class Moipv2ValidationModuleFrontController extends ModuleFrontController
{
	public function postProcess()
	{
		$cart = $this->context->cart;
		extract($_POST);
		if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active)
			Tools::redirect('index.php?controller=order&step=1');


		$authorized = false;
		foreach (Module::getPaymentModules() as $module)
			if ($module['name'] == 'moipv2')
			{
				$authorized = true;
				break;
			}


		if (!$authorized)
			die($this->module->l('This payment method is not available.', 'validation'));

		$customer = new Customer($cart->id_customer);


		if (!Validate::isLoadedObject($customer))
			Tools::redirect('index.php?controller=order&step=1');

		

		$currency = $this->context->currency;
		$total = (float)$cart->getOrderTotal(true, Cart::BOTH);
		
		if($method_moip_pay == "BOLETO"){

				$mailVars =	array(
					'method_moip_pay' => $method_moip_pay,
					'{moipv2_name}' => 'Pagamento por Boleto Bancário',
					'{moipv2_link}' => $href_boleto,
				
				);
				$this->module->validateOrder((int)$cart->id, Configuration::get('MOIPV2_STATUS_2'), $total, $this->module->displayName, NULL, $mailVars, (int)$currency->id, false, $customer->secure_key);
				Tools::redirect('index.php?controller=order-confirmation&id_cart='.(int)$cart->id.'&id_module='.(int)$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key.'&moip_key='.$moip_pay.'&paymentMethod='.$method_moip_pay.'&moip_status='.$moip_status.'&redirectURI='.$href_boleto.'&code_line='.$code_line_moip);

		} elseif($method_moip_pay == "ONLINE_BANK_DEBIT") {

				$mailVars =	array(
						'method_moip_pay' => $method_moip_pay,
						'{moipv2_name}' => 'Pagamento por Transferẽncia Bancária',
						'{moipv2_link}' => $href_bank,
						
					);
				$this->module->validateOrder((int)$cart->id, Configuration::get('MOIPV2_STATUS_2'), $total, $this->module->displayName, NULL, $mailVars, (int)$currency->id, false, $customer->secure_key);
				Tools::redirect('index.php?controller=order-confirmation&id_cart='.(int)$cart->id.'&id_module='.(int)$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key.'&moip_key='.$moip_pay.'&paymentMethod='.$method_moip_pay.'&moip_status='.$moip_status.'&redirectURI='.$href_bank);

		} elseif($method_moip_pay == "ERRO") {
				
					Tools::redirect('index.php?controller=order-confirmation&id_cart='.(int)$cart->id.'&id_module='.(int)$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key.'&paymentMethod=ERRO');
		} else {
				
				$mailVars =	array(
						'method_moip_pay' => $method_moip_pay,
						'{moipv2_name}' => 'Cartão de Crédito',					
					);
				$this->module->validateOrder((int)$cart->id, Configuration::get('MOIPV2_STATUS_2'), $total, $this->module->displayName, NULL, $mailVars, (int)$currency->id, false, $customer->secure_key);
				Tools::redirect('index.php?controller=order-confirmation&id_cart='.(int)$cart->id.'&id_module='.(int)$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key.'&moip_key='.$moip_pay.'&paymentMethod='.$method_moip_pay.'&moip_status='.$moip_status);			
		}

	}

}
