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
                <label class="col-md-3 form-control-label">{l s='DNI' mod='wholesalermodule'}</label>
                <div class="col-md-6">
                    <input class="form-control" type="text" name="dni" class="form-control" value=""  
                    />
                </div>                    
            </div>

            <div class="form-group row">
                <label class="col-md-3 form-control-label">{l s='Razon social' mod='wholesalermodule'}</label>
                <div class="col-md-6">
                    <input class="form-control" type="text" name="razon_social" class="form-control" value=""  
                    />
                </div>                    
            </div>

            <div class="form-group row">
                <label class="col-md-3 form-control-label">{l s='Tel/Cel' mod='wholesalermodule'}</label>
                <div class="col-md-6">
                    <input class="form-control" type="text" name="telefono" class="form-control" value=""  
                    />
                </div>                    
            </div>

            <div class="form-group row">
                <label class="col-md-3 form-control-label">{l s='Email address' mod='wholesalermodule'}</label>
                <div class="col-md-6">
                    <input class="form-control" type="email" name="email" class="form-control" value=""  
                    />
                </div>                    
            </div>

            <div class="form-group row">
                <label class="col-md-3 form-control-label">{l s='Contact name' mod='wholesalermodule'}</label>
                <div class="col-md-6">
                    <input class="form-control" type="email" name="contacto" class="form-control" value=""  
                    />
                </div>                    
            </div>

            <div class="form-group row" for="file-upload" class="btn btn-primary ">
                <label>{l s='Rut' mod='wholesalermodule'}</label>
                <input type="file" name="rutShop" />
            </div>

            <div class="form-group row" for="file-upload" class="btn btn-primary ">
                <label>{l s='Camara de comercio' mod='wholesalermodule'}</label>
                <input type="file" name="commerceShop" />
            </div>

            <div class="form-footer">
                <input type="hidden" name="token" value="{$token|escape:'htmlall':'UTF-8'}" />
                <button type="submit" name="submitWholesaler" class="btn btn-primary">
                    {l s='Send' mod='wholesalermodule'}
                </button>
            </div>

        </div>
    </form>
    {if isset($response)}
        <div>{$response}</div>
    {/if}
</section>


<br>
RUT:
{if isset($archivo)}
<div>{$archivo}</div>
{/if}

CAMARA:
{if isset($camara)}
<div>{$camara}</div>
{/if}
