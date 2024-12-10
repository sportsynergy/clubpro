create table clubpro_main.tblParameterType
(
    parametertypeid   smallint    default 0  not null
        primary key,
    parametertypename varchar(45) default '' not null
)
    engine = MyISAM;

