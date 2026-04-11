<?php

declare(strict_types=1);

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
        ['type' => 'input', 'size' => 30, 'max' => 100, 'eval' => 'trim,required']
    )
    ->addColumn(
        'last_name',
        $lang('tx_maimember_member.last_name'),
        ['type' => 'input', 'size' => 30, 'max' => 100, 'eval' => 'trim,required']
    )
    ->addColumn(
        'email',
        $lang('tx_maimember_member.email'),
        ['type' => 'email', 'eval' => 'required']
    )
    ->addColumn(
        'phone',
        $lang('tx_maimember_member.phone'),
        ['type' => 'input', 'size' => 20, 'max' => 30, 'eval' => 'trim']
    )
    ->addColumn(
        'status',
        $lang('tx_maimember_member.status'),
        [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => [
                ['label' => $lang('tx_maimember_member.status.active'), 'value' => 'active'],
                ['label' => $lang('tx_maimember_member.status.inactive'), 'value' => 'inactive'],
            ],
            'default' => 'active',
        ]
    )
    ->addColumn(
        'join_date',
        $lang('tx_maimember_member.join_date'),
        ['type' => 'datetime', 'format' => 'date']
    )
    ->addColumn(
        'image',
        $lang('tx_maimember_member.image'),
        [
            'type' => 'file',
            'allowed' => 'common-image-types',
            'maxitems' => 1,
            'appearance' => [
                'createNewRelationLinkTitle' => $lang('tx_maimember_member.image.addFile'),
            ],
        ]
    )
    ->addColumn(
        'fe_user',
        $lang('tx_maimember_member.fe_user'),
        [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'foreign_table' => 'fe_users',
            'foreign_table_where' => 'ORDER BY fe_users.username',
            'items' => [['label' => '', 'value' => 0]],
            'minitems' => 0,
            'maxitems' => 1,
        ]
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
