create table clubpro_main.tblkpGuestReservations
(
    reservationid int,
    name          text charset utf8                   not null,
    userid        int                                 null,
    lastmodified  timestamp default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP
)
    engine = MyISAM;

create index id
    on clubpro_main.tblkpGuestReservations (reservationid);

alter table clubpro_main.tblkpGuestReservations
    modify reservationid int auto_increment;

