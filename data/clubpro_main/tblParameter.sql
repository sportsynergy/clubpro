create table tblParameter
(
    parameterid     smallint    default 0  not null
        primary key,
    parametertypeid smallint    default 0  not null,
    siteid          smallint    default 0  not null,
    parameterlabel  varchar(45) default '' not null
)
    engine = MyISAM;

