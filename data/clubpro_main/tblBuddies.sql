create table tblBuddies
(
    bid          int auto_increment
        primary key,
    userid       int       default 0                 not null,
    buddyid      int       default 0                 not null,
    lastmodified timestamp default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP
)
    engine = MyISAM;

create index buddyid
    on tblBuddies (buddyid);

