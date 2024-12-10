create table clubpro_main.tblCourts
(
    courtid                int(8) auto_increment
        primary key,
    courttypeid            int(8)             default 0                 not null,
    clubid                 int(8)             default 0                 not null,
    courtname              varchar(155) charset utf8                    not null,
    enable                 tinyint            default 1                 not null,
    siteid                 mediumint unsigned default 0                 not null,
    displayorder           smallint           default 0                 not null,
    lastmodified           timestamp          default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP,
    variableduration       enum ('n', 'y')    default 'n'               not null,
    variableduration_admin enum ('y', 'n')    default 'n'               not null comment 'variable duration for admins'
)
    engine = MyISAM;

