-- Tabelle für Backend-Menübereiche
CREATE TABLE IF NOT EXISTS `tl_backendmenue_bereiche` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `tstamp` int(10) unsigned NOT NULL default '0',
  `sorting` int(10) unsigned NOT NULL default '0',
  `name` varchar(255) NOT NULL default '',
  `icon` varchar(255) NOT NULL default '',
  `position` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `sorting` (`sorting`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabelle für Modul-Zuordnungen zu Bereichen
CREATE TABLE IF NOT EXISTS `tl_backendmenue_zuordnungen` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `sorting` int(10) unsigned NOT NULL default '0',
  `module` varchar(255) NOT NULL default '',
  `position` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`),
  KEY `sorting` (`sorting`),
  CONSTRAINT `tl_backendmenue_zuordnungen_ibfk_1` FOREIGN KEY (`pid`) REFERENCES `tl_backendmenue_bereiche` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
