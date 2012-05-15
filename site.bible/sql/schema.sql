SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- 資料庫: `flyfish`
-- 

-- --------------------------------------------------------

-- 
-- 資料表格式： `albums`
-- 

CREATE TABLE `albums` (
  `id` varchar(20) collate utf8_unicode_ci NOT NULL,
  `sort` int(11) NOT NULL default '0',
  `urn` varchar(128) collate utf8_unicode_ci default NULL,
  `is_active` enum('0','1') collate utf8_unicode_ci NOT NULL default '1',
  `has_cover` enum('0','1') collate utf8_unicode_ci NOT NULL default '0',
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `creator` varchar(64) collate utf8_unicode_ci NOT NULL,
  `creator_id` varchar(20) collate utf8_unicode_ci NOT NULL,
  `info` text collate utf8_unicode_ci,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `deleted` enum('0','1') collate utf8_unicode_ci NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `urn` (`urn`),
  KEY `sort` (`sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- 資料表格式： `bible`
-- 

CREATE TABLE `bible` (
  `id` char(11) character set utf8 NOT NULL COMMENT '經文索引',
  `sort` smallint(6) NOT NULL,
  `stype_id` char(1) character set utf8 NOT NULL default 'g' COMMENT '經文分類',
  `book_id` int(11) NOT NULL COMMENT '卷',
  `chapter_id` int(11) NOT NULL COMMENT '章',
  `verse_id` int(11) NOT NULL COMMENT '節',
  `name` text character set utf8 NOT NULL COMMENT '內文',
  `relate` text character set utf8 NOT NULL COMMENT '相關經文',
  PRIMARY KEY  (`id`),
  KEY `book_id` (`book_id`,`chapter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='聖經內文表';

-- --------------------------------------------------------

-- 
-- 資料表格式： `bible_books`
-- 

CREATE TABLE `bible_books` (
  `id` smallint(5) unsigned NOT NULL,
  `plugin` varchar(32) collate utf8_unicode_ci NOT NULL,
  `testament` varchar(2) collate utf8_unicode_ci NOT NULL,
  `category_id` smallint(6) NOT NULL,
  `category_name` varchar(32) collate utf8_unicode_ci NOT NULL,
  `short` char(2) collate utf8_unicode_ci NOT NULL,
  `short_kr` char(2) collate utf8_unicode_ci NOT NULL,
  `short_en` char(3) collate utf8_unicode_ci NOT NULL,
  `name` varchar(20) collate utf8_unicode_ci NOT NULL,
  `name_kr` varchar(20) collate utf8_unicode_ci NOT NULL,
  `name_en` varchar(32) collate utf8_unicode_ci NOT NULL,
  `full_en` varchar(64) collate utf8_unicode_ci NOT NULL,
  `max_chapter` smallint(5) unsigned NOT NULL default '0',
  `info` text collate utf8_unicode_ci NOT NULL,
  `summary` text collate utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- 資料表格式： `bible_categories`
-- 

CREATE TABLE `bible_categories` (
  `id` smallint(6) unsigned NOT NULL,
  `name` varchar(10) collate utf8_unicode_ci NOT NULL,
  `logo` varchar(32) collate utf8_unicode_ci NOT NULL,
  `info` varchar(255) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- 資料表格式： `bible_chapters`
-- 

CREATE TABLE `bible_chapters` (
  `id` char(6) collate utf8_unicode_ci NOT NULL,
  `book_id` int(11) NOT NULL,
  `chapter_id` int(11) NOT NULL,
  `name` varchar(100) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `book_id` (`book_id`,`chapter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- 資料表格式： `bible_stypes`
-- 

CREATE TABLE `bible_stypes` (
  `id` char(1) collate utf8_unicode_ci NOT NULL,
  `name` varchar(14) collate utf8_unicode_ci NOT NULL,
  `info` varchar(64) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- 資料表格式： `cuv`
-- 

CREATE TABLE `cuv` (
  `id` char(11) character set utf8 NOT NULL COMMENT '經文索引',
  `sort` smallint(6) NOT NULL,
  `stype_id` char(1) character set utf8 NOT NULL default 'g' COMMENT '經文分類',
  `book_id` int(11) NOT NULL COMMENT '卷',
  `chapter_id` int(11) NOT NULL COMMENT '章',
  `verse_id` int(11) NOT NULL COMMENT '節',
  `name` text character set utf8 NOT NULL COMMENT '內文',
  `relate` text character set utf8 NOT NULL COMMENT '相關經文',
  PRIMARY KEY  (`id`),
  KEY `book_id` (`book_id`,`chapter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='和合本聖經內文表';

-- --------------------------------------------------------

-- 
-- 資料表格式： `cuv_chapters`
-- 

CREATE TABLE `cuv_chapters` (
  `id` char(6) collate utf8_unicode_ci NOT NULL,
  `book_id` int(11) NOT NULL,
  `chapter_id` int(11) NOT NULL,
  `name` varchar(100) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `book_id` (`book_id`,`chapter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- 資料表格式： `groups`
-- 

CREATE TABLE `groups` (
  `id` varchar(20) collate utf8_unicode_ci NOT NULL,
  `name` varchar(32) collate utf8_unicode_ci NOT NULL,
  `info` varchar(512) collate utf8_unicode_ci NOT NULL,
  `sort` tinyint(4) unsigned NOT NULL,
  `is_active` tinyint(4) NOT NULL default '0',
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `deleted` enum('0','1') collate utf8_unicode_ci NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- 資料表格式： `groups_managers`
-- 

CREATE TABLE `groups_managers` (
  `group_id` varchar(20) collate utf8_unicode_ci NOT NULL,
  `manager_id` varchar(20) collate utf8_unicode_ci NOT NULL,
  `sort` tinyint(4) NOT NULL,
  PRIMARY KEY  (`group_id`,`manager_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- 資料表格式： `managers`
-- 

CREATE TABLE `managers` (
  `id` varchar(20) collate utf8_unicode_ci NOT NULL,
  `userid` varchar(32) collate utf8_unicode_ci NOT NULL,
  `username` varchar(255) collate utf8_unicode_ci NOT NULL,
  `algorithm` varchar(128) collate utf8_unicode_ci NOT NULL,
  `salt` varchar(128) collate utf8_unicode_ci NOT NULL,
  `password` varchar(128) collate utf8_unicode_ci NOT NULL,
  `is_active` tinyint(4) NOT NULL default '0',
  `is_super_user` tinyint(4) NOT NULL default '0',
  `last_login` datetime NOT NULL,
  `last_login_ip` char(15) collate utf8_unicode_ci default NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `deleted` enum('0','1') collate utf8_unicode_ci NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- 資料表格式： `news`
-- 

CREATE TABLE `news` (
  `id` varchar(20) collate utf8_unicode_ci NOT NULL,
  `urn` varchar(128) collate utf8_unicode_ci default NULL,
  `plugin` varchar(20) collate utf8_unicode_ci NOT NULL,
  `is_active` enum('0','1') collate utf8_unicode_ci NOT NULL default '1',
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `author` varchar(64) collate utf8_unicode_ci NOT NULL,
  `author_id` varchar(20) collate utf8_unicode_ci NOT NULL,
  `article` text collate utf8_unicode_ci,
  `published` datetime NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `deleted` enum('0','1') collate utf8_unicode_ci NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `published` (`published`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- 資料表格式： `praises`
-- 

CREATE TABLE `praises` (
  `id` varchar(20) collate utf8_unicode_ci NOT NULL,
  `plugin` varchar(32) collate utf8_unicode_ci NOT NULL,
  `category_id` varchar(20) collate utf8_unicode_ci NOT NULL,
  `category_code` char(4) collate utf8_unicode_ci NOT NULL,
  `sn` char(5) collate utf8_unicode_ci NOT NULL,
  `name` varchar(64) collate utf8_unicode_ci NOT NULL,
  `name_kr` varchar(64) collate utf8_unicode_ci NOT NULL,
  `is_active` enum('0','1') collate utf8_unicode_ci NOT NULL default '1',
  `key` char(4) collate utf8_unicode_ci NOT NULL,
  `info` varchar(128) collate utf8_unicode_ci NOT NULL,
  `lyrics` text collate utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `deleted` enum('0','1') collate utf8_unicode_ci NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- 資料表格式： `praises_categories`
-- 

CREATE TABLE `praises_categories` (
  `id` varchar(20) collate utf8_unicode_ci NOT NULL,
  `plugin` varchar(32) collate utf8_unicode_ci NOT NULL,
  `code` char(4) collate utf8_unicode_ci NOT NULL,
  `name` varchar(128) collate utf8_unicode_ci NOT NULL,
  `is_active` enum('0','1') collate utf8_unicode_ci NOT NULL default '1',
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `deleted` enum('0','1') collate utf8_unicode_ci NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- 資料表格式： `praises_files`
-- 

CREATE TABLE `praises_files` (
  `id` varchar(20) collate utf8_unicode_ci NOT NULL,
  `plugin` varchar(32) collate utf8_unicode_ci NOT NULL,
  `type` varchar(16) collate utf8_unicode_ci NOT NULL,
  `sort` smallint(6) NOT NULL default '0',
  `parent_id` varchar(20) collate utf8_unicode_ci NOT NULL,
  `name` varchar(64) collate utf8_unicode_ci NOT NULL COMMENT '檔名(上傳時的原始名稱)',
  `ext` char(5) collate utf8_unicode_ci NOT NULL COMMENT '副檔名、檔案格式',
  `path` varchar(64) collate utf8_unicode_ci NOT NULL COMMENT '存檔的真實路徑及名稱',
  `size` varchar(16) collate utf8_unicode_ci NOT NULL COMMENT '檔案大小(Human Readable)',
  `volume` int(11) NOT NULL COMMENT '檔案大小(Numerized)',
  `info` varchar(128) collate utf8_unicode_ci default NULL,
  `is_active` enum('0','1') collate utf8_unicode_ci NOT NULL default '1',
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `deleted` enum('0','1') collate utf8_unicode_ci NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- 資料表格式： `privileges`
-- 

CREATE TABLE `privileges` (
  `request` varchar(64) collate utf8_unicode_ci NOT NULL,
  `content` varchar(128) collate utf8_unicode_ci NOT NULL,
  `access` enum('allow','deny','neutral') collate utf8_unicode_ci NOT NULL default 'neutral',
  PRIMARY KEY  (`request`,`content`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- 資料表格式： `syslog`
-- 

CREATE TABLE `syslog` (
  `id` varchar(20) collate utf8_unicode_ci NOT NULL,
  `plugin` varchar(20) collate utf8_unicode_ci NOT NULL,
  `type` varchar(20) collate utf8_unicode_ci NOT NULL,
  `prior` varchar(10) collate utf8_unicode_ci NOT NULL,
  `userid` varchar(32) collate utf8_unicode_ci NOT NULL,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `ip` char(15) collate utf8_unicode_ci NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `name` (`name`),
  KEY `userid` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
