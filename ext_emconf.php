<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Member',
    'description' => 'Mitglieder-Verwaltung (Record & Bewerbung) – verwaltet Mitglieder, Bewerbungen und Status-Workflows.',
    'category' => 'plugin',
    'author' => 'Joel Maximilian Mai',
    'author_email' => '',
    'state' => 'alpha',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '12.0.0-12.99.99',
            'extbase' => '12.0.0-12.99.99',
            'fluid' => '12.0.0-12.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
