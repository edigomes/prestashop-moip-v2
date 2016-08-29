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

{if $status == 'ok'}
	<p>
		{if $paymentMethod == 'CREDIT_CARD'}
			<p>O seu pedido foi gerado com sucesso, seu status é: {$status_moip}</p>
			<p>O código Moip da transação: {$moip_pay}</p>
		{elseif $paymentMethod == 'BOLETO'}
			<script type="text/javascript" src="{$modules_dir}moipv2/script/boleto.js"></script>
			<script type="text/javascript" src="{$modules_dir}moipv2/script/printPage.js"></script>
			{literal}
			        <script type="text/javascript">
			            $(document).ready(function(){
			                BoletoWidget();
			            });
			        </script>
			 {/literal}

			<p>O código de sua transação é: {$moip_pay}</p>
			<p>
			
				<div class="modal bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
				 	<div class="modal-dialog modal-lg">

					  	 <div class="modal-header">
					  	 <span class="right"><a class="btnPrint button wide card-done left billet-print-now" href='{$redirectURI}/print'>Imprima seu boleto</a>						</span>	
							 <h4 class="modal-title">Boleto Bancário</h4>
							 
						</div>
						<div class="modal-body">

							 <div class="progress progress-striped active">
							        <div class="progress-bar"  style="width:100%"></div>
							 </div>
						  	<iframe src="{$redirectURI}/print"  class="mce-iframe" frameborder="0" width="100%" height="750px"  scrolling="auto" id="moip_page" style="display:none;" onload="update_iframe();"></iframe>
						</div>
				  	</div>
				</div>
			</p>
		{elseif $paymentMethod == 'ONLINE_BANK_DEBIT'}
			<p>O código de sua transação é: {$moip_pay}</p>
			<p><a href="{$redirectURI}" target="_blank" class="btnPrint button wide card-done left ">Clique aqui para ir ao banco </a></p>

		{else}

		{/if}
		
		{if !isset($reference)}
			<br /><br />- {l s='Não esqueça de anotar o número do seu pedido #%d.' sprintf=$id_order mod='moipv2'}
		{else}
			<br /><br />- {l s='Não esqueça de anotar o número do seu pedido %s.' sprintf=$reference mod='moipv2'}
		{/if}
		
		<br /><br />{l s='Caso tenha alguma dúvida entre em contato conosco' mod='moipv2'} <a href="{$link->getPageLink('contact', true)|escape:'html'}">{l s='por nosso SAC.' mod='moipv2'}</a>.
	</p>
{else}
	<p class="warning">
		{l s='Ocorreu um problema em seu pedido, por favor entre contato conosco via' mod='moipv2'} 
		<a href="{$link->getPageLink('contact', true)|escape:'html'}">{l s='SAC.' mod='moipv2'}</a>.
	</p>
{/if}



