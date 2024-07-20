create table tblClubEventParticipants
(
    id           int auto_increment
        primary key,
    clubeventid  int       default 0                 not null,
    userid       int       default 0                 null,
    guests       text                                not null,
    extra        text                                not null,
    comments     text                                not null,
    lastmodified timestamp default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP,
    enddate      timestamp                           null,
    division     varchar(20)                         null,
    partnerid    int                                 null
)
    comment 'A place to keep track of people that sign up for events' engine = MyISAM
                                                                      charset = utf8;

create index userid
    on tblClubEventParticipants (userid);

