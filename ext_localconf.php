<?php

defined('TYPO3') or die();

(static function (): void {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'MaiMember',
        'MemberList',
        [
            \Maispace\MaiMember\Controller\MemberController::class => 'list,show',
        ],
        [],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'MaiMember',
        'ApplicationForm',
        [
            \Maispace\MaiMember\Controller\MemberApplicationController::class => 'new,create,confirmation',
        ],
        [
            \Maispace\MaiMember\Controller\MemberApplicationController::class => 'new,create',
        ],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );
})();
