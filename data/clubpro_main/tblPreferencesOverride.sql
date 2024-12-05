create table clubpro_main.tblPreferencesOverride
(
    id                int auto_increment
        primary key,
    preference        varchar(45) not null comment 'the column name from the tblClubSites ',
    parameteroptionid varchar(45) not null comment 'The parameter value that applies this override',
    override          varchar(45) null comment 'This is what will be overridden'
);

