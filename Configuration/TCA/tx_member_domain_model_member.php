<?php

declare(strict_types=1);

return [
    'ctrl' => [
        'title' => 'LLL:EXT:member/Resources/Private/Language/locallang_db.xlf:tx_member_domain_model_member',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'sortby' => 'sorting',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'searchFields' => 'name,interests',
        'iconfile' => 'EXT:member/Resources/Public/Icons/tx_member_domain_model_member.svg',
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
    ],
    'types' => [
        '1' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    name, status, entry_date, interests, photo, fe_user,
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
        'name' => [
            'exclude' => false,
            'label' => 'LLL:EXT:member/Resources/Private/Language/locallang_db.xlf:tx_member_domain_model_member.name',
            'config' => [
                'type' => 'input',
                'size' => 40,
                'eval' => 'trim',
                'required' => true,
                'max' => 255,
            ],
        ],
        'status' => [
            'exclude' => false,
            'label' => 'LLL:EXT:member/Resources/Private/Language/locallang_db.xlf:tx_member_domain_model_member.status',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'label' => 'LLL:EXT:member/Resources/Private/Language/locallang_db.xlf:tx_member_domain_model_member.status.0',
                        'value' => 0,
                    ],
                    [
                        'label' => 'LLL:EXT:member/Resources/Private/Language/locallang_db.xlf:tx_member_domain_model_member.status.1',
                        'value' => 1,
                    ],
                    [
                        'label' => 'LLL:EXT:member/Resources/Private/Language/locallang_db.xlf:tx_member_domain_model_member.status.2',
                        'value' => 2,
                    ],
                ],
                'default' => 0,
            ],
        ],
        'entry_date' => [
            'exclude' => false,
            'label' => 'LLL:EXT:member/Resources/Private/Language/locallang_db.xlf:tx_member_domain_model_member.entry_date',
            'config' => [
                'type' => 'datetime',
                'format' => 'date',
                'size' => 13,
                'nullable' => true,
                'default' => null,
            ],
        ],
        'photo' => [
            'exclude' => false,
            'label' => 'LLL:EXT:member/Resources/Private/Language/locallang_db.xlf:tx_member_domain_model_member.photo',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                'photo',
                [
                    'appearance' => [
                        'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:images.addFileReference',
                    ],
                    'overrideChildTca' => [
                        'types' => [
                            '0' => ['showitem' => '--linebreak--,title,description,--linebreak--,alternative'],
                            \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                                'showitem' => '--linebreak--,title,description,--linebreak--,alternative',
                            ],
                        ],
                    ],
                    'minitems' => 0,
                    'maxitems' => 1,
                ],
                'jpg,jpeg,png,gif,webp'
            ),
        ],
        'interests' => [
            'exclude' => false,
            'label' => 'LLL:EXT:member/Resources/Private/Language/locallang_db.xlf:tx_member_domain_model_member.interests',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 5,
                'eval' => 'trim',
            ],
        ],
        'fe_user' => [
            'exclude' => false,
            'label' => 'LLL:EXT:member/Resources/Private/Language/locallang_db.xlf:tx_member_domain_model_member.fe_user',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'fe_users',
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
