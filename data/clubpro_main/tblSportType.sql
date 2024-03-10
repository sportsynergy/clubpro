create table clubpro_main.tblSportType
(
    sportid      int,
    sportname    text                                not null,
    lastmodified timestamp default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP
)
    engine = MyISAM;

create index sportid
    on clubpro_main.tblSportType (sportid);

alter table clubpro_main.tblSportType
    add primary key (sportid);

alter table clubpro_main.tblSportType
    modify sportid int auto_increment;

