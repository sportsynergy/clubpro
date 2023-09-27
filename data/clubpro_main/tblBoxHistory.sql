create table tblBoxHistory
(
    boxid         int       default 0                 not null,
    reservationid int       default 0                 not null,
    lastmodified  timestamp default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP
)
    engine = MyISAM;

create index boxid
    on tblBoxHistory (boxid);

