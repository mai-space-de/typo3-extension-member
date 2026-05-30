<?php

declare(strict_types=1);

defined('TYPO3') or die();

use Maispace\MaiBase\TableConfigurationArray\CType;
use Maispace\MaiBase\TableConfigurationArray\Helper;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

$lang = Helper::localLangHelperFactory('mai_member', 'Default/locallang_tca.xlf');

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

ExtensionUtility::registerPlugin(
    'MaiMember',
    'List',
    $lang('plugin.list.title'),
    'mai-content',
    'maispace_feature',
);

(new CType('maispace_member_list', $lang('ctype.member_list'), 'mai-content'))
    ->addDefaultHeaderPalette()
    ->addCustomFields('pi_flexform')
    ->addDefaultLanguageTab()
    ->addDefaultAccessTab()
    ->setGroup('maispace_feature')
    ->register();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    '*',
    'FILE:EXT:mai_member/Configuration/FlexForms/Members.xml',
    'maispace_member_list',
);
