<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Print Labels',

            'form' => [
                'fields' => [
                    'quantity' => 'Number of Labels',
                    'format'   => 'Format',

                    'format-options' => [
                        'dymo'       => 'Dymo',
                        '2x7_price'  => '2x7 with price',
                        '4x7_price'  => '4x7 with price',
                        '4x12'       => '4x12',
                        '4x12_price' => '4x12 with price',
                    ],
                ],
            ],
        ],

        'delete' => [
            'notification' => [
                'title' => 'Product Deleted',
                'body'  => 'The product has been deleted successfully.',
            ],
        ],
    ],
];
