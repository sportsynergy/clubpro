create table clubpro_main.tblReservations
(
    reservationid int auto_increment
        primary key,
    courtid       int             default 0                     not null,
    time          int             default 0                     not null,
    usertype      tinyint         default 0                     not null,
    matchtype     tinyint         default 0                     not null,
    eventid       smallint(4)     default 0                     not null,
    guesttype     tinyint         default 0                     not null,
    creator       int             default 0                     not null,
    createdate    timestamp       default '0000-00-00 00:00:00' not null,
    lastmodifier  int             default 0                     not null,
    enddate       timestamp                                     null,
    lastmodified  timestamp       default CURRENT_TIMESTAMP     not null on update CURRENT_TIMESTAMP,
    locked        enum ('y', 'n') default 'n'                   not null,
    duration      int                                           null
)
    engine = MyISAM;

create index courtid
    on clubpro_main.tblReservations (courtid);

create index enddate
    on clubpro_main.tblReservations (enddate);

create index matchtype
    on clubpro_main.tblReservations (matchtype);

create index time
    on clubpro_main.tblReservations (time);

create index usertype
    on clubpro_main.tblReservations (usertype);

