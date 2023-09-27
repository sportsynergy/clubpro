create table clubpro_main.tblReoccuringEvents
(
    id            int auto_increment
        primary key,
    courtid       int       default 0                 not null,
    eventinterval int       default 0                 not null,
    starttime     int       default 0                 not null,
    endtime       int       default 0                 not null,
    lastmodified  timestamp default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP
)
    engine = MyISAM;

create index courtid
    on clubpro_main.tblReoccuringEvents (courtid);

