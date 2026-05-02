<?php

return [
    /**
     * Define which OCR engine to use
     */
    'ocr_engine' => 'tesseract',

    /**
     * Available OCR engines and their configuration
     */
    'engines' => [
        'tesseract' => [
            'class' => 'Tesseract',
            'executable' => env('TESSERACT_PATH', 'tesseract'),
            'lang' => env('OCR_LANG', 'ind'), // Default to Indonesian
        ],
    ],
];
