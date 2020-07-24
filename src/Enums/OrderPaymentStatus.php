<?php

namespace Strappberry\EcwidApi\Enums;

use MyCLabs\Enum\Enum;

/**
 * Class OrderPaymentStatus
 * @method static OrderPaymentStatus AWAITING_PAYMENT()
 * @method static OrderPaymentStatus PAID()
 * @method static OrderPaymentStatus CANCELLED()
 * @method static OrderPaymentStatus REFUNDED()
 * @method static OrderPaymentStatus PARTIALLY_REFUNDED()
 * @package Strappberry\EciwdApi\Enums
 */
class OrderPaymentStatus extends Enum
{
    private const AWAITING_PAYMENT = 'AWAITING_PAYMENT';
    private const PAID = 'PAID';
    private const CANCELLED = 'CANCELLED';
    private const REFUNDED = 'REFUNDED';
    private const PARTIALLY_REFUNDED = 'PARTIALLY_REFUNDED';
}