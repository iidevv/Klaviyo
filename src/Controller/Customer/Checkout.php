<?php

namespace Iidev\Klaviyo\Controller\Customer;

use Iidev\Klaviyo\Core\API;
use XCart\Extender\Mapping\Extender;

/**
 * Checkout
 * @Extender\Mixin
 */
class Checkout extends \XLite\Controller\Customer\Checkout
{
    protected function saveAnonymousProfile()
    {
        parent::saveAnonymousProfile();

        $profile = $this->getCart()->getOrigProfile();

        $api = new API();
        $api->createAndSubscribeProfile($profile->getLogin(), ["\$source" => 'buyer']);
    }
}
