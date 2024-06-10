<?php

namespace Iidev\Klaviyo\Controller\Customer;

use \XLite\Core\Session;
use \XLite\Core\Request;

class TrackingSnippet extends \XLite\Controller\Customer\ACustomer
{
    public function handleRequest()
    {
        $sessionValue = Request::getInstance()->session_value;

        if (!empty($sessionValue)) {
            Session::getInstance()->session_value = $sessionValue;
        }

        parent::handleRequest();
    }  
}