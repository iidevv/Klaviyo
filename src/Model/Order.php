<?php

namespace Iidev\Klaviyo\Model;

use XCart\Extender\Mapping\Extender;
use Iidev\Klaviyo\Core\BackendTracking;


/**
 * Class represents an order
 *
 * @Extender\Mixin
 */
abstract class Order extends \XLite\Model\Order
{
    public function setPaymentStatus($paymentStatus = null)
    {
        parent::setPaymentStatus($paymentStatus);

        if (!$this->getPaymentStatus())
            return;

        $paymentStatus = $this->getPaymentStatus()->getCode();
        $oldStatus = $this->getOldPaymentStatusCode();

        $tracking = new BackendTracking;

        $order = \XLite\Core\Database::getRepo('XLite\Model\Order')->find($this->getOrderId());

        if (!$order instanceof \XLite\Model\Order) {
            return;
        }

        if ($paymentStatus === $oldStatus) {
            return;
        }

        if ($paymentStatus === \XLite\Model\Order\Status\Payment::STATUS_PAID) {
            $tracking->doProcessedOrder($order);
        }
        if ($paymentStatus === \XLite\Model\Order\Status\Payment::STATUS_CANCELED) {
            $tracking->doCancelledOrder($order);
        }
        if ($paymentStatus === \XLite\Model\Order\Status\Payment::STATUS_REFUNDED) {
            $tracking->doRefundedOrder($order);
        }
    }

    public function setShippingStatus($shippingStatus = null)
    {
        parent::setShippingStatus($shippingStatus);

        if (!$this->getShippingStatus())
            return;

        $shippingStatus = $this->getShippingStatus()->getCode();

        $paymentStatus = $this->getPaymentStatus()->getCode();

        if ($paymentStatus === \XLite\Model\Order\Status\Payment::STATUS_PAID && $shippingStatus === \XLite\Model\Order\Status\Shipping::STATUS_SHIPPED) {
            $tracking = new BackendTracking;

            $order = \XLite\Core\Database::getRepo('XLite\Model\Order')->find($this->getOrderId());

            if (!$order instanceof \XLite\Model\Order) {
                return;
            }

            $tracking->doFulfilledOrder($order);
        }
    }

}
