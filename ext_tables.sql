CREATE TABLE tx_maimember_member (
    first_name varchar(255) DEFAULT '' NOT NULL,
    last_name varchar(255) DEFAULT '' NOT NULL,
    email varchar(255) DEFAULT '' NOT NULL,
    phone varchar(100) DEFAULT '' NOT NULL,
    status varchar(20) DEFAULT 'active' NOT NULL,
    join_date int(11) unsigned DEFAULT '0' NOT NULL,
    image int(11) unsigned DEFAULT '0' NOT NULL,
    fe_user int(11) unsigned DEFAULT '0' NOT NULL
);

CREATE TABLE tx_maimember_application (
    first_name varchar(255) DEFAULT '' NOT NULL,
    last_name varchar(255) DEFAULT '' NOT NULL,
    email varchar(255) DEFAULT '' NOT NULL,
    message text,
    status varchar(20) DEFAULT 'pending' NOT NULL,
    submitted_at int(11) unsigned DEFAULT '0' NOT NULL,
    member int(11) unsigned DEFAULT '0' NOT NULL
);
