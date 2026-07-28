<?php

declare(strict_types=1);

defined('TYPO3') or die();

use Maispace\MaiBase\TableConfigurationArray\Helper;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

$lang = Helper::localLangHelperFactory('mai_member', 'Default/locallang_tca.xlf');

// "View" (maimember_view) duplicates "List" (maimember_list) below — same
// MemberController::list,detail mapping. Kept registered (and its FlexForm
// intact) so existing content elements keep rendering, but hidden from the
// wizard via elements_wizard.tsconfig (disabled=1). Do not offer both for
// new content.
ExtensionUtility::registerPlugin(
    'MaiMember',
    'View',
    'LLL:EXT:mai_member/Resources/Private/Language/locallang_db.xlf:tt_content.CType.mai_member_view',
    'mai-content',
    'maispace_plugins_lists',
    '',
    'FILE:EXT:mai_member/Configuration/FlexForms/Members.xml',
);

ExtensionUtility::registerPlugin(
    'MaiMember',
    'Application',
    'LLL:EXT:mai_member/Resources/Private/Language/locallang_db.xlf:tt_content.CType.mai_member_application',
    'mai-content',
    'maispace_plugins_interactive',
);

ExtensionUtility::registerPlugin(
    'MaiMember',
    'List',
    $lang('plugin.list.title'),
    'mai-content',
    'maispace_plugins_lists',
    '',
    'FILE:EXT:mai_member/Configuration/FlexForms/Members.xml',
);
