<?php

declare(strict_types=1);

defined('TYPO3') or die();

use Maispace\MaiBase\Backend\CustomPageModule\DoktypeIds;
use Maispace\MaiBase\TableConfigurationArray\Field;
use Maispace\MaiBase\TableConfigurationArray\FieldConfig\DatetimeConfig;
use Maispace\MaiBase\TableConfigurationArray\FieldConfig\EmailConfig;
use Maispace\MaiBase\TableConfigurationArray\FieldConfig\FileConfig;
use Maispace\MaiBase\TableConfigurationArray\FieldConfig\InputConfig;
use Maispace\MaiBase\TableConfigurationArray\FieldConfig\NumberConfig;
use Maispace\MaiBase\TableConfigurationArray\FieldConfig\SelectSingleConfig;
use Maispace\MaiBase\TableConfigurationArray\Helper;
use Maispace\MaiBase\TableConfigurationArray\Palette;

$lang = Helper::localLangHelperFactory('mai_member', 'Default/locallang_tca.xlf');
$memberDoktype = (string) DoktypeIds::MEMBER;

(new Field('pages', 'tx_maimember_first_name', $lang('tx_maimember_member.first_name'), true))
    ->setConfig((new InputConfig())->setSize(30)->setMax(100)->setEval('trim')->setRequired())
    ->registerField();

(new Field('pages', 'tx_maimember_last_name', $lang('tx_maimember_member.last_name'), true))
    ->setConfig((new InputConfig())->setSize(30)->setMax(100)->setEval('trim')->setRequired())
    ->registerField();

(new Field('pages', 'tx_maimember_position', $lang('tx_maimember_member.position'), true))
    ->setConfig((new InputConfig())->setSize(30)->setMax(255)->setEval('trim'))
    ->registerField();

(new Field('pages', 'tx_maimember_email', $lang('tx_maimember_member.email'), true))
    ->setConfig(new EmailConfig())
    ->registerField();

(new Field('pages', 'tx_maimember_phone', $lang('tx_maimember_member.phone'), true))
    ->setConfig((new InputConfig())->setSize(20)->setMax(30)->setEval('trim'))
    ->registerField();

(new Field('pages', 'tx_maimember_status', $lang('tx_maimember_member.status'), true))
    ->setConfig(
        (new SelectSingleConfig())
            ->setItems([
                ['label' => $lang('tx_maimember_member.status.active'), 'value' => 'active'],
                ['label' => $lang('tx_maimember_member.status.inactive'), 'value' => 'inactive'],
            ])
            ->setDefault('active')
    )
    ->registerField();

(new Field('pages', 'tx_maimember_join_date', $lang('tx_maimember_member.join_date'), true))
    ->setConfig((new DatetimeConfig())->setFormat('date'))
    ->registerField();

(new Field('pages', 'tx_maimember_image', $lang('tx_maimember_member.image'), true))
    ->setConfig(
        (new FileConfig())
            ->setAllowed('common-image-types')
            ->setMaxItems(1)
            ->setAppearance([
                'createNewRelationLinkTitle' => $lang('tx_maimember_member.image.addFile'),
            ])
    )
    ->registerField();

(new Field('pages', 'tx_maimember_fe_user', $lang('tx_maimember_member.fe_user'), true))
    ->setConfig(
        (new SelectSingleConfig())
            ->setForeignTable('fe_users')
            ->setForeignTableWhere('ORDER BY fe_users.username')
            ->setItems([['label' => '', 'value' => 0]])
            ->setMinItems(0)
            ->setMaxItems(1)
    )
    ->registerField();

// Migration idempotency marker (hidden from editors).
(new Field('pages', 'tx_maimember_legacy_member_uid', $lang('tx_maimember_member.legacy_member_uid'), true))
    ->setConfig((new NumberConfig())->setFormat('integer')->setDefault(0)->setReadOnly())
    ->registerField();

(new Palette('pages', 'tx_maimember_name', $lang('palette.name')))
    ->override('tx_maimember_first_name, tx_maimember_last_name');

(new Palette('pages', 'tx_maimember_contact', $lang('palette.contact')))
    ->override('tx_maimember_email, tx_maimember_phone');

(new Palette('pages', 'tx_maimember_meta', $lang('palette.memberMeta')))
    ->override('tx_maimember_status, tx_maimember_join_date, tx_maimember_fe_user');

$memberTabs = ',--div--;' . $lang('tab.member')
    . ',--palette--;;tx_maimember_name,'
    . 'tx_maimember_position,tx_maimember_image,'
    . '--palette--;;tx_maimember_contact,'
    . '--palette--;;tx_maimember_meta';

$showitem = (string) ($GLOBALS['TCA']['pages']['types'][$memberDoktype]['showitem'] ?? '');
if ($showitem !== '' && !str_contains($showitem, 'tx_maimember_first_name')) {
    $GLOBALS['TCA']['pages']['types'][$memberDoktype]['showitem'] = preg_replace(
        '/(--palette--;;title)/',
        '$1' . $memberTabs,
        $showitem,
        1,
    ) ?? ($showitem . $memberTabs);
}

Helper::overrideColumns('pages', $memberDoktype, 'nav_hide', [
    'config' => [
        'default' => 1,
    ],
]);
