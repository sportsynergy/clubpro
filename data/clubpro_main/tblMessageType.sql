create table tblMessageType
(
    messagetypeid smallint    not null
        primary key,
    name          varchar(32) not null
)
    engine = MyISAM
    charset = utf8;

