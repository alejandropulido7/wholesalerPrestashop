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

function installBD (){

    $sql = array();

        $sql[0] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ .'wholesaler` (
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
