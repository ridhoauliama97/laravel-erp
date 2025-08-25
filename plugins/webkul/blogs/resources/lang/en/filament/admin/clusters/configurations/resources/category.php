<?php

return [
    'navigation' => [
        'title' => 'Categories',
        'group' => 'Blog',
    ],

    'form' => [
        'fields' => [
            'name'             => 'Name',
            'name-placeholder' => 'Category title ...',
            'sub-title'        => 'Sub Title',
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'name',
            'sub-title'  => 'Sub Title',
            'posts'      => 'Posts',
            'created-at' => 'Created At',
        ],

        'filters' => [
            'is-published' => 'Is Published',
            'author'       => 'Author',
            'creator'      => 'Created By',
            'category'     => 'Category',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Category updated',
                    'body'  => 'The category has been updated successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Category restored',
                    'body'  => 'The category has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Category deleted',
                    'body'  => 'The category has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Category force deleted',
                    'body'  => 'The category has been force deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Categories restored',
                    'body'  => 'The categories has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Categories deleted',
                    'body'  => 'The categories has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Categories force deleted',
                    'body'  => 'The categories has been force deleted successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
    ],
];
