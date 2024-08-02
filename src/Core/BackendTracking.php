<?php

namespace Iidev\Klaviyo\Core;

use Iidev\Klaviyo\Core\Main;
use Iidev\Klaviyo\Core\API;

class BackendTracking
{
    public function doPlacedOrder(\XLite\Model\Order $order)
    {
        $api = new API();

        $attributes = $this->getOrderEventData($order, "Placed Order");

        $api->event($attributes);
    }

    public function doOrderedProduct(\XLite\Model\Order $order)
    {
        $main = Main::getInstance();
        $api = new API();

        $orderId = $order->getOrderId();
        $items = $order->getItems();
        $profile = $order->getProfile();
        $login = trim($profile->getLogin());

        foreach ($items as $item) {
            $product = $item->getProduct();

            $attributes = [
                "properties" => [
                    "OrderId" => $orderId,
                    "ProductId" => $item->getSku(),
                    "SKU" => $item->getSku(),
                    "ProductName" => $item->getName(),
                    "Quantity" => $item->getAmount(),
                    "ProductURL" => $item->getURL(),
                    "ImageURL" => $item->getImageURL(),
                    "Categories" => $main->getProductCategories($product),
                    "ProductBrand" => $product->getBrandName(),
                ],
                "value" => $item->getTotal(),
                "metric" => [
                    "data" => [
                        "type" => "metric",
                        "attributes" => [
                            "name" => "Ordered Product"
                        ]
                    ]
                ],
                "profile" => [
                    "data" => [
                        "type" => "profile",
                        "attributes" => [
                            "email" => $login
                        ]
                    ]
                ],
                "unique_id" => $item->getSku()
            ];

            $api->event($attributes);
        }
    }

    public function doFulfilledOrder(\XLite\Model\Order $order)
    {
        $api = new API();

        $attributes = $this->getOrderEventData($order, "Fulfilled Order");

        $api->event($attributes);
    }

    public function doCancelledOrder(\XLite\Model\Order $order)
    {
        $api = new API();

        $attributes = $this->getOrderEventData($order, "Cancelled Order");

        $api->event($attributes);
    }

    public function doRefundedOrder(\XLite\Model\Order $order)
    {
        $api = new API();

        $attributes = $this->getOrderEventData($order, "Refunded Order");

        $api->event($attributes);
    }

    public function doProcessedOrder(\XLite\Model\Order $order)
    {
        $api = new API();

        $attributes = $this->getOrderEventData($order, "Processed Order");

        $api->event($attributes);
    }

    public function doCreateOrUpdateProfile($attributes)
    {
        $api = new API();

        $api->createOrUpdateProfile($attributes);
    }
    private function getOrderEventData(\XLite\Model\Order $order, $eventName = '')
    {
        $main = Main::getInstance();
        $items = $order->getItems();
        $profile = $order->getProfile();
        $login = trim($profile->getLogin());

        $attributes = [
            "properties" => [
                "OrderId" => $order->getOrderId(),
                "Categories" => $main->getItemCategories($items),
                "ItemNames" => $main->getItemNames($items),
                "Brands" => $main->getItemBrands($items),
                "Items" => $main->getItems($items),
                "BillingAddress" => [
                    "FirstName" => $profile->getBillingAddress()->getFirstname(),
                    "LastName" => $profile->getBillingAddress()->getLastname(),
                    "Address1" => $profile->getBillingAddress()->getStreet(),
                    "City" => $profile->getBillingAddress()->getCity(),
                    "RegionCode" => $profile->getBillingAddress()->getState()->getCode(),
                    "CountryCode" => $profile->getBillingAddress()->getCountry()->getCode(),
                    "Zip" => $profile->getBillingAddress()->getZipcode(),
                    "Phone" => $profile->getBillingAddress()->getPhone()
                ],
                "ShippingAddress" => [
                    "Address1" => $profile->getShippingAddress()->getStreet()
                ]
            ],
            "time" => $main->getOrderDate(),
            "value" => $order->getTotal(),
            "value_currency" => $order->getCurrency()->getCode(),
            "unique_id" => $order->getOrderId(),
            "metric" => [
                "data" => [
                    "type" => "metric",
                    "attributes" => [
                        "name" => $eventName
                    ]
                ]
            ],
            "profile" => [
                "data" => [
                    "type" => "profile",
                    "attributes" => [
                        "email" => $login,
                    ]
                ]
            ]
        ];

        if ($order->getCoupon()) {
            $attributes["DiscountCode"] = $order->getCoupon()->getName();
            $attributes["DiscountValue"] = $order->getCoupon()->getAmount();
        }
        return $attributes;
    }
}