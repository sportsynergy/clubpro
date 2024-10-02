create table tblBoxLeagues
(
    boxid        int auto_increment
        primary key,
    boxname      text                                not null,
    siteid       int       default 0                 not null,
    boxrank      smallint  default 0                 not null,
    courttypeid  int       default 0                 not null,
    enddate      date                                not null,
    enddatestamp int       default 0                 not null,
    enable       int       default 1                 not null,
    lastmodified timestamp default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP,
    ladderid     int                                 null,
    autoschedule bit       default b'0'              null,
    startdate    date                                null
)
    engine = MyISAM;

