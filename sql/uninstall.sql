-- uninstall.sql - Creates tables for the RichText plugin.
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
-- NOTE user privileges are not automatically dropped
-- see section 13.7.1.3, “GRANT Syntax” of MySQL 5.5 Reference Manual
-- http://dev.mysql.com/doc/refman/5.5/en/grant.html
--
DROP TABLE IF EXISTS `plugin_rich_text`;

