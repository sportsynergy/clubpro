create table clubpro_main.tblParameterAccess
(
    parameteraccessid     int      default 0 not null
        primary key,
    parameteraccesstypeid int      default 0 not null,
    roleid                smallint default 0 not null,
    parameterid           smallint           null
)
    engine = MyISAM;

