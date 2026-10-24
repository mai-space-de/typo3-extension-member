CREATE TABLE tx_maimember_application (
    first_name varchar(255) DEFAULT '' NOT NULL,
    last_name varchar(255) DEFAULT '' NOT NULL,
    email varchar(255) DEFAULT '' NOT NULL,
    message text,
    status varchar(20) DEFAULT 'pending' NOT NULL,
    submitted_at int(11) unsigned DEFAULT '0' NOT NULL,
    member int(11) unsigned DEFAULT '0' NOT NULL
);

# Member doktype (123) metadata on pages — People BGM-47
CREATE TABLE pages (
    tx_maimember_first_name varchar(255) DEFAULT '' NOT NULL,
    tx_maimember_last_name varchar(255) DEFAULT '' NOT NULL,
    tx_maimember_position varchar(255) DEFAULT '' NOT NULL,
    tx_maimember_email varchar(255) DEFAULT '' NOT NULL,
    tx_maimember_phone varchar(100) DEFAULT '' NOT NULL,
    tx_maimember_status varchar(20) DEFAULT 'active' NOT NULL,
    tx_maimember_join_date int(11) unsigned DEFAULT '0' NOT NULL,
    tx_maimember_image int(11) unsigned DEFAULT '0' NOT NULL,
    tx_maimember_fe_user int(11) unsigned DEFAULT '0' NOT NULL,
    tx_maimember_legacy_member_uid int(11) DEFAULT 0 NOT NULL
);
