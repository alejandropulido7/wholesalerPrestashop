<?php

class RenderListBack extends Module
{

    public function renderList(){

        $helper = new HelperList();

        $helper->shopLinkType = 'shop';
        $helper->identifier = 'id_mayorista';
        $helper->show_toolbar = true;
        $helper->title = 'Lista de Productos';
        $helper->table = 'wholesaler';
        $helper->identifier = 'id_mayorista';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->shopLinkType = '';
        $helper->simple_header = false;
        $helper->actions = array('edit', 'delete');
        $helper->tpl_vars = array(
            'show_filters' => false,
            'show_reset_button' => true,
            'fields' => array(
                'id_mayorista' => array('title' => 'ID', 'align' => 'center', 'class' => 'fixed-width-xs'),
                'dni_mayorista' => array('title' => 'DNI', 'align' => 'center'),
                'razon_social' => array('title' => 'Razon Social', 'align' => 'center'),
                'telefono' => array('title' => 'Telefono', 'align' => 'center'),
            ),
        );

        $columns = array(
            'id_mayorista' => array('title' => 'ID', 'align' => 'center', 'class' => 'fixed-width-xs'),
            'dni_mayorista' => array('title' => 'DNI', 'align' => 'center'),
            'razon_social' => array('title' => 'Razon Social', 'align' => 'center'),
            'telefono' => array('title' => 'Telefono', 'align' => 'center'),
        );

        $sql = 'SELECT id_mayorista, dni_mayorista, razon_social, telefono FROM '._DB_PREFIX_.'wholesaler';
        $data = Db::getInstance()->executeS($sql);

        return $helper->generateList($data, $columns);         
    }

}