<?php

declare(strict_types=1);

return [
    \Maispace\MaiMember\Domain\Model\Member::class => [
        'tableName' => 'tx_maimember_member',
    ],
    \Maispace\MaiMember\Domain\Model\Application::class => [
        'tableName' => 'tx_maimember_application',
    ],
];
