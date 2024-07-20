create table tblClubSiteLadders
(
    id             int auto_increment
        primary key,
    siteid         int                                 not null,
    courttypeid    int                                 not null,
    name           varchar(255)                        not null,
    enddate        timestamp default CURRENT_TIMESTAMP null,
    lastUpdated    timestamp                           null,
    leaguesUpdated timestamp                           null
)
    engine = MyISAM;

create index clubid
    on tblClubSiteLadders (siteid);

