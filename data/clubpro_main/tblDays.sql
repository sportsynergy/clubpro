create table clubpro_main.tblDays
(
    dayid tinyint      default 0  not null
        primary key,
    name  varchar(255) default '' not null
)
    engine = MyISAM;

