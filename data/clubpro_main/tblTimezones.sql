create table tblTimezones
(
    tzid         tinyint auto_increment
        primary key,
    name         text                                not null,
    offset       tinyint   default 0                 not null,
    lastmodified timestamp default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP
)
    engine = MyISAM;

