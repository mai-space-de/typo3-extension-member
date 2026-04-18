<?php

declare(strict_types=1);

use Maispace\MaiBase\TableConfigurationArray\FieldConfig\DatetimeConfig;
use Maispace\MaiBase\TableConfigurationArray\FieldConfig\EmailConfig;
use Maispace\MaiBase\TableConfigurationArray\FieldConfig\InputConfig;
use Maispace\MaiBase\TableConfigurationArray\FieldConfig\SelectSingleConfig;
use Maispace\MaiBase\TableConfigurationArray\FieldConfig\TextConfig;
use Maispace\MaiBase\TableConfigurationArray\Helper;
use Maispace\MaiBase\TableConfigurationArray\Table;

$lang = Helper::localLangHelperFactory('mai_member', 'Default/locallang_tca.xlf');

return (new Table($lang('table.tx_maimember_application')))
    ->setDefaultConfig()
    ->setLabel('last_name')
    ->setAlternativeLabelFields('first_name, email')
    ->appendAlternativeLabelToLabel()
    ->setIconFile('EXT:mai_member/Resources/Public/Icons/tx_maimember_application.svg')
    ->setDefaultSorting('ORDER BY submitted_at DESC')
    ->addColumn(
        'first_name',
        $lang('tx_maimember_application.first_name'),
        (new InputConfig())->setSize(30)->setMax(100)->setEval('trim')->setRequired()
    )
    ->addColumn(
        'last_name',
        $lang('tx_maimember_application.last_name'),
        (new InputConfig())->setSize(30)->setMax(100)->setEval('trim')->setRequired()
    )
    ->addColumn(
        'email',
        $lang('tx_maimember_application.email'),
        (new EmailConfig())->setRequired()
    )
    ->addColumn(
        'message',
        $lang('tx_maimember_application.message'),
        (new TextConfig())->setRows(10)->setCols(50)->setEval('trim')
    )
    ->addColumn(
        'status',
        $lang('tx_maimember_application.status'),
        (new SelectSingleConfig())
            ->setItems([
                ['label' => $lang('tx_maimember_application.status.pending'), 'value' => 'pending'],
                ['label' => $lang('tx_maimember_application.status.approved'), 'value' => 'approved'],
                ['label' => $lang('tx_maimember_application.status.rejected'), 'value' => 'rejected'],
            ])
            ->setDefault('pending')
    )
    ->addColumn(
        'submitted_at',
        $lang('tx_maimember_application.submitted_at'),
        (new DatetimeConfig())->setFormat('datetime')->setReadOnly()
    )
    ->addColumn(
        'member',
        $lang('tx_maimember_application.member'),
        (new SelectSingleConfig())
            ->setForeignTable('tx_maimember_member')
            ->setForeignTableWhere('ORDER BY tx_maimember_member.last_name')
            ->setItems([['label' => '', 'value' => 0]])
            ->setMinItems(0)
            ->setMaxItems(1)
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
