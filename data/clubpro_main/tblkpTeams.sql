create table clubpro_main.tblkpTeams
(
    userid       int       default 0                 not null,
    teamid       int       default 0                 not null,
    enable       tinyint   default 1                 not null,
    lastmodified timestamp default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP
)
    engine = MyISAM;

create index teamid
    on clubpro_main.tblkpTeams (teamid);

