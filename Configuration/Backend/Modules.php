<?php

declare(strict_types=1);

use Maispace\MaiMember\Controller\Backend\MemberBackendController;

return [
    'mai_member' => [
        'parent' => 'web',
        'access' => 'user',
        'workspaces' => 'online',
        'path' => '/module/mai-member',
        'iconIdentifier' => 'ext-maispace-mai_member',
        'labels' => 'LLL:EXT:mai_member/Resources/Private/Language/locallang_mod.xlf',
        'extensionName' => 'MaiMember',
        'controllerActions' => [
            MemberBackendController::class => ['index', 'approve', 'reject', 'exportCsv'],
        ],
    ],
];
