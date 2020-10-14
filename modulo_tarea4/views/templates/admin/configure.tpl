{*
* 2007-2020 PrestaShop
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
*  @copyright 2007-2020 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="panel">
	<h3><i class="icon icon-credit-card"></i> {l s='Modulo tarea 4' mod='modulo_tarea4'}</h3>
	<p>
		{l s='Modulo que envia un mail con confirmación de pedido y genera cupón descuento a partir de x cantidad gastada por el cliente en la tienda.' mod='modulo_tarea4'}
	</p>
	<br />
</div>

<div class="panel">
	<h3><i class="icon icon-tags"></i>{l s="Cupones existentes" mod="modulo_tarea4"}</h3>
	<table class="table">
		<thead>
			<tr>
				<th scope="col">{l s="customer id" mod="modulo_tarea4"}</th>
				<th scope="col">{l s="Nombre" mod="modulo_tarea4"}</th>
				<th scope="col">{l s="Apellido" mod="modulo_tarea4"}</th>
				<th scope="col">{l s="Email" mod="modulo_tarea4"}</th>
				<th scope="col">{l s="Codígo cupón" mod="modulo_tarea4"}</th>
				<th scope="col">{l s="Fecha creación cupón" mod="modulo_tarea4"}</th>
				<th scope="col">{l s="Valor del cupón" mod="modulo_tarea4"}</th>
			</tr>
		</thead>
		
		<tbody>

		{if dbcontent}
			{foreach from=$dbcontent key=key item=r}
			<tr>
				<td name="customer_id"> {$r['customer_id']} </td>
				<td name="firstname"> {$r['firstname']} </td>
				<td name="lastname"> {$r['lastname']} </td>
				<td name="email"> {$r['email']} </td>
				<td name="discountcode"> {$r['discountcode']} </td>
				<td name="discount_creation_date"> {$r['discount_creation_date']} </td>
				<td name="discount_valor"> {$r['discount_valor']} </td>
			</tr>
			{/foreach}
		{else}
			<p> No existen cupones </p>
		{/if}
		
		</tbody>
	</table>
</div>

