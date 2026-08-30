create
    definer = root@localhost function clubpro_main.SPLIT_STR(x varchar(255), delim varchar(12), pos int) returns varchar(255)
    RETURN REPLACE(SUBSTRING(SUBSTRING_INDEX(x, delim, pos),
       LENGTH(SUBSTRING_INDEX(x, delim, pos -1)) + 1),
       delim, '');

