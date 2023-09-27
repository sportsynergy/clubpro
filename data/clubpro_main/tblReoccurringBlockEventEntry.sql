create table tblReoccurringBlockEventEntry
(
    id                     int auto_increment
        primary key,
    reoccuringblockeventid int default 0 not null,
    reoccuringentryid      int default 0 not null
)
    engine = MyISAM;

create index reoccuringblockeventid
    on tblReoccurringBlockEventEntry (reoccuringblockeventid, reoccuringentryid);

