<?php

namespace Iidev\Klaviyo\Core;

use XLite\InjectLoggerTrait;
use XLite\Core\Session;

class Main extends \XLite\Base\Singleton
{
    use InjectLoggerTrait;
    protected static $instance;

    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }
    public function getAddedToCartData($item)
    {
        $product = $item->getProduct();

        $data = [
            "\$value" => $item->getPrice() * $item->getAmount(),
            "AddedItemProductName" => $item->getName(),
            "AddedItemProductID" => $item->getSku(),
            "AddedItemSKU" => $item->getSku(),
            "AddedItemCategories" => $this->getProductCategories($product),
            "AddedItemImageURL" => $item->getImageURL(),
            "AddedItemURL" => $item->getURL(),
            "AddedItemPrice" => (int) $item->getPrice(),
            "AddedItemQuantity" => $item->getAmount(),
            "CheckoutURL" => \XLite::getController()->getShopURL('?target=checkout'),
        ];

        $cartItems = \XLite::getController()->getCart()->getItems();

        $data['Items'] = $this->getItems($cartItems);
        $data["ItemNames"] = $this->getItemNames($cartItems);

        return $data;
    }

    public function getStartCheckoutData()
    {

        $cart = \XLite::getController()->getCart();

        $data = [
            "\$event_id" => $this->getUniqueNumber() . "_" . time(),
            "\$value" => $cart->getTotal(),
            "CheckoutURL" => \XLite::getController()->getShopURL('?target=checkout')
        ];

        $cartItems = $cart->getItems();

        $data['Items'] = $this->getItems($cartItems);
        $data["ItemNames"] = $this->getItemNames($cartItems);
        $data["Categories"] = $this->getItemCategories($cartItems);

        return $data;
    }

    public function getViewedProductData($product)
    {
        $productData = [
            "title" => $product->getName(),
            "sku" => $product->getVariant() ? $product->getVariant()->getSku() : $product->getSku(),
            "categories" => $this->getProductCategories($product),
            "image" => $product->getImageURL(),
            "url" => $product->getURL(),
            "brand" => $product->getBrandName(),
            "price" => $product->getPrice(),
            "market_price" => $product->getNetMarketPrice()
        ];

        $data = [
            "viewedProduct" => [
                "ProductName" => $productData['title'],
                "ProductID" => $productData['sku'],
                "SKU" => $productData['sku'],
                "Categories" => $productData['categories'],
                "ImageURL" => $productData['image'],
                "URL" => $productData['url'],
                "Brand" => $productData['brand'],
                "Price" => $productData['price'],
            ],
            "trackViewedItem" => [
                "Title" => $productData['title'],
                "ItemId" => $productData['sku'],
                "Categories" => $productData['categories'],
                "ImageUrl" => $productData['image'],
                "Url" => $productData['url'],
                "Metadata" => [
                    "Brand" => $productData['brand'],
                    "Price" => $productData['price'],
                ]
            ]
        ];

        if ($productData['market_price']) {
            $data["viewedProduct"]["CompareAtPrice"] = $productData['market_price'];
            $data["trackViewedItem"]["Metadata"]["CompareAtPrice"] = $productData['market_price'];
        }

        return $data;
    }

    public function getProductCategories($product)
    {
        $categories = [];
        foreach ($product->getCategories() as $category) {
            $categories[] = $category->getName();
        }
        return $categories;
    }

    public function getItemNames($items)
    {
        $items = $items->toArray();

        return array_map(
            function ($item) {
                return $item->getName();
            },
            $items
        );
    }

    public function getItemCategories($items)
    {
        $items = $items->toArray();

        return array_map(
            function ($item) {
                $product = $item->getProduct();
                if (!$product->getCategories()[0])
                    return 'Products';

                return $product->getCategories()[0]->getName();
            },
            $items
        );
    }

    public function getItemBrands($items)
    {
        $items = $items->toArray();

        return array_map(
            function ($item) {
                $product = $item->getProduct();

                return $product->getBrandName();
            },
            $items
        );
    }


    public function getItems($cartItems)
    {
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
        return $items;
    }

    public function getOrderDate()
    {
        date_default_timezone_set('UTC');

        $date = date('Y-m-d\TH:i:s\Z');

        date_default_timezone_set(\XLite\Core\Converter::getTimeZone()->getName());

        return $date;
    }

    public static function getUniqueNumber()
    {
        $uniqueNumber = Session::getInstance()->kl_unique_number;
        if (!$uniqueNumber) {
            $uniqueNumber = self::generateUniqueNumber();
            Session::getInstance()->kl_unique_number = $uniqueNumber;
        }

        return $uniqueNumber;
    }
    private static function generateUniqueNumber()
    {
        $timestamp = time();
        $randomNumber = mt_rand(100, 999);

        $uniqueNumber = substr($timestamp . $randomNumber, 5);

        return intval($uniqueNumber);
    }
    protected function __construct()
    {
        parent::__construct();
    }
}
