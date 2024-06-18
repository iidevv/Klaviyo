<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Iidev\Klaviyo\Controller\Customer;

use XCart\Extender\Mapping\Extender;

use Iidev\Klaviyo\Core\API;

/**
 * @Extender\Mixin
 */
class ContactUs extends \CDev\ContactUs\Controller\Customer\ContactUs
{

    protected function doActionSend()
    {
        $email = \XLite\Core\Request::getInstance()->email;
        
        $api = new API();
        $api->createAndSubscribeProfile($email, ["\$source" => 'catalog_request']);

        parent::doActionSend();
    }
}
