<?php

declare(strict_types=1);

return [
    \Maispace\MaiMember\Domain\Model\Member::class => [
        'tableName' => 'pages',
        'properties' => [
            'title' => [
                'fieldName' => 'title',
            ],
            'slug' => [
                'fieldName' => 'slug',
            ],
            'doktype' => [
                'fieldName' => 'doktype',
            ],
            'firstName' => [
                'fieldName' => 'tx_maimember_first_name',
            ],
            'lastName' => [
                'fieldName' => 'tx_maimember_last_name',
            ],
            'position' => [
                'fieldName' => 'tx_maimember_position',
            ],
            'email' => [
                'fieldName' => 'tx_maimember_email',
            ],
            'phone' => [
                'fieldName' => 'tx_maimember_phone',
            ],
            'status' => [
                'fieldName' => 'tx_maimember_status',
            ],
            'joinDate' => [
                'fieldName' => 'tx_maimember_join_date',
            ],
            'image' => [
                'fieldName' => 'tx_maimember_image',
            ],
            'feUser' => [
                'fieldName' => 'tx_maimember_fe_user',
            ],
        ],
    ],
    \Maispace\MaiMember\Domain\Model\Application::class => [
        'tableName' => 'tx_maimember_application',
    ],
];
