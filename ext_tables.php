<?php

defined('TYPO3') or die();

(static function (): void {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'MaiMember',
        'MemberList',
        'LLL:EXT:mai_member/Resources/Private/Language/locallang_db.xlf:plugin.memberlist.title'
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'MaiMember',
        'ApplicationForm',
        'LLL:EXT:mai_member/Resources/Private/Language/locallang_db.xlf:plugin.applicationform.title'
    );
})();
