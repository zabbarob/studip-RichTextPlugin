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
     * Test if string starts with prefix.
     *
     * @param string $string The string that must start with the prefix.
     * @param string $prefix The prefix of the string.
     *
     * @return boolean  True if string starts with prefix.
     *                  False if string does not start with prefix.
     */
    public static function startsWith($string, $prefix) {
        return substr($string, 0, strlen($prefix)) === $prefix;
    }

    /**
     * Remove prefix from string.
     *
     * Does not change the string if it has a different prefix.
     *
     * @param string $string The string that must start with the prefix.
     * @param string $prefix The prefix of the string.
     *
     * @return string String without prefix.
     */
    public static function removePrefix($string, $prefix) {
        if (RichTextPluginUtils::startsWith($string, $prefix)) {
            return substr($string, strlen($prefix));
        }
        return $string;
    }

    /**
     * Check if media proxy should be used and if so return the respective URL.
     *
     * @param string $url   URL to media file.
     * @return mixed        URL string to media file (possibly 'proxied')
     *                      or NULL if URL is invalid.
     */
    public static function getMediaUrl($url) {
        // some values we need later
        $studip_path = $GLOBALS['CANONICAL_RELATIVE_PATH_STUDIP'];
        $LOAD_EXTERNAL_MEDIA = Config::GetInstance()->getValue('LOAD_EXTERNAL_MEDIA');
        $base_url = $GLOBALS['ABSOLUTE_URI_STUDIP'];
        $media_proxy = $base_url . 'dispatch.php/media_proxy?url=';

        // clean up URLs that already access the media proxy
        if (RichTextPluginUtils::startsWith($url, $media_proxy)) {
            $url = urldecode(RichTextPluginUtils::removePrefix($url, $media_proxy));
        }

        $pu = @parse_url($url);
        $url_is_http = $pu['scheme'] == 'http' || $pu['scheme'] == 'https';
        $url_is_on_host = $pu['host'] == $_SERVER['HTTP_HOST']
            || $pu['host'] . ':' . $pu['port'] == $_SERVER['HTTP_HOST'];
        $url_is_studip = strpos($pu['path'], $studip_path) === 0;

        $url_is_intern = $url_is_http && $url_is_on_host && $url_is_studip;

        if ($url_is_intern) {
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
            return $media_proxy . urlencode(idna_link($url));
        }
        if ($LOAD_EXTERNAL_MEDIA === 'allow') {
            return $url;
        }
        $GLOBALS['msg'][] = 'External media denied: ' . htmlentities($url);
        return NULL; // deny external media ==> remove <img src> attribute
    }

    /**
     * Verify that user has requested permission, throw exception if not.
     * @param string $permission Minimum requested permission level.
     */
    public function verifyPermission($permission) {
        $context = RichTextPluginUtils::getSeminarId();
        if (!$GLOBALS['perm']->have_studip_perm($permission, $context)) {
            throw new AccessDeniedException(sprintf(studip_utf8decode(_('Es werden mindestens "%s"-Zugriffsrechte benötigt.')), $permission));
        }
    }

    /**
     * Verify that HTTP request was send as HTTP POST, throw exception if not.
     */
    public function verifyPostRequest() {
        if (!Request::isPost()) {
            throw new AccessDeniedException(_('Die Anfrage muss als HTTP POST gestellt werden.'));
        }
    }
}

