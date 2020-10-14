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
<!--
<link rel="stylesheet" type="text/css" href="prestasho_1_7/modules/modulo_tarea4/views/css/front.css" />
-->
<main>
 <article>
 <div class="card-body">
<div class="tarjeta">
    <div class="box_stars_l">
        <p id="start1_l">★</p>
        <p id="start2_l">★</p>
        <p id="start3_l">★</p>
    </div>


    <div class="box_ryg">
        <h2 class="tit_ryg">{l s='¡Rasca y gana!'}</h2>
        <p class="desc_ryg"></p>

        <div class="card1">
	
            <div class="base">
                <strong>- {$valor_smarty} &euro; </strong> 
                {l s='con el código' d='Shop.Theme.Checkout'}: <strong>{$discountcode_smarty} </strong>
                 
            </div>
        
            <canvas id="scratch" width="300" height="60">
            </canvas>

        </div>

         <p class="desc_ryg"> <br/> {l s='Pulsa y arrastra para conseguir descuento en tu próxima compra'} </p>
    </div>

   <div class="box_stars_r">
        <p id="start1_r">★</p>
        <p id="start2_r">★</p>
        <p id="start3_r">★</p>
    </div>
         
</div>
</div>
</article>
</main>


<!-- scratch-card-with-canvas JS -->
<!--<script type="text/javascript" src="localhost/htdocs/prestasho_1_7/rasca_y_gana/js/scratch-card.js"></script>-->


