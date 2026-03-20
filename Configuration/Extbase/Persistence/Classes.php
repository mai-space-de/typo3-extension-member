<?php

declare(strict_types=1);

return [
    \Maispace\Member\Domain\Model\Member::class => [
        'tableName' => 'tx_member_domain_model_member',
        'properties' => [
            'entryDate' => ['fieldName' => 'entry_date'],
            'feUser' => ['fieldName' => 'fe_user'],
        ],
    ],
    \Maispace\Member\Domain\Model\MemberApplication::class => [
        'tableName' => 'tx_member_domain_model_memberapplication',
        'properties' => [
            'applicantName' => ['fieldName' => 'applicant_name'],
        ],
    ],
];
