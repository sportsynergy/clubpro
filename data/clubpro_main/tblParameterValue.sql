create table tblParameterValue
(
    userid         int         default 0                 not null,
    parameterid    smallint    default 0                 not null,
    parametervalue varchar(45) default ''                not null,
    lastmodified   timestamp   default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP,
    enddate        timestamp                             null,
    primary key (userid, parameterid)
)
    engine = MyISAM;

