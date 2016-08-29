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
class Moipv2AuthorizationModuleFrontController extends ModuleFrontController
{
	public $ssl = true;
	public $display_column_left = false;

	/**
	 * @see FrontController::initContent()
	 */


    public function generateRuleMoip($code)
    {
    
    $cartRule = new CartRule(CartRule::getIdByCode($code));
    $this->context->cart->addCartRule($cartRule->id);
    return;
   }

    public function initContent()
        {

            
            parent::initContent();
            extract($_POST);
            Logger::addLog("something FrontController",1);
            #Logger::addLog(json_decode($_POST),1);
            
            $moipv2 = new Moipv2();

            if($paymentMethod =='BOLETO' && Configuration::get('MOIPV2_BOLETODISCOUNT') == 1){
                $this->generateRuleMoip(Configuration::get('MOIPV2_CUPOMBOLETO'));    
            }
            
            $json_order = $this->getOrderCreateMoip($_POST);
            $moip_order = $moipv2->getOrderIdMoip($json_order);
            if(isset($moip_order->id)){          
                $json = $this->getPaymentJson($_POST);
                
                $PAY_MOIP = $this->getPaymentMoIP($moip_order->id, $json);
            } 
            $cart = $this->context->cart;
            if (!$this->module->checkCurrency($cart))
                    Tools::redirect('index.php?controller=order');

             if(isset($PAY_MOIP->id) && isset($moip_order->id)){
                    if($paymentMethod == "BOLETO"){
                        $array_assign = array(
                            'nbProducts' => $cart->nbProducts(),
                            'codigo_moip' => (string)$PAY_MOIP->id,
                            'method_moip_pay' => $paymentMethod,
                            'status_moip' => (string)$PAY_MOIP->status,
                            'href_boleto' => (string)$PAY_MOIP->_links->payBoleto->redirectHref,
                            'code_line_moip' => (string)$PAY_MOIP->fundingInstrument->boleto->lineCode,
                            'cust_currency' => $cart->id_currency,
                            'currencies' => $this->module->getCurrency((int)$cart->id_currency),
                            'total' => $cart->getOrderTotal(true, Cart::BOTH),
                            'isoCode' => $this->context->language->iso_code,
                            'moipv2Name' => $this->module->moipv2Name,
                            'redir_automatic' =>  Configuration::get('MOIPV2_REDIR_BOLETO'),
                            'this_path' => $this->module->getPathUri(),
                            'this_path_moipv2' => $this->module->getPathUri(),
                            'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->module->name.'/'
                        );
                        $this->context->smarty->assign($array_assign);
                        
                    } elseif($paymentMethod == "ONLINE_BANK_DEBIT") {
                        if($paymentForm == "041"){
                            $bank_url = $PAY_MOIP->_links->payOnlineBankDebitBanrisul->redirectHref;
                            //banrisul

                        } elseif($paymentForm == "341" ){
                            $bank_url = $PAY_MOIP->_links->payOnlineBankDebitItau->redirectHref;
                            //itau
                        } elseif($paymentForm == "237"){
                            $bank_url = $PAY_MOIP->_links->payOnlineBankDebitBradesco->redirectHref;
                            //bradesco
                        } else{
                            $bank_url = $PAY_MOIP->_links->payOnlineBankDebitBB->redirectHref;
                            //bb
                        }
                        $array_assign = array(
                            'nbProducts' => $cart->nbProducts(),
                            'codigo_moip' => (string)$PAY_MOIP->id,
                            'method_moip_pay' => (string)$paymentMethod,
                            'status_moip' => (string)$PAY_MOIP->status,
                            'bank_name_moip' => (string)$PAY_MOIP->fundingInstrument->onlineBankDebit->bankName,
                            'href_bank' => (string)$bank_url,
                            'cust_currency' => $cart->id_currency,
                            'currencies' => $this->module->getCurrency((int)$cart->id_currency),
                            'total' => $cart->getOrderTotal(true, Cart::BOTH),
                            'isoCode' => $this->context->language->iso_code,
                            'moipv2Name' => $this->module->moipv2Name,
                            'redir_automatic' =>  Configuration::get('MOIPV2_REDIR_TEF'),
                            'this_path' => $this->module->getPathUri(),
                            'this_path_moipv2' => $this->module->getPathUri(),
                            'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->module->name.'/'
                        );
                        $this->context->smarty->assign($array_assign);

                    } else {

                        $decode_dump = json_decode($PAY_MOIP);
                        $array_assign = array(
                            'dump' => $PAY_MOIP,
                            'nbProducts' => $cart->nbProducts(),
                            'codigo_moip' => (string)$PAY_MOIP->id,
                            'method_moip_pay' => $paymentMethod,
                            'status_moip' => (string)$PAY_MOIP->status,
                            'installmentCount' => (string)$PAY_MOIP->installmentCount,
                            'brand' => (string)$PAY_MOIP->fundingInstrument->creditCard->brand,
                            'first6' => (string)$PAY_MOIP->fundingInstrument->creditCard->first6,
                            'last4' => (string)$PAY_MOIP->fundingInstrument->creditCard->last4,
                            'cust_currency' => $cart->id_currency,
                            'currencies' => $this->module->getCurrency((int)$cart->id_currency),
                            'total' => $cart->getOrderTotal(true, Cart::BOTH),
                            'isoCode' => $this->context->language->iso_code,
                            'moipv2Name' => $this->module->moipv2Name,
                            
                            'this_path' => $this->module->getPathUri(),
                            'this_path_moipv2' => $this->module->getPathUri(),
                            'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->module->name.'/'
                        );
                        $this->context->smarty->assign($array_assign);

                    }
             } else {
                $erro_order = "";
                $erro = "";
                
                if(isset($moip_order->errors)){
                    foreach ($moip_order->errors as $key => $value) {
                            $erro_order = $value->description;
                    }
                        
                        
                } elseif(isset($PAY_MOIP->errors)) {
                    foreach ($PAY_MOIP->errors as $key => $value) {
                        $erro = $value->description;
                    }
                }
                    
                    $this->context->smarty->assign(array(
                            'nbProducts' => $cart->nbProducts(),
                            'method_moip_pay' => 'ERRO',
                            'erro_message' => $erro_order.' '.$erro,
                            'cust_currency' => $cart->id_currency,
                            'currencies' => $this->module->getCurrency((int)$cart->id_currency),
                            'total' => $cart->getOrderTotal(true, Cart::BOTH),
                            'isoCode' => $this->context->language->iso_code,
                            'moipv2Name' => $this->module->moipv2Name,

                            'this_path' => $this->module->getPathUri(),
                            'this_path_moipv2' => $this->module->getPathUri(),
                            'this_path_ssl' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.'modules/'.$this->module->name.'/'
                        ));
             }
            
            # var_dump($json_order);
            # var_dump($moip_order);
            # var_dump($PAY_MOIP);
            # var_dump($json);
            $this->setTemplate('payment_execution.tpl');
        }
	public function getPaymentJson($pagamento) {
                extract($pagamento);
                if($paymentMethod == "BOLETO"){
                    $array_payment = array(
                            "fundingInstrument" => array(
                                            "method" => "BOLETO",
                                            "boleto" => array(
                                                    "expirationDate"=> $this->getDataVencimento(3),
                                                    "instructionLines"=> array (
                                                                    "first" => "Pagamento do pedido na loja: do pedido",
                                                                    "second" => "Não Receber após o Vencimento",
                                                                    "third" => "+ Info em: url_do_site"
                                                            ),
                                                    ),
                                            ),
                            );
                } elseif ($paymentMethod  == "ONLINE_BANK_DEBIT") {
                    $array_payment = array(
                            "fundingInstrument" => array(
                                        "method" => "ONLINE_BANK_DEBIT",
                                            "onlineBankDebit" => array(
                                                            "bankNumber" => $paymentForm,
                                                            "expirationDate" =>  $this->getDataVencimento(3),
                                                            "returnUri" =>  "https://"
                                                        ),
                                            ),
                        );
                } else {
                        $array_payment = array(
                                    "installmentCount" => $parcelamentoCartao,
                                    "fundingInstrument" =>
                                                array(
                                                    "method" => "CREDIT_CARD",
                                                    "creditCard" =>
                                                                    array(
                                                                        "hash" => $paymentHASH,
                                                                        "holder" =>
                                                                        array(
                                                                            "fullname" => $nomePortador,
                                                                            "birthdate" =>  date('Y-m-d', strtotime($dataPortador)),
                                                                            "taxDocument" =>
                                                                                    array(
                                                                                          "type" => "CPF",
                                                                                          "number"=> preg_replace("/[^0-9]/", "", $cpfPortador)
                                                                                    ),
                                                                            "phone" =>
                                                                                array(
                                                                                    "countryCode" => "55",
                                                                                    "areaCode" => $this->getNumberOrDDD($telefonePortador, true),
                                                                                    "number" =>   $this->getNumberOrDDD($telefonePortador)
                                                                                ),
                                                                         ),
                                                                    ),
                                                  ),
                            );
                }
                $json = json_encode($array_payment);
                return $json;
        }
        public function getNumberOrDDD($param_telefone, $param_ddd = false) {
                $cust_ddd = '11';
                $cust_telephone = preg_replace("/[^0-9]/", "", $param_telefone);
                $st = strlen($cust_telephone) - 8;
                if ($st > 0) {
                    $cust_ddd = substr($cust_telephone, 0, 2);
                    $cust_telephone = substr($cust_telephone, $st, 8);
                }

                if ($param_ddd === false) {
                    $retorno = $cust_telephone;
                } else {
                    $retorno = $cust_ddd;
                }

                return $retorno;
        }
       
