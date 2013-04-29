<?php
/**
 * RichTextPluginUtils.php - Utility functions needed by the RichText plugin.
 *
 * Even though these functions are included with the ExternalLink plugin they 
 * are not specific to it and might be useful in other projects as well.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Robert Costa <rcosta@uos.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 */

/**
 * Encapsulates various utility functions under the Utils class namespace.
 */
class RichTextPluginUtils {

    /**
     * Get the current URL as called by the web client.
     * taken from http://stackoverflow.com/a/2820771
     *
     * @return string  The current URL.
     */
    public static function getUrl() {
        $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
        return $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    /**
     * Get the file name of the currently executed PHP script.
     *
     * @return string  Filename of currently executed PHP script.
     */
    public static function getBasename() {
        return basename($_SERVER['PHP_SELF']);
    }

    /**
     * Get the base URL including the directory path, excluding file name, 
     * query string, etc.
     *
     * return string  Base URL of client request.
     */
    public static function getBaseUrl() {
        $url = Utils::getUrl();
        $pos = strpos($url, Utils::getBasename());
        // remove current script name, query, etc.
        // only keep host URL and directory part of path
        return substr($url, 0, $pos);
    }

    /**
     * Return id of currently selected seminar.
     * Return false, if no seminar is selected.
     *
     * @return mixed  seminar_id or false
     */
    public static function getSeminarId() {
        if (!Request::option('cid')) {
            if ($GLOBALS['SessionSeminar']) {
                URLHelper::bindLinkParam('cid', $GLOBALS['SessionSeminar']);
                return $GLOBALS['SessionSeminar'];
            }
            return false;
        }
        return Request::option('cid');
    }

    public static function getFolder($folder_id) {
        $db = DBManager::get();
        return $db->query(
            "SELECT * " .
            "FROM folder " .
            "WHERE folder_id = " . $db->quote($folder_id) . " " .
            "")->fetch(PDO::FETCH_COLUMN, 0);
    }

    public static function createFolder($range_id, $folder_id, $folder_name) {
        $db = DBManager::get();
        $db->exec(
            "INSERT IGNORE INTO folder " .
                "SET folder_id = " . $db->quote($folder_id) . ", " .
                "range_id = " . $db->quote($range_id) . ", "  .
                "user_id = " . $db->quote($GLOBALS['user']->id) . ", " .
                "name = " . $db->quote($folder_name) . ", " .
                "permission = '7', " .
                "mkdate = " . $db->quote(time()) . ", " .
                "chdate = " . $db->quote(time()) . " " .
                "");
    }
}

