<?php

use Symfony\Component\Validator\Constraints\FileValidator;

class WidgetWholesaler extends Module
{

    function getWidgetVariables($hookName = null, array $param){

        $values = [];
        $response = null;     

        $archivo_rut = null;
        $archivo_camara = null;
        $rutaTemplate = _PS_MAIL_DIR_;

        if (Tools::isSubmit('submitWholesaler')) {
            $values['dniShop'] = pSQL(Tools::getValue('dni'));
            $values['nameShop'] = pSQL(Tools::getValue('razon_social'));
            $values['cellPhone'] = pSQL(Tools::getValue('telefono'));
            $values['emailShop'] = pSQL(Tools::getValue('email'));
            $values['contactShop'] = pSQL(Tools::getValue('contacto'));
            $values['rutShop'] = pSQL($this->fileValidation('rutShop',Tools::getValue('email')));
            $values['commerceShop'] = pSQL($this->fileValidation('commerceShop',Tools::getValue('email')));

            $archivo_rut = $values['rutShop'];
            $archivo_camara = $values['commerceShop'];
           
            $responseSave = $this->saveDatabase($values);

            $response = $responseSave ? $this->l('Form sended successfull') : $this->l('Error in form');

        }

        

        return array(
            'response' => $archivo_rut,
            'camara' => $archivo_camara,
            'response' => $response
        );

    }

    protected function fileValidation(string $fileName, string $email){

        $PDF_TYPE = 'application/pdf';
        $file_path = null;

        if(Tools::isSubmit('submitWholesaler')){
            if (isset($_FILES[$fileName]) && !empty($_FILES[$fileName]['tmp_name'])){

                $fileType = $_FILES[$fileName]['type'];
    
                if (!$fileType === $PDF_TYPE) {
                    return $this->displayError("Formato de archivo incorrecto");
                }else{
                    $ext = substr($_FILES[$fileName]['name'], strrpos($_FILES[$fileName]['name'], '.') + 1);
                    $file_name = md5($_FILES[$fileName]['name']) . '.' . $ext;
    
                    $file_name_server = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'customer_files' . DIRECTORY_SEPARATOR . $email ."_". $file_name;
    
                    if (!move_uploaded_file($_FILES[$fileName]['tmp_name'], $file_name_server)) {
                        return $this->displayError($this->trans('An error occurred while attempting to upload the file.', [], 'Admin.Notifications.Error'));
                    }else{
                        $file_path = $file_name_server;
                    }
                }
            }
        }        

        return $file_path;
    }


    protected function saveDatabase(array $data){

        $success = false;

        $sql = 'INSERT INTO `'. _DB_PREFIX_ .'wholesaler` (`dni_mayorista`, `razon_social`, `telefono`, `email`, `contacto`, `rut`, `camara_comercio`) 
                                                    VALUES ('.$data['dniShop'].', "'.$data['nameShop'].'", "'.$data['cellPhone'].'", "'.$data['emailShop'].'", "'.$data['contactShop'].'", "'.$data['rutShop'].'", "'.$data['commerceShop'].'");';
        
        if (Db::getInstance()->execute($sql) != false) {
            $success = true;
        }

        return $success;
    }


    protected function sendEmail(int $dniShop, array $data){

        // $var_list = [
            //     '{firstname}' => '',
            //     '{lastname}' => '',
            //     '{order_name}' => '-',
            //     '{attached_file}' => '-',
            //     '{message}' => Tools::nl2br(Tools::htmlentitiesUTF8(Tools::stripslashes($messageWhole))),
            //     '{email}' =>  $from,
            //     '{product_name}' => '',
            // ];


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




        // if (!Mail::sendMailTest(
        //     1,
        //     Configuration::get('PS_MAIL_SERVER'),
        //     $messageWhole,
        //     'Solicitud de registro Mayorista',
        //     Configuration::get('PS_MAIL_TYPE'),
        //     Configuration::get('PS_SHOP_EMAIL'),
        //     $from,
        //     Configuration::get('PS_MAIL_USER'),
        //     Configuration::get('PS_MAIL_PASSWD'),
        //     Configuration::get('PS_MAIL_SMTP_PORT'),
        //     Configuration::get('PS_MAIL_SMTP_ENCRYPTION')
        // )) {
        //     $this->context->controller->errors[] = $this->trans(
        //         'An error occurred while sending the message.',
        //         [],
        //         'Modules.Contactform.Shop'
        //     );
        // };

    }
}