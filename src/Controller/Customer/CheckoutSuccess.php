<?php

namespace Iidev\Klaviyo\Controller\Customer;

use Iidev\Klaviyo\Core\BackendTracking;
use XCart\Extender\Mapping\Extender;

/**
 * Checkout success controller
 * @Extender\Mixin
 */
class CheckoutSuccess extends \XLite\Controller\Customer\CheckoutSuccess
{
    protected function doNoAction()
    {
        parent::doNoAction();

        $orders = \XLite\Core\Session::getInstance()->placedOrders;
        if (!is_array($orders)) {
            $orders = [];
        }
        if (
            !\XLite\Core\Request::getInstance()->isAJAX()
            && in_array($this->getTarget(), ['checkout_success', 'checkoutSuccess'])
            && $this->getOrder()
            && !in_array($this->getOrder()->getOrderId(), $orders)
        ) {
            $tracking = new BackendTracking;
            
            $tracking->doPlacedOrder($this->getOrder());
            $tracking->doOrderedProduct($this->getOrder());

            $orders[] = $this->getOrder()->getOrderId();
            \XLite\Core\Session::getInstance()->placedOrders = $orders;
        }
    }
}
