<?php

declare(strict_types=1);

$allIcons = 'EXT:mai_base/Resources/Public/Icons/all-icons/';

return [
    'ext-maispace-mai_member' => [
        'provider' => \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        'source' => 'EXT:mai_member/Resources/Public/Icons/Extension.svg',
    ],
    'mai-member' => [
        'provider' => \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        'source' => $allIcons . 'interface-user-single--close-geometric-human-person-single-up-user--Streamline-Core.svg',
    ],
    'mai-member-application' => [
        'provider' => \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        'source' => $allIcons . 'interface-user-add--actions-add-close-geometric-human-person-plus-single-up-user--Streamline-Core.svg',
    ],
    'mai-member-module' => [
        'provider' => \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        'source' => $allIcons . 'interface-user-check--actions-close-checkmark-check-geometric-human-person-single-success-up-user--Streamline-Core.svg',
    ],
    'tx-maimember-member' => [
        'provider' => \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        'source' => $allIcons . 'interface-user-single--close-geometric-human-person-single-up-user--Streamline-Core.svg',
    ],
    'tx-maimember-application' => [
        'provider' => \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        'source' => $allIcons . 'interface-user-add--actions-add-close-geometric-human-person-plus-single-up-user--Streamline-Core.svg',
    ],
];
