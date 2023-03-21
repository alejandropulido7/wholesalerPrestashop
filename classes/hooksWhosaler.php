<?php

use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;

class HooksWhosaler extends Module
{

    public function hookActionPresentCart($params){        
        $minimalPurchase = $params['presentedCart']['minimalPurchase'];
        $priceFormatter = new PriceFormatter();
        $productsTotalExcludingTax = $this->context->cart->getOrderTotal(false);
        $lang = Language::getLanguage($this->context->language->id);
        $message = '';
        
        if($lang['language_code'] == 'en'){
            $message = $this->l('Wholesaler: A minimum shopping cart total of '.$priceFormatter->format($minimalPurchase).' (tax excl.) is required to validate your order. Current cart total is '.$priceFormatter->format($productsTotalExcludingTax).' (tax excl.).');
        }else if($lang['language_code'] == 'es'){
            $message = $this->l('Mayorista: Se necesita una compra mínima total de '.$priceFormatter->format($minimalPurchase).' (impuestos exc.) para validar su pedido. En este momento el valor total de su carrito es de '.$priceFormatter->format($productsTotalExcludingTax).' (impuestos exc.).');
        }

        if($productsTotalExcludingTax < $minimalPurchase){
            $params['presentedCart']['minimalPurchaseRequired'] = $message;
        }

    }


    public function hookOverrideMinimalPurchasePrice($params){
        $priceFormatter = new PriceFormatter();
        $minimalPurchase = $priceFormatter->convertAmount((float) Configuration::get('WHOLESALERMODULE_MINIMUM_PURCHASE'));
        $group = Group::searchByName('Mayorista');
        $defaultGroup = Customer::getDefaultGroupId($this->context->customer->id) ?? 0;
        
        if($defaultGroup == $group['id_group']){
            $params['minimalPurchase'] = $minimalPurchase;
        }
        
    }


}