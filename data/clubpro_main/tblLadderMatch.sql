create table clubpro_main.tblLadderMatch
(
    id            int auto_increment
        primary key,
    ladderid      int                                  not null,
    score         varchar(8)                           null,
    winnerid      int                                  not null,
    loserid       int                                  not null,
    reported_time timestamp  default CURRENT_TIMESTAMP not null,
    match_time    datetime                             not null,
    enddate       timestamp                            null,
    processed     tinyint(1) default 0                 not null
);