        public function getDataVencimento($NDias) {
	            $DataAct = date("Ymd");
	            $d = new DateTime( $DataAct );
	            $t = $d->getTimestamp();
	            for($i=0; $i<$NDias; $i++){
	                $addDay = 86400;

	                $nextDay = date('w', ($t+$addDay));
	                if($nextDay == 0 || $nextDay == 6) {
	                    $i--;
	                }
	                $t = $t+$addDay;
	            }
	            $d->setTimestamp($t);
	            return $d->format( 'Y-m-d' );
	    }

        public function getPaymentMoIP($IdMoip, $json) {
           
	            $documento = 'Content-Type: application/json; charset=utf-8';
               if(Configuration::get('MOIPV2_ENDPOINT') == 1){
                    $url = "https://sandbox.moip.com.br/v2/orders/{$IdMoip}/payments";
                    $oauth = Configuration::get('MOIPV2_OAUTH_DEV');
                } else {
                        $url = "https://api.moip.com.br/v2/orders/{$IdMoip}/payments";
                        $oauth = Configuration::get('MOIPV2_OAUTH_PROD');
                }


                $header = "Authorization: OAuth " . $oauth;
	            
	            $result = array();
	             $ch = curl_init();
	             curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	            curl_setopt($ch, CURLOPT_URL, $url);
	            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
	            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	            curl_setopt($ch, CURLOPT_HTTPHEADER, array($header, $documento));
	            curl_setopt($ch,CURLOPT_USERAGENT,'MoipPrestashop/2.0.0');
	            $responseBody = curl_exec($ch);
	            curl_close($ch);
	            $decode = json_decode($responseBody);

	         return $decode;
	    }

