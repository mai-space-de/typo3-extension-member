<?php

declare(strict_types=1);

return [
    'web_MemberMemberApplication' => [
        'parent' => 'web',
        'access' => 'user',
        'workspaces' => 'live',
        'iconIdentifier' => 'module-member',
        'labels' => 'LLL:EXT:mai_member/Resources/Private/Language/locallang_mod.xlf',
        'extensionName' => 'MaiMember',
        'controllerActions' => [
            \Maispace\MaiMember\Controller\Backend\MemberApplicationController::class => [
                'index',
                'show',
                'approve',
                'activate',
                'reject',
            ],
        ],
    ],
];
