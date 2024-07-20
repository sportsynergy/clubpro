create table tblSchedulingPolicy
(
    policyid       int auto_increment
        primary key,
    policyname     varchar(255)    default ''                not null,
    description    varchar(255)    default ''                not null,
    schedulelimit  int             default 0                 not null,
    dayid          smallint                                  null,
    courtid        int                                       null,
    siteid         mediumint                                 null,
    starttime      time                                      null,
    endtime        time                                      null,
    allowlooking   enum ('y', 'n') default 'y'               not null,
    allowback2back enum ('n', 'y') default 'y'               not null,
    lastmodified   timestamp       default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP
)
    engine = MyISAM;

create index siteid
    on tblSchedulingPolicy (siteid);

