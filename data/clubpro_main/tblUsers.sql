create table tblUsers
(
    userid       int(8) auto_increment
        primary key,
    username     varchar(55)                          not null,
    firstname    varchar(35)                          not null,
    lastname     varchar(35)                          not null,
    email        varchar(155)                         not null,
    workphone    varchar(40)                          not null,
    homephone    varchar(40)                          not null,
    cellphone    varchar(40)                          not null,
    pager        varchar(40)                          not null,
    password     varchar(55)                          not null,
    useraddress  varchar(255)                         not null,
    gender       tinyint(1) default 1                 not null,
    lastmodified timestamp  default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP,
    enddate      timestamp                            null,
    photo        longblob                             null
)
    engine = MyISAM
    charset = utf8;

create index email
    on tblUsers (email);

create fulltext index fulltextsearch
    on tblUsers (username, firstname, lastname, email, workphone, homephone, cellphone, pager);

create index username
    on tblUsers (username);

