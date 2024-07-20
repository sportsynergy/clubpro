create table tblClubEvents
(
    id               int auto_increment
        primary key,
    name             varchar(45) charset utf8 default ''                not null,
    clubid           int                      default 0                 not null,
    eventdate        date                     default '0000-00-00'      not null,
    eventenddate     date                     default '0000-00-00'      null,
    description      text charset utf8                                  not null,
    enddate          timestamp                                          null,
    lastmodified     timestamp                default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP,
    creator          int                      default 0                 not null,
    lastmodifier     int                      default 0                 not null,
    registerdivision enum ('y', 'n')          default 'n'               not null,
    registerteam     enum ('y', 'n')          default 'n'               not null
)
    engine = MyISAM;

create index clubid
    on tblClubEvents (clubid);

