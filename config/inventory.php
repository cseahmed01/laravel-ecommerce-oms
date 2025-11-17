<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Low Stock Threshold
    |--------------------------------------------------------------------------
    |
    | This value determines the stock level at which a low stock alert
    | will be triggered for product variants.
    |
    */

    'low_stock_threshold' => env('LOW_STOCK_THRESHOLD', 10),
];