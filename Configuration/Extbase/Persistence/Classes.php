<?php

declare(strict_types=1);

return [
    \Maispace\MaiMember\Domain\Model\Member::class => [
        'tableName' => 'tx_maimember_domain_model_member',
        'properties' => [
            'entryDate' => ['fieldName' => 'entry_date'],
            'feUser' => ['fieldName' => 'fe_user'],
        ],
    ],
    \Maispace\MaiMember\Domain\Model\MemberApplication::class => [
        'tableName' => 'tx_maimember_domain_model_memberapplication',
        'properties' => [
            'applicantName' => ['fieldName' => 'applicant_name'],
        ],
    ],
];
