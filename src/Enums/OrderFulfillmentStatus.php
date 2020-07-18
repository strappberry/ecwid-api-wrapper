<?php

namespace Strappberry\EcwidApi\Enums;

use MyCLabs\Enum\Enum;

/**
 * Class OrderFulfillmentStatus
 * @method static OrderFulfillmentStatus AWAITING_PROCESSING()
 * @method static OrderFulfillmentStatus PROCESSING()
 * @method static OrderFulfillmentStatus SHIPPED()
 * @method static OrderFulfillmentStatus DELIVERED()
 * @method static OrderFulfillmentStatus WILL_NOT_DELIVER()
 * @method static OrderFulfillmentStatus RETURNED()
 * @method static OrderFulfillmentStatus READY_FOR_PICKUP()
 * @package Strappberry\EciwdApi\Enums
 */
class OrderFulfillmentStatus extends Enum {
    private const AWAITING_PROCESSING = 'AWAITING_PROCESSING';
    private const PROCESSING = 'PROCESSING';
    private const SHIPPED = 'SHIPPED';
    private const DELIVERED = 'DELIVERED';
    private const WILL_NOT_DELIVER = 'WILL_NOT_DELIVER';
    private const RETURNED = 'RETURNED';
    private const READY_FOR_PICKUP = 'READY_FOR_PICKUP';
}