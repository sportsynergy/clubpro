create table tblFooterMessage
(
    id      int auto_increment
        primary key,
    text    varchar(255) default '' not null,
    enddate timestamp               null
)
    engine = MyISAM;

