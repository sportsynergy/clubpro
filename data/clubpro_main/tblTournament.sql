create table clubpro_main.tblTournament
(
    id        int auto_increment
        primary key,
    ladder_id int not null,
    round     int null,
    player1   int null,
    player2   int null,
    score     int null,
    winnerid  int null
);

