create table clubpro_main.tblBoxLeagueSchedule
(
    id           int auto_increment
        primary key,
    boxid        int                                 not null,
    userid1      int                                 not null,
    userid2      int                                 not null,
    scored       bit       default b'0'              not null,
    lastmodified timestamp default CURRENT_TIMESTAMP null
);

