<?php

declare(strict_types=1);

defined('TYPO3') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(
    [
        'label' => 'LLL:EXT:mai_member/Resources/Private/Language/locallang_db.xlf:tt_content.CType.mai_member_view',
        'value' => 'mai_member_view',
        'icon' => 'EXT:mai_member/Resources/Public/Icons/ContentElement/MembersView.svg',
        'group' => 'default',
    ],
    'CType',
    'mai_member'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(
    [
        'label' => 'LLL:EXT:mai_member/Resources/Private/Language/locallang_db.xlf:tt_content.CType.mai_member_application',
        'value' => 'mai_member_application',
        'icon' => 'EXT:mai_member/Resources/Public/Icons/ContentElement/MembersView.svg',
        'group' => 'default',
    ],
    'CType',
    'mai_member'
);

$GLOBALS['TCA']['tt_content']['types']['mai_member_view'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,
            header,
            pi_flexform,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,
            --palette--;;access,
    ',
    'columnsOverrides' => [
        'pi_flexform' => [
            'label' => 'LLL:EXT:mai_member/Resources/Private/Language/locallang_db.xlf:tt_content.pi_flexform.mai_member_view',
            'config' => ['ds' => 'FILE:EXT:mai_member/Configuration/FlexForms/Members.xml'],
        ],
    ],
];

$GLOBALS['TCA']['tt_content']['types']['mai_member_application'] = [
    'showitem' => '
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;;general,
            header,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,
            --palette--;;access,
    ',
];
