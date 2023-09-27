create table clubpro_main.tblChallengeMatch
(
    id           int auto_increment
        primary key,
    challengerid int                                 not null,
    challengeeid int                                 not null,
    courttypeid  int                                 not null,
    date         timestamp default CURRENT_TIMESTAMP not null,
    enddate      timestamp                           null,
    score        float                               null comment 'the loser score, null is not scored',
    siteid       mediumint                           not null,
    ladderid     int       default 0                 null
)
    engine = MyISAM;

create index siteid
    on clubpro_main.tblChallengeMatch (siteid);

