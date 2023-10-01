create table clubpro_main.tblCourtHours
(
    id           int auto_increment
        primary key,
    dayid        tinyint   default 0                 not null,
    courtid      int       default 0                 not null,
    opentime     time      default '00:00:00'        not null,
    closetime    time      default '00:00:00'        not null,
    hourstart    tinyint   default 0                 not null,
    duration     double    default 0                 not null,
    lastmodified timestamp default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP
)
    engine = MyISAM;

