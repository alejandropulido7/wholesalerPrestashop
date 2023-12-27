<?php

class RenderListBack extends Module
{
    public function renderListWholesaler($nameModule){

        $helper = new HelperList();

        $helper->shopLinkType = '';
        $helper->identifier = 'id_mayorista';
        $helper->show_toolbar = true;
        $helper->title = 'Lista de Clientes Mayoristas';
        $helper->table = 'wholesaler';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$nameModule;
        $helper->simple_header = false;
        $helper->actions = array(
            'edit', 
            'delete',
            'approve');

        $helper->tpl_vars = array(
            'show_filters' => false,
            'show_reset_button' => true,
            'fields' => array(
                'id_mayorista' => array('title' => 'ID', 'align' => 'center', 'class' => 'fixed-width-xs'),
                'dni_mayorista' => array('title' => 'DNI', 'align' => 'center'),
                'razon_social' => array('title' => 'Razon Social', 'align' => 'center'),
                'telefono' => array('title' => 'Telefono', 'align' => 'center')
            ),
        );

        $columns = array(
            'id_mayorista' => array('title' => 'ID', 'align' => 'center', 'class' => 'fixed-width-xs'),
            'dni_mayorista' => array('title' => 'DNI', 'align' => 'center'),
            'razon_social' => array('title' => 'Razon Social', 'align' => 'center'),
            'telefono' => array('title' => 'Telefono', 'align' => 'center')
        );

        $sql = 'SELECT id_mayorista, dni_mayorista, razon_social, telefono FROM '._DB_PREFIX_.'wholesaler';
        $data = Db::getInstance()->executeS($sql);

        return $helper->generateList($data, $columns);         
    }

    function updateWholesaler($id){

        $sqlUpdate = 'SELECT id_mayorista, dni_mayorista, razon_social, telefono FROM '._DB_PREFIX_.'wholesaler WHERE `id_mayorista` = '.(int)$id.';';
        $dataUpdate = Db::getInstance()->executeS($sqlUpdate);

        $this->context->smarty->assign(array(
            'module_dir'=> $this->_path,
            'data' => $dataUpdate
            )
        );
        return $this->context->smarty->fetch($this->local_path.'views/templates/admin/configureUpdate.tpl');
    }

}