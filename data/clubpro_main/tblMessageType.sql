create table clubpro_main.tblMessageType
(
    messagetypeid smallint    not null
        primary key,
    name          varchar(32) not null
)
    engine = MyISAM
    charset = utf8;

