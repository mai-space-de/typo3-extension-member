<?php
$EM_CONF[$_EXTKEY] = [
    "title" => "Mai Member",
    "description" =>
        "Member management extension for managing organisation members and handling applications with status workflows. FE-User linking requires `mai_account`.",
    "category" => "module",
    "author" => "Maispace",
    "author_email" => "",
    "state" => "stable",
    "version" => "1.0.0",
    "constraints" => [
        "depends" => [
            "typo3" => "13.4.0-14.99.99",
            "mai_base" => "13.0.0-14.99.99",
            "mai_mail" => "13.0.0-14.99.99",
        ],
        "conflicts" => [],
        "suggests" => [
            "mai_account" => "FE-User linking on member records",
        ],
    ],
];
