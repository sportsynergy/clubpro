create table clubpro_main.tblMatchScore
(
    id          int auto_increment
        primary key,
    courttypeid int not null,
    gameswon    int not null,
    gameslost   int not null
)
    engine = MyISAM;

