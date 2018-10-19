CREATE TABLE GB_MESSAGES
(
M_NO      int       unsigned NOT NULL auto_increment,
MSG_TEXT TEXT                NULL,
U_NO      int       unsigned NOT NULL,
M_TIME    int       unsigned NOT NULL,

PRIMARY KEY(M_NO)
);

ALTER TABLE GB_MESSAGES
ADD FOREIGN KEY(U_NO) REFERENCES USERS;