-- install.sql - Creates tables for the RichText plugin.
-- 
-- This program is free software, you can redistribute it and/or
-- modify it under the terms of the GNU General Public License as
-- published by the Free Software Foundation, either version 2 of
-- the License, or (at your option) any later version.
-- 
-- @author      Robert Costa <zabbarob@gmail.com>
-- @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
-- @category    Stud.IP
--
CREATE TABLE IF NOT EXISTS `plugin_rich_text` (
  `range_id` varchar(32) COLLATE latin1_german1_ci NOT NULL DEFAULT '',
  `body` text COLLATE latin1_german1_ci,
  PRIMARY KEY (`range_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

