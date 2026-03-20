<?php

defined('TYPO3') or die();

(static function (): void {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'Member',
        'MemberList',
        'LLL:EXT:member/Resources/Private/Language/locallang_db.xlf:plugin.memberlist.title'
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'Member',
        'ApplicationForm',
        'LLL:EXT:member/Resources/Private/Language/locallang_db.xlf:plugin.applicationform.title'
    );
})();
