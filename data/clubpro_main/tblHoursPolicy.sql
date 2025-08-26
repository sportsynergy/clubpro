create table clubpro_main.tblHoursPolicy
(
    policyid     int auto_increment
        primary key,
    siteid       int       default 0                 not null,
    day          tinyint   default 0                 not null,
    month        tinyint   default 0                 not null,
    year         int       default 0                 not null,
    opentime     time      default '00:00:00'        not null,
    closetime    time      default '00:00:00'        not null,
    enable       tinyint   default 0                 not null,
    lastmodified timestamp default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP
)
    engine = MyISAM;

create index siteid
    on clubpro_main.tblHoursPolicy (siteid);

