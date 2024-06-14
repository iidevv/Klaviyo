<?php

namespace Iidev\Klaviyo\Core;

use Iidev\Klaviyo\Core\Main;
use \XLite\Core\Event;

class FrontendTracking
{
    public function doStartCheckout()
    {
        $main = Main::getInstance();
        return $main->getStartCheckoutData();
    }

    public function doViewProduct(\XLite\Model\Product $product)
    {
        $main = Main::getInstance();
        return $main->getViewedProductData($product);
    }
    public function doAddToCart(\XLite\Model\OrderItem $item)
    {
        $main = Main::getInstance();
        Event::klaviyoAddedToCart($main->getAddedToCartData($item));
    }
}