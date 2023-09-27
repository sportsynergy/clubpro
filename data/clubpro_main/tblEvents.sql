create table tblEvents
(
    eventid      smallint(4) auto_increment
        primary key,
    eventname    text                                not null,
    siteid       mediumint default 0                 not null,
    playerlimit  tinyint                             not null,
    lastmodified timestamp default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP
)
    engine = MyISAM;

create index siteid
    on tblEvents (siteid);

