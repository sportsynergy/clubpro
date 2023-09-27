create table clubpro_main.tblParameterOptions
(
    parameteroptionid smallint auto_increment
        primary key,
    parameterid       smallint    default 0  not null,
    optionname        varchar(45) default '' not null,
    optionvalue       varchar(45) default '' not null
)
    engine = MyISAM;

