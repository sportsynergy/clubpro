create table tblCourtGroupingEntry
(
    id           int auto_increment
        primary key,
    courtid      int       default 0                 not null,
    groupingid   int       default 0                 not null,
    lastmodified timestamp default CURRENT_TIMESTAMP not null
)
    engine = MyISAM;

