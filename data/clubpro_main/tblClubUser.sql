create table tblClubUser
(
    id           int auto_increment
        primary key,
    userid       int             default 0                 not null,
    clubid       int             default 0                 not null,
    msince       varchar(35)                               null,
    roleid       tinyint         default 0                 not null,
    recemail     enum ('y', 'n') default 'y'               not null,
    enable       enum ('y', 'n') default 'y'               not null,
    memberid     varchar(255)                              null,
    lastlogin    int                                       null,
    lastmodified timestamp       default CURRENT_TIMESTAMP null on update CURRENT_TIMESTAMP,
    enddate      timestamp                                 null,
    ccnew        enum ('y', 'n') default 'n'               null
)
    engine = MyISAM
    charset = utf8;

create index clubid
    on tblClubUser (clubid);

