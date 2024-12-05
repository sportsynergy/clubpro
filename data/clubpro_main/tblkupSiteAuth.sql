create table clubpro_main.tblkupSiteAuth
(
    userid       mediumint default 0                 not null,
    siteid       mediumint default 0                 not null,
    lastmodified timestamp default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP
)
    engine = MyISAM;

create index userid
    on clubpro_main.tblkupSiteAuth (userid);

