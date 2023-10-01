create table clubpro_main.tblRoles
(
    roleid          tinyint auto_increment
        primary key,
    rolename        text                                not null,
    roleaccesslevel tinyint   default 0                 not null,
    lastmodified    timestamp default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP
)
    engine = MyISAM;

