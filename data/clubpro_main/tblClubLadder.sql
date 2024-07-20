create table tblClubLadder
(
    id              int auto_increment
        primary key,
    userid          int                           default 0                 not null,
    courttypeid     int                           default 0                 not null,
    ladderposition  int                           default 0                 not null,
    lastmodified    timestamp                     default CURRENT_TIMESTAMP not null,
    enddate         timestamp                                               null,
    going           enum ('steady', 'down', 'up') default 'steady'          not null,
    locked          enum ('y', 'n')               default 'n'               not null,
    ladderid        int                                                     null,
    lastmatchresult tinyint(1)                                              null,
    lastmatchuserid int                                                     null
)
    engine = MyISAM;

create index tblClubLadder_ladderid_index
    on tblClubLadder (ladderid);

create index tblClubLadder_userid_index
    on tblClubLadder (userid);

