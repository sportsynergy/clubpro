create table clubpro_main.tblClubs
(
    clubid       int       default 0                 not null
        primary key,
    clubname     text charset utf8                   not null,
    clubaddress  text charset utf8                   not null,
    contactid    int       default 0                 not null,
    clubphone    text charset utf8                   not null,
    timezone     tinyint   default 0                 not null,
    rankdev      float     default 0                 not null,
    enable       int       default 1                 not null,
    lastmodified timestamp default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP
)
    engine = MyISAM;

