create table clubpro_main.tblkpBoxLeagues
(
    boxid        int       default 0                 not null,
    userid       int       default 0                 not null,
    boxplace     int       default 0                 not null,
    games        tinyint   default 0                 not null,
    score        smallint  default 0                 not null,
    lastmodified timestamp default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP
)
    engine = MyISAM;

