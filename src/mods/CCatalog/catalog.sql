CREATE TABLE CATEGORY
(
C_NO int       unsigned NOT NULL auto_increment,
NAME char(255)          NOT NULL,

PRIMARY KEY(C_NO)
);

CREATE TABLE BRANDS
(
B_NO int       unsigned NOT NULL auto_increment,
NAME char(255)          NOT NULL,

PRIMARY KEY(B_NO)
);

CREATE TABLE GROUPS
(
G_NO   int       unsigned NOT NULL auto_increment,
NAME   char(255)          NOT NULL DEFAULT 'Товары',
HIDDEN int(1)    unsigned NOT NULL DEFAULT 1,
B_NO   int                NULL,
C_NO   int                NULL,

PRIMARY KEY(G_NO)
);

ALTER TABLE GROUPS
ADD FOREIGN KEY(B_NO) REFERENCES BRANDS;

ALTER TABLE GROUPS
ADD FOREIGN KEY(C_NO) REFERENCES CATEGORY;

CREATE TABLE PGROUPS
(
PG_NO  int       unsigned NOT NULL auto_increment,
NAME   char(255)          NOT NULL DEFAULT 'Другие',
HIDDEN int(1)    unsigned NOT NULL DEFAULT 1,
G_NO   int                NOT NULL,

PRIMARY KEY(PG_NO)
);

ALTER TABLE PGROUPS
ADD FOREIGN KEY(G_NO) REFERENCES GROUPS;

CREATE TABLE TYPES
(
T_NO     int       unsigned NOT NULL auto_increment,
NAME     char(255)          NOT NULL,
TYPE_XSL char(255)          NOT NULL,

PRIMARY KEY(T_NO)
);

CREATE TABLE COUNTSTATUS
(
CS_NO    int       unsigned NOT NULL auto_increment,
NAME     char(255)          NOT NULL,

PRIMARY KEY(CS_NO)
);

CREATE TABLE PRODUCTS
(
P_NO         int       unsigned NOT NULL auto_increment,
NAME         char(255)          NOT NULL DEFAULT '',
PRICE        int                NOT NULL DEFAULT 0,
ARTICUL      char(255)          NOT NULL DEFAULT '',
MAKER        char(255)          NOT NULL DEFAULT '',
COUNT        int                NOT NULL DEFAULT 0,
HIDDEN       int(1)             NOT NULL DEFAULT 0,
IS_HIT       int(1)             NOT NULL DEFAULT 0,
IS_NEW       int(1)             NOT NULL DEFAULT 0,
IS_REC       int(1)             NOT NULL DEFAULT 0,
IMG          char(255)          NULL,
PROPERTY_XML TEXT               NOT NULL,
T_NO         int                NOT NULL DEFAULT 1,
PG_NO        int                NULL,
CS_NO        int                NULL,

PRIMARY KEY(P_NO)
);

ALTER TABLE PRODUCTS
ADD FOREIGN KEY(PG_NO) REFERENCES PGROUPS;

ALTER TABLE PRODUCTS
ADD FOREIGN KEY(CS_NO) REFERENCES COUNTSTATUS;

ALTER TABLE PRODUCTS
ADD FOREIGN KEY(T_NO) REFERENCES TYPES;

-- data
INSERT INTO TYPES VALUE(1, 'По умолчанию', 'default.xsl');