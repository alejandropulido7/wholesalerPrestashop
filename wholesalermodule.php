<?php
/**
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
*/

use PhpParser\Node\Expr\Cast\Double;
use PrestaShop\PrestaShop\Adapter\Presenter\Cart\CartPresenter;

if (!defined('_PS_VERSION_')) {
    exit;
}

class WholesalerModule extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'wholesalermodule';
        $this->tab = 'checkout';
        $this->version = '1.0.0';
        $this->author = 'Coding Proactive';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Modulo Mayoristas');
        $this->description = $this->l('Gestion de mayoristas');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('WHOLESALERMODULE_MINIMUM_PURCHASE', 0);

        return parent::install() 
                && $this->registerHook('displayShoppingCart') 
                && $this->registerHook('displayMinimalPurchase')
                && $this->registerHook('actionFrontControllerSetMedia')
                && $this->installBD();
    }

    public function uninstall()
    {
        Configuration::deleteByName('WHOLESALERMODULE_MINIMUM_PURCHASE');

        return parent::uninstall() && $this->uninstallDB();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitWholesalerModuleModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;

        $helper->identifier = $this->identifier;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
        .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );
        $helper->submit_action = 'submitWholesalerModuleModule';

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Cantidad mínima de compra para mayoristas'),
                        'name' => 'WHOLESALERMODULE_MINIMUM_PURCHASE',
                        'label' => $this->l('Cantidad mínima de compra necesaria para validar el pedido de mayoristas'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'WHOLESALERMODULE_MINIMUM_PURCHASE' => Configuration::get('WHOLESALERMODULE_MINIMUM_PURCHASE'),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    /**
    * Start custom code.
    */
    public function installBD()
    {
        $sql = array();

        $sql[0] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . $this-> name .'_mayoristas` (
            `id_mayorista` int(11) NOT NULL AUTO_INCREMENT,
            `dni_mayorista` int(12) NOT NULL,
            `razon_social` varchar(254) NOT NULL,
            `telefono` varchar(254) NOT NULL,
            `email` varchar(254) NOT NULL,
            `contacto` varchar(254) NOT NULL,
            `rut` varchar(254) NOT NULL,
            `camara_comercio` varchar(254) NOT NULL,
            PRIMARY KEY  (`id_mayorista`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        $group = Group::searchByName('Mayorista');
        $idMax = 0;
        $getGroups = Group::getGroups(1);
        foreach ($getGroups as $value) {
            if($idMax < $value['id_group']){
                $idMax = $value['id_group'];
            }
        }
        $idMax = $idMax+1;
        if(!$group){
            $sql[1] = 'INSERT INTO `'. _DB_PREFIX_ .'group_lang` (`id_group`, `id_lang`, `name`) VALUES ('.$idMax.', 1, "Mayorista");';
            $sql[2] = 'INSERT INTO `'. _DB_PREFIX_ .'group_lang` (`id_group`, `id_lang`, `name`) VALUES ('.$idMax.', 2, "Mayorista");';
            $sql[3] = 'INSERT INTO `'. _DB_PREFIX_ .'group_shop` (`id_group`, `id_shop`) VALUES ('.$idMax.', 1);';
            $sql[4] = 'INSERT INTO `'. _DB_PREFIX_ .'group` (`id_group`, `reduction`, `price_display_method`, `show_prices`, `date_add`, `date_upd`) VALUES ('.$idMax.', "0", "0", "0", sysdate(), sysdate());';
        }        
        foreach ($sql as $query) {
            if (Db::getInstance()->execute($query) == false) {
                return false;
            }
        }
        
        return true;
    }

    public function uninstallDB()
    {
        $sqlUnistall = array();
        $sqlUnistall[0] = 'DROP TABLE IF EXISTS '._DB_PREFIX_. $this-> name .'_mayoristas';
        $group = Group::searchByName('Mayorista');
        $idGroup = $group['id_group'];
        if($idGroup != null || $idGroup != 0){
            $sql[1] = 'DELETE `'. _DB_PREFIX_ .'group_lang` WHERE `id_group` = '.$idGroup.';';
            $sql[2] = 'DELETE `'. _DB_PREFIX_ .'group_shop` WHERE `id_group` = '.$idGroup.';';
            $sql[3] = 'DELETE `'. _DB_PREFIX_ .'group` WHERE `id_group` = '.$idGroup.';';
        }

        foreach ($sqlUnistall as $query) {
            if (Db::getInstance()->execute($query) == false) {
                return false;
            }
        }
        
        return true;    
        
    }

    public function hookDisplayShoppingCart()
    {
        $idCart = $this->context->cookie->id_cart;
        $idOrder = $this->context->cookie->id_order;
        $url = _THEME_DIR_ . 'templates/checkout/_partials/cart-detailed-actions.tpl';
        $carrito = Cart::getTotalCart($idCart);
        $regla = (Double)Configuration::get('WHOLESALERMODULE_MINIMUM_PURCHASE');

        $grupo = Group::getGroups(1);
        $grupoCreado = false;
        $name = Group::searchByName('Cliente');

        $idMax = 0;

        $grupo = Group::getGroups(1);
        foreach ($grupo as $value) {
            if($idMax < $value['id_group']){
                $idMax = $value['id_group'];
            }
        }

        $this->context->smarty->assign([
            'textoAEnviar' => $carrito,
            'reglaMayorista' => $regla,
            'grupo' => $grupo,
            'idMax' => $idMax,
        ]);
        //return $this->context->smarty->fetch($this->local_path.'views/templates/admin/mayoristas.tpl');
        return $this->display(__FILE__, 'mayoristas.tpl');
        // return $this->context->smarty->fetch($url);
    }

    public function hookDisplayMinimalPurchase()
    {
        $minimalPurchase = (Double)Configuration::get('WHOLESALERMODULE_MINIMUM_PURCHASE');
        $productsTotalExcludingTax = $this->context->cart->getCartTotalPrice();
        echo 'A minimum shopping cart total of '.$minimalPurchase.' (tax excl.) is required to validate your order. Current cart total is '.$productsTotalExcludingTax.' (tax excl.).';
        
    }

    public function hookActionFrontControllerSetMedia()
    {
        $this->context->controller->registerJavascript(
            'frontwholesaler-js',
            'modules/' . $this->name . '/views/js/front.js'
        );
    }
}
