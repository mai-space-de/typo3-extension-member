<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') or die();

ExtensionManagementUtility::addPlugin(
    [
        'label' => 'LLL:EXT:member/Resources/Private/Language/locallang_db.xlf:plugin.memberlist.title',
        'value' => 'member_memberlist',
        'icon' => 'EXT:member/Resources/Public/Icons/Extension.svg',
        'group' => 'maispace',
    ],
    'CType',
    'member'
);

ExtensionManagementUtility::addPlugin(
    [
        'label' => 'LLL:EXT:member/Resources/Private/Language/locallang_db.xlf:plugin.applicationform.title',
        'value' => 'member_applicationform',
        'icon' => 'EXT:member/Resources/Public/Icons/Extension.svg',
        'group' => 'maispace',
    ],
    'CType',
    'member'
);
