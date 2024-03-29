﻿DROP TABLE IF EXISTS `#__phocaguestbook_items`;
CREATE TABLE `#__phocaguestbook_items` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `catid` int(11) NOT NULL default '0',
  `sid` int(11) NOT NULL default '0',
  `username` varchar(100) NOT NULL default '',
  `userid` int(11) NOT NULL default '0',
  `email` varchar(50) NOT NULL default '',
  `homesite` varchar(50) NOT NULL default '',
  `ip` varchar(20) NOT NULL default '',
  `title` varchar(200) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `content` text NOT NULL default '',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `published` tinyint(1) NOT NULL default '0',
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  `params` text NOT NULL,
  `language` char(7) NOT NULL Default '',
  PRIMARY KEY  (`id`),
  KEY `published` (`published`)
) ENGINE=MyISAM CHARACTER SET `utf8`;

DROP TABLE IF EXISTS `#__phocaguestbook_books`;
CREATE TABLE `#__phocaguestbook_books` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) NOT NULL default 0,
  `title` varchar(255) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `alias` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  `section` varchar(50) NOT NULL default '',
  `image_position` varchar(30) NOT NULL default '',
  `description` text NOT NULL,
  `published` tinyint(1) NOT NULL default '0',
  `checked_out` int(11) unsigned NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `editor` varchar(50) default NULL,
  `ordering` int(11) NOT NULL default '0',
  `access` tinyint(3) unsigned NOT NULL default '0',
  `report` tinyint(3) unsigned NOT NULL default '0',
  `count` int(11) NOT NULL default '0',
  `params` text NOT NULL,
  `language` char(7) NOT NULL Default '',
  PRIMARY KEY  (`id`),
  KEY `cat_idx` (`section`,`published`,`access`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`)
) ENGINE=MyISAM CHARACTER SET `utf8`;

-- 2.0.0 UPDATE ONLY
-- ALTER TABLE `#__phocaguestbook_books` ADD `language` char(7) NOT NULL Default '' AFTER `params` ;  
-- ALTER TABLE `#__phocaguestbook_items` ADD `language` char(7) NOT NULL Default '' AFTER `params` ;
-- ALTER TABLE `#__phocaguestbook_items` ADD `alias` varchar(255) NOT NULL default '' AFTER `title` ; 

-- 2.0.0 RC UPDATE ONLY
-- ALTER TABLE `#__phocaguestbook_books` ADD `report` tinyint(3) unsigned NOT NULL default '0' AFTER `params` ; 