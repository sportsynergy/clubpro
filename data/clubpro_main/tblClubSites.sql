create table clubpro_main.tblClubSites
(
    siteid                      mediumint auto_increment
        primary key,
    clubid                      mediumint                                                default 0                 not null,
    sitename                    text                                                                               not null,
    sitecode                    varchar(55)                                                                        not null,
    allowselfcancel             enum ('y', 'n', '2')                                     default 'y'               not null,
    enableautologin             enum ('y', 'n')                                          default 'n'               not null,
    rankingadjustment           int                                                      default 0                 not null,
    allowsoloreservations       enum ('y', 'n')                                          default 'y'               not null,
    allowselfscore              enum ('y', 'n')                                          default 'y'               not null,
    daysahead                   tinyint                                                  default 0                 not null,
    displaytime                 time                                                                               null,
    password                    varchar(255)                                                                       null,
    isliteversion               enum ('y', 'n')                                          default 'n'               not null,
    enable                      enum ('y', 'n')                                          default 'y'               not null,
    lastmodified                timestamp                                                default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP,
    allowallsiteadvertising     enum ('y', 'n')                                          default 'n'               not null,
    enableguestreservation      enum ('y', 'n')                                          default 'n'               not null,
    displaysitenavigation       enum ('y', 'n')                                          default 'y'               not null,
    displayrecentactivity       enum ('y', 'n')                                          default 'y'               not null,
    allownearrankingadvertising enum ('y', 'n')                                          default 'y'               not null,
    rankingscheme               enum ('point', 'ladder', 'jumpladder', 'jumpladderplus') default 'point'           not null,
    challengerange              smallint                                                 default 2                 not null,
    facebookurl                 varchar(255)                                                                       null,
    twitterurl                  varchar(255)                                                                       null,
    singleplayerdoublesmatch    enum ('y', 'n')                                                                    not null,
    reminders                   enum ('none', '24', '5', '6', '7', '8', '9', '10')       default 'none'            not null,
    displaycourttype            enum ('y', 'n')                                          default 'y'               not null,
    showplayernames             enum ('y', 'n')                                          default 'y'               not null comment 'Display the player names on the main reservation page',
    requirelogin                enum ('y', 'n')                                          default 'n'               not null comment 'require login before accessing main booking page',
    ccadmins                    enum ('y', 'n')                                          default 'n'               not null,
    allowplayerslooking         enum ('y', 'n')                                          default 'y'               null,
    timeoutlink                 varchar(255)                                                                       null comment 'the url after a timeout',
    constraint sitecode
        unique (sitecode)
)
    engine = MyISAM
    charset = utf8;

