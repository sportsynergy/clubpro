create table clubpro_main.tblClubLadderTeamMember
(
    id      int auto_increment
        primary key,
    teamid  int       not null,
    userid  int       not null,
    enddate timestamp null
);

