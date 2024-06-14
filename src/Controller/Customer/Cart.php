<?php

namespace Iidev\Klaviyo\Controller\Customer;

use XCart\Extender\Mapping\Extender as Extender;
use Iidev\Klaviyo\Core\FrontendTracking;

/**
 * @Extender\Mixin
 */
class Cart extends \XLite\Controller\Customer\Cart
{
    protected function processAddItemSuccess($item)
    {
        $tracking = new FrontendTracking();
        $tracking->doAddToCart($item);
        
        parent::processAddItemSuccess($item);
    }
}
