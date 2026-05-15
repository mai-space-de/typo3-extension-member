<?php

declare(strict_types=1);

return [
    'ext-maispace-mai_member' => [
        'provider' => \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        'source' => 'EXT:mai_member/Resources/Public/Icons/Extension.svg',
    ],
    'tx-maimember-member' => [
        'provider' => \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        'source' => 'EXT:mai_base/Resources/Public/Icons/generic_table.svg',
    ],
    'tx-maimember-application' => [
        'provider' => \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        'source' => 'EXT:mai_base/Resources/Public/Icons/generic_table.svg',
    ],
];
