create table clubpro_main.tblSiteActivity
(
    id           int auto_increment
        primary key,
    activitydate timestamp    default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP,
    siteid       mediumint                              null,
    description  varchar(255) default ''                not null,
    enddate      timestamp                              null
)
    engine = MyISAM;

create index siteid
    on clubpro_main.tblSiteActivity (siteid);

