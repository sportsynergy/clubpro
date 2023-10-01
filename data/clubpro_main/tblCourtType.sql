create table clubpro_main.tblCourtType
(
    courttypeid     int auto_increment
        primary key,
    sportid         int(8)    default 0                 not null,
    courttypename   text                                not null,
    reservationtype int       default 0                 not null,
    enable          tinyint   default 1                 not null,
    lastmodified    timestamp default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP
)
    engine = MyISAM;

