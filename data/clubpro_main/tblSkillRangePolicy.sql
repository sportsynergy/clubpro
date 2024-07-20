create table tblSkillRangePolicy
(
    policyid     int auto_increment
        primary key,
    policyname   varchar(255) default ''                not null,
    description  varchar(255) default ''                not null,
    skillrange   float        default 0                 not null,
    dayid        smallint                               null,
    courtid      int                                    null,
    siteid       mediumint                              null,
    starttime    time                                   null,
    endtime      time                                   null,
    lastmodified timestamp    default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP
)
    engine = MyISAM;

