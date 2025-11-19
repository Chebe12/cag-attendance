<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default QR Code Backend
    |--------------------------------------------------------------------------
    |
    | This option controls the default QR code backend that will be used
    | by the application. You may set this to "gd" or "imagick".
    | GD is more widely available and does not require additional extensions.
    |
    */

    'default' => 'svg', // Use SVG as it doesn't require any extensions
    
    /*
    |--------------------------------------------------------------------------
    | QR Code Size
    |--------------------------------------------------------------------------
    |
    | The default size of the QR code in pixels.
    |
    */

    'size' => 400,
    
    /*
    |--------------------------------------------------------------------------
    | Error Correction Level
    |--------------------------------------------------------------------------
    |
    | Possible values: 'L', 'M', 'Q', 'H'
    | L (Low): ~7% of codewords can be restored
    | M (Medium): ~15% of codewords can be restored
    | Q (Quartile): ~25% of codewords can be restored
    | H (High): ~30% of codewords can be restored
    |
    */

    'error_correction' => 'H',
];
