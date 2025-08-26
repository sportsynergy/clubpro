create table clubpro_main.tblUserRankings
(
    userid       int       default 0                 not null,
    courttypeid  int       default 0                 not null,
    ranking      float     default 0                 not null,
    hot          tinyint   default 0                 not null,
    usertype     tinyint   default 0                 not null,
    lastmodified timestamp default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP
)
    engine = MyISAM
    charset = utf8;

create index courttypeid
    on clubpro_main.tblUserRankings (courttypeid);

create index hot
    on clubpro_main.tblUserRankings (hot);

create index userid
    on clubpro_main.tblUserRankings (userid);

create index usertype
    on clubpro_main.tblUserRankings (usertype);

