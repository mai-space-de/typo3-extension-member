<?php

declare(strict_types=1);

use Maispace\MaiBase\TableConfigurationArray\FieldConfig\DatetimeConfig;
use Maispace\MaiBase\TableConfigurationArray\FieldConfig\EmailConfig;
use Maispace\MaiBase\TableConfigurationArray\FieldConfig\FileConfig;
use Maispace\MaiBase\TableConfigurationArray\FieldConfig\InputConfig;
use Maispace\MaiBase\TableConfigurationArray\FieldConfig\SelectSingleConfig;
use Maispace\MaiBase\TableConfigurationArray\Helper;
use Maispace\MaiBase\TableConfigurationArray\Table;

$lang = Helper::localLangHelperFactory('mai_member', 'Default/locallang_tca.xlf');

return (new Table($lang('table.tx_maimember_member')))
    ->setDefaultConfig()
    ->setLabel('last_name')
    ->setAlternativeLabelFields('first_name')
    ->appendAlternativeLabelToLabel()
    ->setSearchFields('first_name, last_name, email, phone')
    ->setIconFile('EXT:mai_member/Resources/Public/Icons/tx_maimember_member.svg')
    ->setDefaultSorting('ORDER BY last_name ASC, first_name ASC')
    ->setThumbnailField('image')
    ->addColumn(
        'first_name',
        $lang('tx_maimember_member.first_name'),
        (new InputConfig())->setSize(30)->setMax(100)->setEval('trim,required')
    )
    ->addColumn(
        'last_name',
        $lang('tx_maimember_member.last_name'),
        (new InputConfig())->setSize(30)->setMax(100)->setEval('trim,required')
    )
    ->addColumn(
        'email',
        $lang('tx_maimember_member.email'),
        (new EmailConfig())->setEval('required')
    )
    ->addColumn(
        'phone',
        $lang('tx_maimember_member.phone'),
        (new InputConfig())->setSize(20)->setMax(30)->setEval('trim')
    )
    ->addColumn(
        'status',
        $lang('tx_maimember_member.status'),
        (new SelectSingleConfig())
            ->setItems([
                ['label' => $lang('tx_maimember_member.status.active'), 'value' => 'active'],
                ['label' => $lang('tx_maimember_member.status.inactive'), 'value' => 'inactive'],
            ])
            ->setDefault('active')
    )
    ->addColumn(
        'join_date',
        $lang('tx_maimember_member.join_date'),
        (new DatetimeConfig())->setFormat('date')
    )
    ->addColumn(
        'image',
        $lang('tx_maimember_member.image'),
        (new FileConfig())
            ->setAllowed('common-image-types')
            ->setMaxItems(1)
            ->setAppearance([
                'createNewRelationLinkTitle' => $lang('tx_maimember_member.image.addFile'),
            ])
    )
    ->addColumn(
        'fe_user',
        $lang('tx_maimember_member.fe_user'),
        (new SelectSingleConfig())
            ->setForeignTable('fe_users')
            ->setForeignTableWhere('ORDER BY fe_users.username')
            ->setItems([['label' => '', 'value' => 0]])
            ->setMinItems(0)
            ->setMaxItems(1)
    )
    ->addPalette(
        'name',
        $lang('palette.name'),
        'first_name, last_name'
    )
    ->addPalette(
        'contact',
        $lang('palette.contact'),
        'email, phone'
    )
    ->addTypeShowItem(
        '0',
        '--palette--;;name, image,
        --div--;' . $lang('tab.contact') . ', --palette--;;contact,
        --div--;' . $lang('tab.meta') . ', status, join_date, fe_user,
        --div--;' . $lang('tab.language') . ', --palette--;;language,
        --div--;' . $lang('tab.access') . ', --palette--;;hidden, --palette--;;access'
    )
    ->getConfig();
