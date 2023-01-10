{*
* 2007-2022 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2022 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
minimumPurchase:
<input id="minimumPurchase" type="text" value="{$minimumPurchase}" disabled>
<br>
cartTotalPrice:
<input id="cartTotalPrice" type="number" inputmode="numeric" value="{$cartTotalPrice}">
<br>
custumer:
<input id="currentGroup" type="text" value="{$currentGroup}" disabled>
<br>
group:
<input id="idWholesaler" type="text" value="{$idWholesaler}" disabled>
Mensaje:
<div id="message">{$isOk}</div>
isWholesaler:
<input id="isWholesaler" type="number" value="{$isWholesaler}">



<span id="subtotalCustom"></span>
{if $isWholesaler}
	{if !$isOk}
		<div class="checkout cart-detailed-actions-custom">
			<div class="alert alert-warning" role="alert">
				{$message}
			</div>
		<div>
	{else}
		<div class="text-sm-center">
		<a href="{$urls.pages.order}" class="btn btn-primary">{l s='Proceed to checkout' d='Shop.Theme.Actions'}</a>
		</div>
	{/if}
{/if}

<script type="text/javascript">

alert("isWholesaler");

const isWholesaler = $("#isWholesaler").val() == 1 ? true : false;
    alert(isWholesaler);
    if(isWholesaler){
        $(".card-block.checkout .btn-primary").css("display","none");
    } 
	
</script>