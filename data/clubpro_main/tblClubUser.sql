create table clubpro_main.tblClubUser
(
    id                          int auto_increment
        primary key,
    userid                      int             default 0                 not null,
    clubid                      int             default 0                 not null,
    msince                      varchar(35)                               null,
    roleid                      tinyint         default 0                 not null,
    recemail                    enum ('y', 'n') default 'y'               not null,
    enable                      enum ('y', 'n') default 'y'               not null,
    memberid                    varchar(255)                              null,
    lastlogin                   int                                       null,
    lastmodified                timestamp       default CURRENT_TIMESTAMP null on update CURRENT_TIMESTAMP,
    enddate                     timestamp                                 null,
    ccnew                       enum ('y', 'n') default 'n'               null,
    available_at_5              bit             default b'0'              not null,
    available_at_6              bit             default b'0'              not null,
    available_at_7              bit             default b'0'              not null,
    recleaguematchnotifications enum ('y', 'n') default 'n'               not null
)
    engine = MyISAM
    charset = utf8;

create index clubid
    on clubpro_main.tblClubUser (clubid);

