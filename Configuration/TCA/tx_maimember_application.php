<?php

declare(strict_types=1);

use Maispace\MaiBase\TableConfigurationArray\Helper;
use Maispace\MaiBase\TableConfigurationArray\Table;

$lang = Helper::localLangHelperFactory('mai_member', 'Default/locallang_tca.xlf');

return (new Table($lang('table.tx_maimember_application')))
    ->setDefaultConfig()
    ->setLabel('last_name')
    ->setAlternativeLabelFields('first_name, email')
    ->appendAlternativeLabelToLabel()
    ->setSearchFields('first_name, last_name, email, message')
    ->setIconFile('EXT:mai_member/Resources/Public/Icons/tx_maimember_application.svg')
    ->setDefaultSorting('ORDER BY submitted_at DESC')
    ->addColumn(
        'first_name',
        $lang('tx_maimember_application.first_name'),
        ['type' => 'input', 'size' => 30, 'max' => 100, 'eval' => 'trim,required']
    )
    ->addColumn(
        'last_name',
        $lang('tx_maimember_application.last_name'),
        ['type' => 'input', 'size' => 30, 'max' => 100, 'eval' => 'trim,required']
    )
    ->addColumn(
        'email',
        $lang('tx_maimember_application.email'),
        ['type' => 'email', 'eval' => 'required']
    )
    ->addColumn(
        'message',
        $lang('tx_maimember_application.message'),
        ['type' => 'text', 'rows' => 10, 'cols' => 50, 'eval' => 'trim']
    )
    ->addColumn(
        'status',
        $lang('tx_maimember_application.status'),
        [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => [
                ['label' => $lang('tx_maimember_application.status.pending'), 'value' => 'pending'],
                ['label' => $lang('tx_maimember_application.status.approved'), 'value' => 'approved'],
                ['label' => $lang('tx_maimember_application.status.rejected'), 'value' => 'rejected'],
            ],
            'default' => 'pending',
        ]
    )
    ->addColumn(
        'submitted_at',
        $lang('tx_maimember_application.submitted_at'),
        ['type' => 'datetime', 'format' => 'datetime', 'readOnly' => true]
    )
    ->addColumn(
        'member',
        $lang('tx_maimember_application.member'),
        [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'foreign_table' => 'tx_maimember_member',
            'foreign_table_where' => 'ORDER BY tx_maimember_member.last_name',
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
    ->addTypeShowItem(
        '0',
        '--palette--;;name, email, message,
        --div--;' . $lang('tab.workflow') . ', status, submitted_at, member,
        --div--;' . $lang('tab.access') . ', --palette--;;hidden'
    )
    ->getConfig();
