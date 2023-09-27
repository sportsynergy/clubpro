create table tblCourtEventParticipants
(
    reservationid int                                 not null,
    userid        int                                 not null,
    lastModified  timestamp default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP,
    enddate       timestamp                           null
)
    engine = MyISAM;

create index reservationid
    on tblCourtEventParticipants (reservationid);

