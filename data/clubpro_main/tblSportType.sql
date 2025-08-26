create table clubpro_main.tblSportType
(
    sportid      int auto_increment
        primary key,
    sportname    text                                not null,
    lastmodified timestamp default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP
)
    engine = MyISAM;

create index sportid
    on clubpro_main.tblSportType (sportid);

