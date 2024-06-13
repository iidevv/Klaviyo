<?php

namespace Iidev\Klaviyo\Controller\Customer;

use XCart\Extender\Mapping\Extender as Extender;
use \XLite\Core\Event;

/**
 * @Extender\Mixin
 */
class Cart extends \XLite\Controller\Customer\Cart
{
    protected function getAddedToCartEventData($item)
    {
        $cartItems = $this->getCart()->getItems();
        $eventData = [];

        foreach ($cartItems as $cartItem) {
            $product = $cartItem->getProduct();
            $eventData[] = [
                "ProductName" => $product->getName(),
                "ProductID" => $product->getSku(),
                "SKU" => $product->getSku(),
                "Categories" => $this->getProductCategories($product),
                "ImageURL" => $product->getImageURL(),
                "URL" => $product->getURL(),
                "Brand" => $product->getBrand(),
                "Price" => $product->getDisplayPrice(),
                "CompareAtPrice" => $product->getNetPrice()
                // 'product_id' => $product->getProductId(),
                // 'product_name' => $product->getName(),
                // 'quantity' => $cartItem->getAmount(),
                // 'price' => $cartItem->getTotal(),
                // 'categories' => $this->getProductCategories($product),
                // 'image_url' => $product->getImage() ? $product->getImage()->getFrontURL() : '',
                // 'product_url' => \XLite\Core\Converter::buildFullURL(
                //     'product',
                //     '',
                //     ['product_id' => $product->getProductId()],
                //     \XLite::getInstance()->getShopURL()
                // ),
            ];
        }

        return $eventData;
    }

    protected function getProductCategories($product)
    {
        $categories = [];
        foreach ($product->getCategories() as $category) {
            $categories[] = $category->getName();
        }
        return $categories;
    }

    protected function processAddItemSuccess($item)
    {
        Event::klaviyoAddedToCart($this->getAddedToCartEventData($item));

        parent::processAddItemSuccess($item);
    }
}
