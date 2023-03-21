<?php

class RenderFormBack extends Module
{

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    public function renderForm($nameModule, $tabModule )
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;

        $helper->identifier = $this->identifier;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
        .'&configure='.$nameModule.'&tab_module='.$tabModule.'&module_name='.$nameModule;
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
     * Set values for the inputs.
     */
    public function getConfigFormValues()
    {
        $this->displayConfirmation($this->trans('The settings have been updated.', [], 'Admin.Notifications.Success'));
        return array(
            'WHOLESALERMODULE_MINIMUM_PURCHASE' => Configuration::get('WHOLESALERMODULE_MINIMUM_PURCHASE'),
        );
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
}