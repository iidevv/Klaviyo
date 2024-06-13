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
        $product = $item->getProduct();

        $result = [
            "\$value" => $item->getPrice() * $item->getAmount(),
            "AddedItemProductName" => $item->getName(),
            "AddedItemProductID" => $item->getSku(),
            "AddedItemSKU" => $item->getSku(),
            "AddedItemCategories" => $this->getProductCategories($product),
            "AddedItemImageURL" => $item->getImageURL(),
            "AddedItemURL" => $item->getURL(),
            "AddedItemPrice" => (int) $item->getPrice(),
            "AddedItemQuantity" => $item->getAmount(),
            "CheckoutURL" => $this->getShopURL('?target=checkout'),
        ];

        $cartItems = $this->getCart()->getItems();

        $items = [];
        
        foreach ($cartItems as $cartItem) {
            $product = $cartItem->getProduct();
            $items[] = [
                "ProductID" => $cartItem->getSku(),
                "SKU" => $cartItem->getSku(),
                "ProductName" => $cartItem->getName(),
                "Quantity" => $cartItem->getAmount(),
                "ItemPrice" => (int) $product->getPrice(),
                "RowTotal" => $cartItem->getPrice() * $cartItem->getAmount(),
                "ProductURL" => $cartItem->getURL(),
                "ImageURL" => $cartItem->getImageURL(),
                "ProductCategories" => $this->getProductCategories($product),
            ];

            if ($product->getNetMarketPrice()) {
                $items[count($items) - 1]["CompareAtPrice"] = $product->getNetMarketPrice();
            }
        }

        $result["ItemNames"] = array_map(
            function ($item) {
                return $item['ProductName'];
            },
            $items
        );

        $result['Items'] = $items;

        return $result;
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
