create table clubpro_main.tblReservationType
(
    reservationtypeid   tinyint   default 0                 not null
        primary key,
    reservationtypename text                                not null,
    lastmodified        timestamp default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP
)
    engine = MyISAM;

