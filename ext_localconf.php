<?php

declare(strict_types=1);

defined('TYPO3') or die();

(static function (): void {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'MaiMember',
        'View',
        [\Maispace\MaiMember\Controller\MemberController::class => 'list,detail'],
        [],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'MaiMember',
        'Application',
        [\Maispace\MaiMember\Controller\ApplicationController::class => 'form,submit'],
        [\Maispace\MaiMember\Controller\ApplicationController::class => 'submit'],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );
})();
