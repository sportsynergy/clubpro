create table clubpro_main.tblClubLadderTeam
(
    id       int auto_increment
        primary key,
    ladderid int           not null,
    enddate  timestamp     null,
    name     varchar(255)  not null,
    score    int default 0 not null
);

