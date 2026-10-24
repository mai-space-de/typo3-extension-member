<?php

declare(strict_types=1);

defined('TYPO3') or die();

(static function (): void {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'MaiMember',
        'View',
        [\Maispace\MaiMember\Controller\MemberController::class => 'list'],
        [],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'MaiMember',
        'List',
        [
            \Maispace\MaiMember\Controller\MemberController::class => 'list',
        ],
        [],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT,
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'MaiMember',
        'Detail',
        [
            \Maispace\MaiMember\Controller\MemberController::class => 'detail',
        ],
        [],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT,
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'MaiMember',
        'Application',
        [\Maispace\MaiMember\Controller\ApplicationController::class => 'form,submit'],
        [\Maispace\MaiMember\Controller\ApplicationController::class => 'submit'],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['mai_member']
        = \Maispace\MaiMember\Hook\MemberCacheInvalidationHook::class;

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass']['mai_member']
        = \Maispace\MaiMember\Hook\MemberCacheInvalidationHook::class;
})();
