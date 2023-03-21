<?php



class WidgetWhosaler extends Module
{

    function getWidgetVariables($hookName = null, array $params){

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
            'rutaTemplate' => $rutaTemplate
		);
    }
}