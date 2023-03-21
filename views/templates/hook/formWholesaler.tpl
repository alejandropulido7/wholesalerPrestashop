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

<style>
.formWholesaler{
    padding: 1rem;
    display: flex;
    justify-content: center;
    text-align: center;
}
</style>

<section class="formWholesaler">
    <form action="" method="post" enctype="multipart/form-data">

        <div class="text-center">
            <h1 class="h3">{l s='Send a message' mod='wholesalermodule'}</h1>
            <p>{l s='If you would like to add a comment about your order, please write it in the field below.'}</p>
        </div>

        <div class="form-fields">
            <div class="form-group row">
                <label class="col-md-3 form-control-label">{l s='Email address' d='Modules.Contactform.Shop'}</label>
                <div class="col-md-6">
                    <input class="form-control" type="email" name="from" class="form-control" value=""  
                    />
                </div>                    
            </div>

            <div class="form-group row">
                <label class="col-md-3 form-control-label">{l s='Message' d='Modules.Contactform.Shop'}</label>
                <div class="col-md-6 text-left">
                    <textarea class="form-control" cols="67" rows="3" name="message">
                    </textarea>
                </div>
            </div>

            <div class="form-group row" for="file-upload" class="btn btn-primary ">
                <label>{l s='Attach File' d='Modules.Contactform.Shop'}</label>
                <input type="file" name="fileUpload" />
            </div>

            <div class="form-footer">
                <input type="hidden" name="token" value="{$token|escape:'htmlall':'UTF-8'}" />
                <button type="submit" name="submitWholesaler" class="btn btn-primary">
                    {l s='Send' d='Modules.Contactform.Shop'}
                </button>
            </div>

        </div>
    </form>
</section>

<div>Correo</div>
{if isset($fromWhole)}
<div>{$fromWhole}</div>
{/if}
<div>Mensaje</div>
{if isset($messageWhole)}
<div>{$messageWhole}</div>
{/if}

{$rutaTemplate}

SQL:
{$sql}
