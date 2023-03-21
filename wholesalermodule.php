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
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;

require_once(dirname(__FILE__) . '/classes/renderFormBack.php');
require_once(dirname(__FILE__) . '/classes/renderListBack.php');
require_once(dirname(__FILE__) . '/classes/widgetWhosaler.php');
require_once(dirname(__FILE__) . '/classes/hooksWhosaler.php');
require_once(dirname(__FILE__) . '/sql/install.php');
require_once(dirname(__FILE__) . '/sql/uninstall.php');

if (!defined('_PS_VERSION_')) {
    exit;
}

class WholesalerModule extends Module implements WidgetInterface
{
    protected $config_form = false;

    protected $renderFormBack;
    protected $renderListBack;
    protected $widgetWhosaler;
    protected $hooksWhosaler;

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

        $this->displayName = $this->l('Wholesaler Module');
        $this->description = $this->l('Gestion de mayoristas');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

        $this->renderFormBack = new RenderFormBack();
        $this->renderListBack = new RenderListBack();
        $this->widgetWhosaler = new WidgetWhosaler();
        $this->widgetWhosaler = new WidgetWhosaler();
        $this->hooksWhosaler = new HooksWhosaler();
        
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
                && $this->registerHook('overrideMinimalPurchasePrice') 
                && $this->registerHook('actionPresentCart')
                && $this->registerHook('actionFrontControllerSetMedia')
                && installBD();
    }

    public function uninstall()
    {
        Configuration::deleteByName('WHOLESALERMODULE_MINIMUM_PURCHASE');

        return parent::uninstall() && uninstallDB();
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

        return $output.$this->renderFormBack->renderForm($this->name, $this->tab) . $output.$this->renderListBack->renderList();

    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->renderFormBack->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    public function hookOverrideMinimalPurchasePrice($params){
        $this->hooksWhosaler->hookOverrideMinimalPurchasePrice($params);        
    }

    public function hookActionPresentCart($params){
        $this->hooksWhosaler->hookActionPresentCart($params);
    }

    public function hookDisplayShoppingCart()
    {
        
        $message = '';
        $minimalPurchase = Configuration::get('WHOLESALERMODULE_MINIMUM_PURCHASE');
        
        $this->context->smarty->assign([
            'message' => $message,
            'minimumPurchase' => $minimalPurchase
        ]);
        
        return $this->display(__FILE__, 'mayoristas.tpl');
    }

    function renderWidget($hookName = null, array $params)
    {
        if (!$this->active) {
            return;
        }
        $this->smarty->assign($this->getWidgetVariables($hookName, $params));
        return $this->display(__FILE__, 'formWholesaler.tpl');
    }

    function getWidgetVariables($hookName = null, array $params){
        $this->widgetWhosaler->getWidgetVariables($hookName, $params);
    }

}
