create table clubpro_main.tblClubSiteLadders
(
    id             int auto_increment
        primary key,
    siteid         int          not null,
    courttypeid    int          not null,
    name           varchar(255) not null,
    enddate        timestamp    null,
    lastUpdated    timestamp    null,
    leaguesUpdated timestamp    null
)
    engine = MyISAM;

create index clubid
    on clubpro_main.tblClubSiteLadders (siteid);

