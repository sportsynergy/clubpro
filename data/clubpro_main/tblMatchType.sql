create table clubpro_main.tblMatchType
(
    id           smallint     default 0                 not null
        primary key,
    name         varchar(255) default ''                not null,
    lastmodified timestamp    default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP
)
    engine = MyISAM;

