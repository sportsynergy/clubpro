create table clubpro_main.tblkpGuestReservations
(
    reservationid int                                 null,
    name          text charset utf8 charset utf8 not null,
    userid        int                                 null,
    lastmodified  timestamp default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP
)
    engine = MyISAM;

create index id
    on clubpro_main.tblkpGuestReservations (reservationid);

