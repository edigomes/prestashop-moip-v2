
{*
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
*}

<p class="payment_module">
<a href="#formulario" title="{l s='pagamento via moip' mod='moipv2'}" class="moipv2">
        Pagamento via Moip S/A
    </a>

    
<form action="{$link->getModuleLink('moipv2', 'authorization', [], true)|escape:'html'}"  class="formulario" id="formulario" method="POST">
	

	<link rel="stylesheet" type="text/css" href="{$modules_dir}/moipv2/css/default.css" />
     <script type="text/javascript" src="https://assets.moip.com.br/integration/moip.min.js"></script>
     <script type="text/javascript" src="{$modules_dir}moipv2/script/jquery.validate.js"></script>
     <script type="text/javascript" src="{$modules_dir}moipv2/script/jquery.maskedinput.js"></script>
     <script type="text/javascript" src="{$modules_dir}moipv2/script/moip.js"></script>
		

        <input type="hidden" name="paymentForm" value="" />
        <input type="hidden" name="paymentMethod" value="" />
        <input type="hidden" name="paymentBank" value=""/>
       	<input type="hidden" name="paymentIdMoip" value=""/>
       	<input type="hidden" name="paymentHASH" id="paymentHASH" value=""/>
        <input type="hidden" name="paymentUrl" value="{$modules_dir|escape:'html'}"/>
        <input type="hidden" name="paymentOrderValue" value="{$orderValueBr}"/>
        
        <textarea id="id-chave-publica" class="chave-publica-moip" style="display:none !important;" autocomplete="off">{$publickey|escape:'html'}</textarea>

    {literal}
        <script type="text/javascript">
            $(document).ready(function(){
                MoipPagamentos();
                calcParcela();
            });
        </script>
    {/literal}
    {if ($MOIPV2_CARTAO_ACEITE)}
      <div class="moipay">

                    <legend>Pagar com Cartão de Crédito em até 12 vezes.</legend>

                    <label class="pchoice" title="Pagar com VISA">
                        <input type="radio" name="payment" value="CREDIT_CARD" id="Visa" />
                        <img src="{$modules_dir}moipv2/images/visa.png" alt="Visa">
                    </label>
                    <label class="pchoice" title="Pagar com MasterCard">
                        <input type="radio" name="payment" value="CREDIT_CARD" id="Mastercard" />
                        <img src="{$modules_dir}moipv2/images/master.png" alt="Mastercard">
                    </label>
                    <label class="pchoice" title="Pagar com ELO">
                        <input type="radio" name="payment" value="CREDIT_CARD" id="ELO" />
                        <img src="{$modules_dir}moipv2/images/ELO.png" alt="ELO">
                    </label>
                    <label class="pchoice" title="Pagar com AmericanExpress">
                        <input type="radio" name="payment" value="CREDIT_CARD" id="AmericanExpress" />
                        <img src="{$modules_dir}moipv2/images/american.png" alt="AmericanExpress">
                    </label>
                    <label class="pchoice" title="Pagar com Dinners">
                        <input type="radio" name="payment" value="CREDIT_CARD" id="Dinners" />
                        <img src="{$modules_dir}moipv2/images/dinners.png" alt="Dinners">
                    </label>
                    <label class="pchoice" title="Pagar com Hipercard">
                        <input type="radio" name="payment" value="CREDIT_CARD" id="Hipercard"/>
                        <img src="{$modules_dir}moipv2/images/hiper.png" alt="Hipercard">
                    </label>
                    <label class="pchoice" title="Pagar com Hipper">
                        <input type="radio" name="payment" value="CREDIT_CARD" id="Hipper"/>
                        <img src="{$modules_dir}moipv2/images/hipper.jpg" alt="Hipper">
                    </label>

                    <div class="escolha payform" id="CREDIT_CARD">
                        <ul id="alert-area">

                        </ul>
                            <legend>Dados do cartão</legend>
                            <ul>
                                <li>
                                    <label>Número de parcelas</label>
                                    <select name="parcelamentoCartao" id="parcelamentoCartao">
                                        <option value="1" label="Pagamento à vista" title="Parcela única de R$ {$orderValueBr}">Pagamento à vista</option>
                                    </select>
                                </li>
                                <li class="parcelamentoCartao">Parcela única de R$ {$orderValueBr}</li>
                                <br class="clear">
                                <li>
                                    <label>Número do cartão</label>
                                    <input type="text"  id="cartaoNumero" required class="required input" />
                                </li>
                                <li>
                                    <label>Validade</label>
                                     <select name="cartaoMes" id="cartaoMes" class="required input">
                                        <option value="" selected="">Mês</option>
                                        <option value="01">01</option>
                                        <option value="02">02</option>
                                        <option value="03">03</option>
                                        <option value="04">04</option>
                                        <option value="05">05</option>
                                        <option value="06">06</option>
                                        <option value="07">07</option>
                                        <option value="08">08</option>
                                        <option value="09">09</option>
                                        <option value="10">10</option>
                                        <option value="11">11</option>
                                        <option value="12">12</option>
                                    </select>
                                    /
                                    <select name="cartaoAno" id="cartaoAno" class="required input">
                                        <option value="" selected="">Ano</option>
                                        <option value="15">2015</option>
                                        <option value="16">2016</option>
                                        <option value="17">2017</option>
                                        <option value="18">2018</option>
                                        <option value="19">2019</option>
                                        <option value="20">2020</option>
                                        <option value="21">2021</option>
                                        <option value="22">2022</option>
                                        <option value="23">2023</option>
                                        <option value="24">2024</option>
                                        <option value="25">2025</option>
                                        <option value="26">2026</option>
                                        <option value="27">2027</option>
                                        <option value="28">2028</option>
                                        <option value="29">2029</option>
                                        <option value="30">2030</option>
                                    </select>
                                </li>
                                <li>
                                    <label>Código de segurança (CVV)</label>
                                    <input type="text"  id="segurancaNumero" size="4" class="required input" />
                                </li>
                            </ul>
                            <br><br>
                            <legend>Dados do titular</legend>
                            <ul>
                                <li>
                                    <label>Nome do titular</label>
                                    <input type="text" name="nomePortador" id="nomePortador" required class="required input" />
                                </li>
                                <li>
                                    <label>Data de nascimento <span>(DD/MM/AAAA)</span></label>
                                    <input type="text" name="dataPortador" id="dataPortador" required class="required input" />
                                </li>

                                <li>
                                    <label>CPF</label>
                                    <input type="text" name="cpfPortador" id="cpfPortador" required class="required input" />
                                </li>
                                <li>
                                    <label>Telefone de contato</label>
                                    <input type="text" name="telefonePortador" id="telefonePortador" required class="required input" />
                                </li>
                                <li>
                                    <img src="{$modules_dir}moipv2/images/spinner.gif" class="spinner_moip" alt="Aguarde..."  style="display:none;"/>
                                    <button class="exclusive moip-btn" id="CREDIT_CARD" name="submit" type="submit">Efetuar pagamento</button> 
                                </li>
                            </ul>
                        <br class="clear">
                    </div>

                </div>
    {/if}
    {if $MOIPV2_BOLETO_ACEITE}
     <div class="moipay">

        <legend>Pagar utilizando Boleto Bancário</legend>

        <label class="pchoice" title="Pagar com Boleto Bradesco">
            <input type="radio" name="payment" value="BOLETO" id="BRADESCO" />
            <img src="{$modules_dir}moipv2/images/boleto.jpg" alt="Boleto">
        </label>

        <div class="escolha payform" id="BOLETO">
            <div id="div-boleto" class="escolha-side-full">
            {if !$MOIPV2_BOLETODISCOUNT}
                <legend>Parcela única de R$ {$order_total} </legend>
            {/if}
                <p>Você deverá efetuar o pagamento do boleto em até três (3) dias após sua impressão.</p>
                <img src="{$modules_dir}moipv2/images/spinner.gif" class="spinner_moip" alt="Aguarde..."  style="display:none;" />
              <button type="submit" class="exclusive moip-btn btn-large" id="BRADESCO">Efetuar pagamento</button>
                

            </div>

        </div>
        <br class="clear">
    </div>
    {/if}
    {if $MOIPV2_TEF_ACEITE}
    <div class="moipay">

                    <legend>Pagar utilizando Débito em Conta</legend>

                    <label class="pchoice" title="Pagar com Banrisul">
                        <input type="radio" name="payment" value="ONLINE_BANK_DEBIT" id="041" />
                        <img src="{$modules_dir}moipv2/images/banrisul.png" alt="Banrisul">
                    </label>
                   <label class="pchoice" title="Pagar com Itau">
                        <input type="radio" name="payment" value="ONLINE_BANK_DEBIT" id="341" />
                        <img src="{$modules_dir}moipv2/images/itau.jpg" alt="Itau">
                    </label>
                    <label class="pchoice" title="Pagar com Bradesco">
                        <input type="radio" name="payment" value="ONLINE_BANK_DEBIT" id="237" />
                        <img src="{$modules_dir}moipv2/images/bradesco.jpg" alt="Bradesco">
                    </label>
                    <label class="pchoice" title="Pagar com Banco do Brasil">
                        <input type="radio" name="payment" value="ONLINE_BANK_DEBIT" id="001" />
                        <img src="{$modules_dir}moipv2/images/bb.jpg" alt="Banco do Brasil">
                    </label>

                    <div class="escolha payform" id="ONLINE_BANK_DEBIT">

                        <div id="div-debito" class="escolha-side-full">

                            <legend>Parcela única de R$ {$orderValueBr}  </legend>
                            <p>Você será redirecionado ao site de seu banco para concluir o pagamento.</p>
                            <img src="{$modules_dir}moipv2/images/spinner.gif" class="spinner_moip" alt="Aguarde..." style="display:none;" />
                            <button class="exclusive moip-btn" id="ONLINE_BANK_DEBIT" name="submit" type="submit">Efetuar pagamento</button>
                            
                        </div>

                    </div>
                    <br class="clear">
                </div>
        {/if}
</form>

</p>
