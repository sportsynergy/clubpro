create table tblTeams
(
    teamid       int auto_increment
        primary key,
    courttypeid  int       default 0                 not null,
    enable       tinyint   default 1                 not null,
    lastmodified timestamp default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP
)
    engine = MyISAM;

