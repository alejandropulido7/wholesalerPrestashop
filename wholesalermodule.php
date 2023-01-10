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

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class WholesalerModule extends Module implements WidgetInterface
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
                && $this->registerHook('displayCheckoutSubtotalDetails') 
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
            $sql[4] = 'INSERT INTO `'. _DB_PREFIX_ .'group` (`id_group`, `reduction`, `price_display_method`, `show_prices`, `date_add`, `date_upd`) VALUES ('.$idMax.', "0", "0", "1", sysdate(), sysdate());';
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

    public function hookDisplayCheckoutSubtotalDetails()
    {
        $lang = $this->context->language->id;
        $cartTotalPrice = $this->context->cart->getCartTotalPrice();
        $minimumPurchase = (Double)Configuration::get('WHOLESALERMODULE_MINIMUM_PURCHASE');  
        $group = Group::searchByName('Mayorista');
        $currentGroup = Customer::getDefaultGroupId($this->context->customer->id) ?? 0;

        $isOk = $cartTotalPrice >= $minimumPurchase ? true : false;
        $isWholesaler = (bool) $group['id_group'] == $currentGroup ? 1 : 0;
        $message = 'A minimum shopping cart total of '.$minimumPurchase.' (tax excl.) is required to validate your order. Current cart total is '.$cartTotalPrice.' (tax excl.)';


        $this->context->smarty->assign([
            'minimumPurchase' => $minimumPurchase,
            'cartTotalPrice' => $cartTotalPrice,
            'idWholesaler' => $group['id_group'],
            'currentGroup' => $currentGroup,
            'isOk' => $isOk,
            'isWholesaler' => $isWholesaler,
            'message' => $message
        ]);
        return $this->display(__FILE__, 'mayoristas.tpl');
    }

    public function hookActionFrontControllerSetMedia()
    {
        $this->context->controller->registerJavascript(
            'frontwholesaler-js',
            'modules/' . $this->name . '/views/js/front.js'
        );
    }

    public function renderWidget($hookName = null, array $params)
    {
        if (!$this->active) {
            return;
        }

        $this->smarty->assign($this->getWidgetVariables($hookName, $params));
        
        return $this->display(__FILE__, 'formWholesaler.tpl');
        // return $this->context->smarty->fetch($this->local_path.'views/templates/hook/formWholesaler.tpl');
    }

    protected function createNewToken()
    {
        $this->context->cookie->contactFormToken = md5(uniqid());
        $this->context->cookie->contactFormTokenTTL = time()+600;

        return $this;
    }


    public function getWidgetVariables($hookName = null, array $params){

        $messageWhole = null;
        $emailShop = null;
        $from = null;
        $rutaTemplate = _PS_MAIL_DIR_;

        if (Tools::isSubmit('submitWholesaler')) {
            $messageWhole = Tools::getValue('message');
            $from = Tools::getValue('from');
            $emailShop = Configuration::get('PS_SHOP_EMAIL');
            

            $var_list = [
                '{firstname}' => '',
                '{lastname}' => '',
                '{order_name}' => '-',
                '{attached_file}' => '-',
                '{message}' => Tools::nl2br(Tools::htmlentitiesUTF8(Tools::stripslashes($messageWhole))),
                '{email}' =>  $from,
                '{product_name}' => '',
            ];

            // if (!Mail::Send(
            //     $this->context->language->id,
            //     'contact',
            //     $this->trans('Your message has been correctly sent', [], 'Emails.Subject'),
            //     $var_list,
            //     $from,
            //     null,
            //     null,
            //     null,
            //     null,
            //     null,
            //     _PS_MAIL_DIR_,
            //     false,
            //     null,
            //     null,
            //     $emailShop
            // )) {
            //     $this->context->controller->errors[] = $this->trans(
            //         'An error occurred while sending the message.',
            //         [],
            //         'Modules.Contactform.Shop'
            //     );
            // };

            if (!Mail::sendMailTest(
                1,
                Configuration::get('PS_MAIL_SERVER'),
                $messageWhole,
                'Solicitud de registro Mayorista',
                Configuration::get('PS_MAIL_TYPE'),
                Configuration::get('PS_SHOP_EMAIL'),
                $from,
                Configuration::get('PS_MAIL_USER'),
                Configuration::get('PS_MAIL_PASSWD'),
                Configuration::get('PS_MAIL_SMTP_PORT'),
                Configuration::get('PS_MAIL_SMTP_ENCRYPTION')
            )) {
                $this->context->controller->errors[] = $this->trans(
                    'An error occurred while sending the message.',
                    [],
                    'Modules.Contactform.Shop'
                );
            };

        }

        return array(
			'token' => $this->context->cookie->contactFormToken,
            'messageWhole' => $messageWhole,
            'fromWhole' => $from,
            'from'=> $emailShop,
            'rutaTemplate' => $rutaTemplate,
		);
    }
}