        public function getOrderCreateMoip($dado_payment){
            extract($dado_payment);
          
            $moipv2 = new Moipv2();
            $cart_itens = $this->context->cart->getProducts();
            $produc_itens = $this->getListaProdutos($cart_itens);
            $shipping_price = $this->context->cart->getTotalShippingCost(null, true);
            $total_order = $this->context->cart->getOrderTotal(true, Cart::BOTH);
            $price_ajust = $this->autidaOrder($cart_itens, $shipping_price, $total_order);

            if($paymentMethod == "CREDIT_CARD"){


                    if($price_ajust > 0){
                        $valor_juros_parcela = $this->getValueParc($this->context->cart->getOrderTotal(true, Cart::BOTH), $parcelamentoCartao);
                        $addtion = $price_ajust + $valor_juros_parcela;
                            if($price_ajust > 0){
                                    $addtion = $addtion;
                                    $discount = 000;
                            } else {
                                    $addtion = $addtion;
                                    $discount = $price_ajust;
                            }
                    } else {
                        
                        $valor_juros_parcela = $this->getValueParc($this->context->cart->getOrderTotal(true, Cart::BOTH), $parcelamentoCartao);

                        $addtion = $valor_juros_parcela;
                        $discount = $price_ajust;
                    }


            } else {
                if($price_ajust > 0){
                        $addtion = $price_ajust;
                        $discount = 000;
                } else {
                        $addtion = 000;
                        $discount = $price_ajust;
                }
            }
            $address =  new Address($this->context->cart->id_address_invoice);
            $customer = new Customer(Context::getContext()->cookie->id_customer);

            // INICIO - definição do atributo para o documento cpf...  altere caso necessário para o seu atributo.

             if(isset($customer->document)){
           
                $taxvat = $customer->document;
             } elseif(isset($customer->taxvat)){
                $taxvat = $customer->taxvat;
             } else{
                $taxvat = '000.000.000-00';
             }
           
             // FIM - definição do atributo para o documento cpf...  altere caso necessário para o seu atributo.

            Logger::addLog("Cart id do pedido:".(int)$this->context->cart->id ,1);
            $prestashopState = $this->getPrestaShopState($address->id_state);
            $addressUF = $prestashopState['iso_code'];
            $array_order = array(
                                            "ownId" => (int)$this->context->cart->id,
                                            "amount" => array(
                                                                                "currency" => "BRL",
                                                                                "subtotals" =>
                                                                                                            array(
                                                                                                                    "shipping"=> number_format($shipping_price, 2, '', ''),
                                                                                                                    "discount"=> abs($discount),
                                                                                                                    "addition" => $addtion
                                                                                                                    ),
                                                                            ),
                                            "items" => $produc_itens,
                                            "customer" => array(
                                                                             "ownId" => $customer->email,
                                                                                      "fullname" =>$address->firstname .' '.$address->lastname,
                                                                                      "email" => $customer->email,
                                                                                      "birthDate" => $customer->birthday,
                                                                                        "taxDocument" => array(
                                                                                                                            "type" => "CPF",
                                                                                                                            "number" => preg_replace("/[^0-9]/", "", $taxvat)
                                                                                                         ),
                                                                             "phone"  => array(
                                                                                        "countryCode" =>"55",
                                                                                        "areaCode" => $this->getNumberOrDDD($address->phone, true),
                                                                                        "number"  => $this->getNumberOrDDD($address->phone)
                                                                              ),
                                                                              "shippingAddress" =>    array(
                                                                                                                                        "street" => $address->address1,
                                                                                                                                        "streetNumber" => $this->getNumEndereco($address->address1),
                                                                                                                                        "complement" => $this->getNumEndereco($address->address2),
                                                                                                                                        "district" => $address->address2,
                                                                                                                                        "city" => $address->city,
                                                                                                                                        "state" => $addressUF,
                                                                                                                                        "country" =>"BRA",
                                                                                                                                        "zipCode" => $address->postcode
                                                                                                                                    ),
                                                                    )
                                        );
            $json_order = json_encode($array_order);
        return $json_order;
    }
		

