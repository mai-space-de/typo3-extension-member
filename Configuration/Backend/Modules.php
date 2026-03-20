<?php

declare(strict_types=1);

return [
    'web_MemberMemberApplication' => [
        'parent' => 'web',
        'access' => 'user',
        'workspaces' => 'live',
        'iconIdentifier' => 'module-member',
        'labels' => 'LLL:EXT:member/Resources/Private/Language/locallang_mod.xlf',
        'extensionName' => 'Member',
        'controllerActions' => [
            \Maispace\Member\Controller\Backend\MemberApplicationController::class => [
                'index',
                'show',
                'approve',
                'activate',
                'reject',
            ],
        ],
    ],
];
