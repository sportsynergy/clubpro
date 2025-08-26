create table clubpro_main.tblMessages
(
    id            int auto_increment
        primary key,
    siteid        int         default 0                 not null,
    heading       varchar(55) default ''                not null,
    message       text                                  not null,
    messagetypeid smallint                              not null,
    enable        tinyint     default 0                 not null,
    lastmodified  timestamp   default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP
)
    engine = MyISAM
    charset = utf8;

