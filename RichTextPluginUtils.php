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
        $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != 'off') ? 'https' : 'http';
        return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
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
        return $db->query('SELECT * FROM folder WHERE folder_id = '
            . $db->quote($folder_id)
        )->fetch(PDO::FETCH_COLUMN, 0);
    }

    public static function createFolder($range_id, $folder_id, $folder_name, $description=null) {
        $db = DBManager::get();
        $db->exec('INSERT IGNORE INTO folder '
            . 'SET folder_id = ' . $db->quote($folder_id)
            . ', range_id = ' . $db->quote($range_id)
            . ', user_id = ' . $db->quote($GLOBALS['user']->id)
            . ', name = ' . $db->quote($folder_name)
            . ', permission = ' . $db->quote(7)
            . ', mkdate = ' . $db->quote(time())
            . ', chdate = ' . $db->quote(time())
            . ', description = ' . $db->quote($description) 
        );
    }

    public static function getStudipDocumentData($seminar_id, $folder_id, $file) {
        $filename = studip_utf8decode($file['name']);
        $document['name'] = $document['filename'] = $filename;
        $document['user_id'] = $GLOBALS['user']->id;
        $document['author_name'] = get_fullname();
        $document['seminar_id'] = $seminar_id;
        $document['range_id'] = $folder_id;
        $document['filesize'] = $file['size'];
        return $document;
    }

    /**
     * Check if media proxy should be used and if so return the respective URL.
     *
     * @param string $url   URL to media file.
     * @return mixed        URL string to media file (possibly 'proxied')
     *                      or NULL if URL is invalid.
     */
    public static function getMediaUrl($url) {
        $studip_path = $GLOBALS['CANONICAL_RELATIVE_PATH_STUDIP'];
        $LOAD_EXTERNAL_MEDIA = Config::GetInstance()->getValue('LOAD_EXTERNAL_MEDIA');

        $pu = @parse_url($url);
        $url_is_http = $pu['scheme'] == 'http' || $pu['scheme'] == 'https';
        $url_is_on_host = $pu['host'] == $_SERVER['HTTP_HOST']
            || $pu['host'] . ':' . $pu['port'] == $_SERVER['HTTP_HOST'];
        $url_is_studip = strpos($pu['path'], $studip_path) === 0;

        // NOTE in original code $intern is undefined if not true
        $intern = $url_is_http && $url_is_on_host && $url_is_studip;

        if ($intern) {
            $pu_path = substr($pu['path'], strlen($studip_path));
            list($pu['first_target']) = explode('/', $pu_path);
            $internal_targets = array('sendfile.php', 'download', 'assets', 'pictures');
            if (in_array($pu['first_target'], $internal_targets)) {
                return idna_link(TransformInternalLinks($url));
            }
            $GLOBALS['msg'][] = 'Invalid internal link removed: ' . htmlentities($url);
            return NULL; // invalid internal link ==> remove <img src> attribute
        }
        if ($LOAD_EXTERNAL_MEDIA === "proxy" && Seminar_Session::is_current_session_authenticated()) {
            return $GLOBALS['ABSOLUTE_URI_STUDIP'] . 'dispatch.php/media_proxy?url=' . urlencode(idna_link($url));
        }
        if ($LOAD_EXTERNAL_MEDIA === 'allow') {
            return $url;
        }
        $GLOBALS['msg'][] = 'External media denied: ' . htmlentities($url);
        return NULL; // deny external media ==> remove <img src> attribute
    }
}

