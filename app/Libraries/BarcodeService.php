<?php

namespace App\Libraries;

use Picqer\Barcode\BarcodeGeneratorPNG;

class BarcodeService
{
    protected BarcodeGeneratorPNG $generator;

    public function __construct()
    {
        $this->generator = new BarcodeGeneratorPNG();
    }

    /**
     * Generate a barcode for an order ID
     *
     * @param int $orderId The order ID
     * @return string Base64 encoded PNG barcode image
     */
    public function generateBarcode(int $orderId): string
    {
        // Create a unique barcode text using order ID with prefix
        $barcodeText = 'ORD-' . str_pad($orderId, 6, '0', STR_PAD_LEFT);

        // Generate PNG barcode
        $barcodeImage = $this->generator->getBarcode($barcodeText, BarcodeGeneratorPNG::TYPE_CODE_128);

        // Return base64 encoded image for HTML display
        return 'data:image/png;base64,' . base64_encode($barcodeImage);
    }

    /**
     * Generate barcode HTML for display in views
     *
     * @param int $orderId The order ID
     * @param string $width CSS width for the image (default: 300px)
     * @param string $height CSS height for the image (default: 80px)
     * @return string HTML img tag with barcode
     */
    public function getBarcodeHtml(int $orderId, string $width = '300px', string $height = '80px'): string
    {
        $barcodeData = $this->generateBarcode($orderId);
        $barcodeText = 'ORD-' . str_pad($orderId, 6, '0', STR_PAD_LEFT);

        return '<div class="text-center">' .
               '<img src="' . $barcodeData . '" alt="Barcode for Order ' . $orderId . '" style="width: ' . $width . '; height: ' . $height . '; border: 1px solid #ddd; padding: 10px; background: white;" />' .
               '<br><small class="text-muted mt-2 d-block">' . $barcodeText . '</small>' .
               '</div>';
    }
}
