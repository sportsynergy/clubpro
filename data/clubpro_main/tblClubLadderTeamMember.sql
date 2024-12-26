create table clubpro_main.tblClubLadderTeamMember
(
    id      int       not null
        primary key,
    teamid  int       not null,
    userid  int       not null,
    enddate timestamp null
);

