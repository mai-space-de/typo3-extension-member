<?php

declare(strict_types=1);

return [
    'ctrl' => [
        'title' => 'LLL:EXT:member/Resources/Private/Language/locallang_db.xlf:tx_maimember_domain_model_memberapplication',
        'label' => 'applicant_name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'sortby' => 'sorting',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'searchFields' => 'applicant_name,email,motivation',
        'iconfile' => 'EXT:member/Resources/Public/Icons/tx_maimember_domain_model_memberapplication.svg',
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
    ],
    'types' => [
        '1' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    applicant_name, email, motivation, status, documents, member,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    hidden,
            ',
        ],
    ],
    'columns' => [
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        'label' => '',
                        'invertStateDisplay' => true,
                    ],
                ],
            ],
        ],
        'applicant_name' => [
            'exclude' => false,
            'label' => 'LLL:EXT:member/Resources/Private/Language/locallang_db.xlf:tx_maimember_domain_model_memberapplication.applicant_name',
            'config' => [
                'type' => 'input',
                'size' => 40,
                'eval' => 'trim',
                'required' => true,
                'max' => 255,
            ],
        ],
        'email' => [
            'exclude' => false,
            'label' => 'LLL:EXT:member/Resources/Private/Language/locallang_db.xlf:tx_maimember_domain_model_memberapplication.email',
            'config' => [
                'type' => 'email',
                'size' => 40,
                'required' => true,
                'max' => 255,
            ],
        ],
        'motivation' => [
            'exclude' => false,
            'label' => 'LLL:EXT:member/Resources/Private/Language/locallang_db.xlf:tx_maimember_domain_model_memberapplication.motivation',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 8,
                'eval' => 'trim',
            ],
        ],
        'status' => [
            'exclude' => false,
            'label' => 'LLL:EXT:member/Resources/Private/Language/locallang_db.xlf:tx_maimember_domain_model_memberapplication.status',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'label' => 'LLL:EXT:member/Resources/Private/Language/locallang_db.xlf:tx_maimember_domain_model_memberapplication.status.0',
                        'value' => 0,
                    ],
                    [
                        'label' => 'LLL:EXT:member/Resources/Private/Language/locallang_db.xlf:tx_maimember_domain_model_memberapplication.status.1',
                        'value' => 1,
                    ],
                    [
                        'label' => 'LLL:EXT:member/Resources/Private/Language/locallang_db.xlf:tx_maimember_domain_model_memberapplication.status.2',
                        'value' => 2,
                    ],
                    [
                        'label' => 'LLL:EXT:member/Resources/Private/Language/locallang_db.xlf:tx_maimember_domain_model_memberapplication.status.3',
                        'value' => 3,
                    ],
                ],
                'default' => 0,
            ],
        ],
        'documents' => [
            'exclude' => false,
            'label' => 'LLL:EXT:member/Resources/Private/Language/locallang_db.xlf:tx_maimember_domain_model_memberapplication.documents',
            'config' => [
                'type' => 'file',
                'minitems' => 0,
                'maxitems' => 10,
                'appearance' => [
                    'createNewRelationLinkTitle' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.addFileReference',
                ],
            ],
        ],
        'member' => [
            'exclude' => false,
            'label' => 'LLL:EXT:member/Resources/Private/Language/locallang_db.xlf:tx_maimember_domain_model_memberapplication.member',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tx_maimember_domain_model_member',
                'items' => [
                    [
                        'label' => '',
                        'value' => 0,
                    ],
                ],
                'default' => 0,
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
    ],
];
