#
# Table structure for table 'tx_mkoptin_domain_model_optin'
#
CREATE TABLE tx_mkoptin_domain_model_optin
(
    uid             int(11) NOT NULL auto_increment,
    pid             int(11) DEFAULT '0' NOT NULL,

    email           varchar(255) DEFAULT '' NOT NULL,
    is_validated    tinyint(4) unsigned DEFAULT '0' NOT NULL,
    validation_hash varchar(32)  DEFAULT '' NOT NULL,
    validation_date datetime     DEFAULT NULL,

    tstamp          int(11) unsigned DEFAULT '0' NOT NULL,
    crdate          int(11) unsigned DEFAULT '0' NOT NULL,
    deleted         smallint(5) unsigned DEFAULT '0' NOT NULL,
    hidden          smallint(5) unsigned DEFAULT '0' NOT NULL,

    PRIMARY KEY (uid),
    KEY             parent (pid),
);
