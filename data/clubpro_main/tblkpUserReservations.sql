create table clubpro_main.tblkpUserReservations
(
    id            int auto_increment
        primary key,
    reservationid int       default 0                 not null,
    userid        int       default 0                 not null,
    outcome       tinyint   default 0                 not null,
    usertype      tinyint   default 0                 not null,
    lastmodified  timestamp default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP
)
    engine = MyISAM
    charset = utf8;

create index outcome
    on clubpro_main.tblkpUserReservations (outcome);

create index reservationid
    on clubpro_main.tblkpUserReservations (reservationid);

create index userid
    on clubpro_main.tblkpUserReservations (userid);

create index usertype
    on clubpro_main.tblkpUserReservations (usertype);

