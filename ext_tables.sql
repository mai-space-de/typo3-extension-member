CREATE TABLE tx_maimember_domain_model_member (
    name varchar(255) DEFAULT '' NOT NULL,
    status int(11) DEFAULT 0 NOT NULL,
    entry_date int(11) DEFAULT NULL,
    photo int(11) unsigned DEFAULT 0 NOT NULL,
    interests text,
    fe_user int(11) unsigned DEFAULT 0 NOT NULL
);

CREATE TABLE tx_maimember_domain_model_memberapplication (
    applicant_name varchar(255) DEFAULT '' NOT NULL,
    email varchar(255) DEFAULT '' NOT NULL,
    motivation text,
    status int(11) DEFAULT 0 NOT NULL,
    documents int(11) unsigned DEFAULT 0 NOT NULL,
    member int(11) unsigned DEFAULT 0 NOT NULL
);
