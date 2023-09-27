create table tblReoccurringBlockEvent
(
    id           int auto_increment
        primary key,
    creator      int       default 0                 not null,
    lastmodified timestamp default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP
)
    engine = MyISAM;

