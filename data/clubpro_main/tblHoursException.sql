create table clubpro_main.tblHoursException
(
    id           int auto_increment
        primary key,
    time         time      default '00:00:00'        not null,
    siteid       mediumint default 0                 not null,
    courtid      int       default 0                 not null,
    duration     double    default 0                 not null,
    dayid        tinyint   default 0                 not null,
    lastmodified timestamp default CURRENT_TIMESTAMP not null
)
    engine = MyISAM;

create index time
    on clubpro_main.tblHoursException (time);

