<?php

return [
    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'General',

                'fields' => [
                    'name' => 'Name',
                    'type' => 'Type',
                ],
            ],

            'options' => [
                'title'  => 'Options',

                'fields' => [
                    'name'        => 'Name',
                    'color'       => 'Color',
                    'extra-price' => 'Extra Price',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'        => 'Name',
            'type'        => 'Type',
            'deleted-at'  => 'Deleted At',
            'created-at'  => 'Created At',
            'updated-at'  => 'Updated At',
        ],

        'groups' => [
            'type'       => 'Type',
            'created-at' => 'Created At',
            'updated-at' => 'Updated At',
        ],

        'filters' => [
            'type' => 'Type',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Attribute restored',
                    'body'  => 'The attribute has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Attribute deleted',
                    'body'  => 'The attribute has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Attribute force deleted',
                        'body'  => 'The attribute has been force deleted successfully.',
                    ],

                    'error' => [
                        'title' => 'Attribute could not be deleted',
                        'body'  => 'The attribute cannot be deleted because it is currently in use.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Attributes restored',
                    'body'  => 'The attributes has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Attributes deleted',
                    'body'  => 'The attributes has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Attributes force deleted',
                        'body'  => 'The attributes has been force deleted successfully.',
                    ],

                    'error' => [
                        'title' => 'Attributes could not be deleted',
                        'body'  => 'The attributes cannot be deleted because they are currently in use.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'General Information',

                'entries' => [
                    'name' => 'Name',
                    'type' => 'Type',
                ],
            ],

            'record-information' => [
                'title' => 'Record Information',

                'entries' => [
                    'creator'    => 'Created By',
                    'created_at' => 'Created At',
                    'updated_at' => 'Last Updated At',
                ],
            ],
        ],
    ],
];
