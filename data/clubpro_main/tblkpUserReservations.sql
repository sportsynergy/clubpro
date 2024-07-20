create table tblkpUserReservations
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
    on tblkpUserReservations (outcome);

create index reservationid
    on tblkpUserReservations (reservationid);

create index userid
    on tblkpUserReservations (userid);

create index usertype
    on tblkpUserReservations (usertype);