    public function getValueParc($valor, $parcela)
    {
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
        $array  = array(
                        '1' => 0,
                        '2' => $this->getJuros($valor,$MOIPV2_CARTAO_PARCEL2, 2),
                        '3' => $this->getJuros($valor,$MOIPV2_CARTAO_PARCEL3, 3),
                        '4' => $this->getJuros($valor,$MOIPV2_CARTAO_PARCEL4, 4),
                        '5' => $this->getJuros($valor,$MOIPV2_CARTAO_PARCEL5, 5),
                        '6' => $this->getJuros($valor,$MOIPV2_CARTAO_PARCEL6, 6),
                        '7' => $this->getJuros($valor,$MOIPV2_CARTAO_PARCEL7, 7),
                        '8' => $this->getJuros($valor,$MOIPV2_CARTAO_PARCEL8, 8),
                        '9' => $this->getJuros($valor,$MOIPV2_CARTAO_PARCEL9, 9),
                        '10' => $this->getJuros($valor,$MOIPV2_CARTAO_PARCEL10, 10),
                        '11' => $this->getJuros($valor,$MOIPV2_CARTAO_PARCEL11, 11),
                        '12' => $this->getJuros($valor,$MOIPV2_CARTAO_PARCEL12, 12),
                    );
        $installmentAmmount = $array[$parcela];
    return $installmentAmmount;
    }


    function getJuros($valor, $juros, $parcela) {
        $principal = $valor;
        $taxa =  $juros/100;
        $valjuros = ($principal * $taxa)/(1 - (pow(1/(1+$taxa), $parcela)));
        $juros_total = ($valjuros * $parcela) - $principal;
        $juros = Tools::convertPrice($juros_total);
        return number_format($juros, 2, '', '');
    }



    public function getListaProdutos($cart_itens) {
           
                            foreach ($cart_itens as $itemId => $item)
                            {
                                if($item['total'] > 0){
                                   $produtos[] = array (
                                    'product' => $item['name'],
                                    'quantity' => $item['cart_quantity'],
                                    'detail' => $item['reference'],
                                    'price' => number_format($item['total'], 2, '', '')
                                    );
                               }

                            }
            return $produtos;
     }



     public function getNumEndereco($endereco) {
            $numEnderecoDefault= '0';
            $numEndereco = trim(preg_replace("/[^0-9]/", "", $endereco));
            if($numEndereco)
                return($numEndereco);
            else
                return($numEnderecoDefault);
    }

    

     public function getPrestaShopState($id_state) {
        $rq = Db::getInstance()->getRow('
        SELECT `name`, `iso_code` FROM `' . _DB_PREFIX_ . 'state`
        WHERE id_state = \'' . pSQL($id_state) . '\'');
        return $rq;
    }

    public function autidaOrder($produc_itens, $shipping_price, $total_order)
    {
        $price_prod = array();
      foreach ($produc_itens as $itemId => $item) {
        if($item['total'] > 0){
             $price_prod[] = $item['total'];
        }
      }
      $preco_prods = array_sum($price_prod);
      $prod_shipping = $preco_prods + $shipping_price;

        if($prod_shipping != $total_order){
            if($total_order > $prod_shipping){
                $addition_price = $total_order - $prod_shipping;
                return number_format($addition_price, 2, '', '');
            } else {
                $discount_order = $total_order - $prod_shipping;
                return number_format($discount_order, 2, '', '');
            }
        }
    }
}
