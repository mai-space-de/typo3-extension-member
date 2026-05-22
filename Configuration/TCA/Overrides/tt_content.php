<?php

declare(strict_types=1);

defined('TYPO3') or die();

use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

ExtensionUtility::registerPlugin(
    'MaiMember',
    'View',
    'LLL:EXT:mai_member/Resources/Private/Language/locallang_db.xlf:tt_content.CType.mai_member_view',
    'mai-content',
    'default',
    '',
    'FILE:EXT:mai_member/Configuration/FlexForms/Members.xml',
);

ExtensionUtility::registerPlugin(
    'MaiMember',
    'Application',
    'LLL:EXT:mai_member/Resources/Private/Language/locallang_db.xlf:tt_content.CType.mai_member_application',
    'mai-content',
    'default',
);
