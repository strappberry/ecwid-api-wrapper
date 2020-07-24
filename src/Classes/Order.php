<?php

namespace Strappberry\EcwidApi\Classes;

use Illuminate\Support\Arr;
use Strappberry\EcwidApi\Enums\OrderFulfillmentStatus;
use Strappberry\EcwidApi\Enums\OrderPaymentStatus;

/**
 * Class Order
 * @method self setEmail(string $email)
 * @method self setOrderComments(string $comments)
 * @method self setPrivateAdminNotes(string $admin_notes)
 * @method self setSubTotal(float $sub_total)
 * @method self setTotal(float $total)
 * @package Strappberry\EcwidApi\Classes
 */
class Order extends UnderlayingDataArray
{
    protected $auto_calculate_total = true;

    /**
     * @param  OrderPaymentStatus  $newStatus
     * @return $this
     */
    public function setPaymentStatus(OrderPaymentStatus $newStatus)
    {
        $this->setProperty('paymentStatus', $newStatus->getValue());
        return $this;
    }

    /**
     * @param  OrderFulfillmentStatus  $newStatus
     * @return $this
     */
    public function setFulfillmentStatus(OrderFulfillmentStatus $newStatus)
    {
        $this->setProperty('fulfillmentStatus', $newStatus->getValue());
        return $this;
    }

    public function autoCalculateOrderTotal($autoCalculate = true)
    {
        $this->auto_calculate_total = $autoCalculate;
    }

    public function addItem($item)
    {
        if (!is_array($item) && get_class($item) !== OrderItem::class) {
            throw new \InvalidArgumentException('$item is not a valid type in `addItem` method');
        }
        if (get_class($item) === OrderItem::class) {
            $item = $item->data();
        }

        $this->addPropertyToArray('items', $item);
    }

    public function data()
    {
        if ($this->auto_calculate_total) {
            $order_total = collect(Arr::get($this->data, 'items', []))
                ->sum(function ($product) {
                    $quantity = Arr::get($product, 'quantity', 0);
                    $price = Arr::get($product, 'price', 0);

                    return $quantity * $price;
                });
            $this->setSubTotal($order_total)->setTotal($order_total);
        }

        return parent::data();
    }

}