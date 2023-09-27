create table tblCourtGrouping
(
    id           int auto_increment
        primary key,
    siteid       int          default 0                 not null,
    name         varchar(255) default ''                not null,
    lastmodified timestamp    default CURRENT_TIMESTAMP not null
)
    engine = MyISAM;

