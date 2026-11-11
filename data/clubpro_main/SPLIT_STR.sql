CREATE DEFINER = root@localhost FUNCTION clubpro_main.SPLIT_STR(
    x VARCHAR(255),
    delim VARCHAR(12),
    pos INT
) RETURNS VARCHAR(255)
DETERMINISTIC
NO SQL
RETURN REPLACE(
    SUBSTRING(
        SUBSTRING_INDEX(x, delim, pos),
        LENGTH(SUBSTRING_INDEX(x, delim, pos - 1)) + 1
    ),
    delim, ''
);
