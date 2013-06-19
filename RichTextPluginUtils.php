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

    /**
     * Get ID of a Stud.IP folder, create the folder if it doesn't exist.
     * @param string $name        Name of the folder.
     * @param string $description Description of the folder (optional and only 
     *                            used if folder doesn't exist).
     * @return string The ID of the Stud.IP folder.
     */
    static public function getFolderId($name, $description=null) {
        $seminar_id = RichTextPluginUtils::getSeminarId();
        $folder_id = md5($name . '_' . $seminar_id);
        $db = DBManager::get();
        $db->exec('INSERT IGNORE INTO folder SET '
            . 'folder_id = ' . $db->quote($folder_id)
            . ', range_id = ' . $db->quote($seminar_id)
            . ', user_id = ' . $db->quote($GLOBALS['user']->id)
            . ', name = ' . $db->quote($folder_name)
            . ', permission = ' . $db->quote(7)
            . ', mkdate = ' . $db->quote(time())
            . ', chdate = ' . $db->quote(time())
            . ', description = ' . $db->quote($description) 
        );
        return $folder_id;
    }

    /**
     * Create a new Stud.IP document from an uploaded file.
     *
     * @param array  $file      Metadata of uploaded file.
     * @param string $folder_id ID of Stud.IP folder to which file is uploaded.
     *
     * @return StudipDocument   The created Stud.IP document.
     * @throws AccessDeniedException if file is forbidden or upload failed.
     */
    static public function uploadFile($file, $folder_id) {
        RichTextPluginUtils::verifyUpload($file); // throw exception if file forbidden

        $newfile = StudipDocument::createWithFile(
            $file['tmp_name'],
            RichTextPluginUtils::getStudipDocumentData($folder_id, $file));

        if (!$newfile) { // file creation failed
            throw new AccessDeniedException(
                _('Stud.IP-Dokument konnte nicht erstellt werden.'));
        }

        return $newfile;
    }

    /**
     * Verify that it is allowed to upload the file.
     * @param Array $file PHP file info array of uploaded file.
     * @throws AccessDeniedException if file is forbidden by Stud.IP settings.
     */
    public static function verifyUpload($file) {
        $GLOBALS['msg'] = ''; // validate_upload will store messages here
        if (!validate_upload($file)) { // upload is forbidden
            // remove error pattern from message
            $error_pattern = utf8_decode('/error§(.+)§/');
            $message = preg_replace($error_pattern, '$1', $GLOBALS['msg']);

            // clear global messages and throw exception
            $GLOBALS['msg'] = '';
            throw new AccessDeniedException(
                studip_utf8encode(decodeHTML($message)));
        }
    }

    /**
     * Initialize Stud.IP metadata array for creating a new Stud.IP document.
     *
     * @param string $folder_id     ID of Stud.IP folder in which the document
     *                              is generated.
     * @param array  $file          Array containing metadata of the uploaded
     *                              file.
     *
     * @return array    Stud.IP document metadata
     */
    public static function getStudipDocumentData($folder_id, $file) {
        $filename = studip_utf8decode($file['name']);
        $document['name'] = $document['filename'] = $filename;
        $document['user_id'] = $GLOBALS['user']->id;
        $document['author_name'] = get_fullname();
        $document['seminar_id'] = RichTextPluginUtils::getSeminarId();
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
        error_log('getMediaUrl ' . $url);

        // some values we need later
        $base_url = $GLOBALS['ABSOLUTE_URI_STUDIP'];
        $media_proxy = $base_url . 'dispatch.php/media_proxy?url=';
        $LOAD_EXTERNAL_MEDIA = Config::GetInstance()->getValue('LOAD_EXTERNAL_MEDIA');

        error_log('studip base: ' . $base_url);
        error_log('media proxy: ' . $media_proxy);
        error_log('load extern: ' . $LOAD_EXTERNAL_MEDIA);

        // clean up URLs that already access the media proxy
        if (RichTextPluginUtils::startsWith($url, $media_proxy)) {
            $url = urldecode(RichTextPluginUtils::removePrefix($url, $media_proxy));
        }

        if (RichTextPluginUtils::isStudipUrl($url)) {
            error_log('isStudipUrl');

            $url_path = @parse_url($url, PHP_URL_PATH);
            $studip_path = @parse_url($GLOBALS['ABSOLUTE_URI_STUDIP'], PHP_URL_PATH);

            $path = RichTextPluginUtils::removePrefix($url_path, $studip_path);

            error_log('url path: ' . $path);

            if (RichTextPluginUtils::isStudipMediaUrlPath($path)) {
                // TODO remove scheme+host from internal Stud.IP URLs
                return idna_link(TransformInternalLinks($url));
            }
            $GLOBALS['msg'][] = 'Invalid internal link removed: ' . htmlentities($url);
            return NULL; // invalid internal link ==> remove <img src> attribute
        }
        if ($LOAD_EXTERNAL_MEDIA === 'proxy' && Seminar_Session::is_current_session_authenticated()) {
            error_log('load proxy');
            // TODO remove scheme+host from internal media proxy
            // TODO can media proxy be external at all?
            return $media_proxy . urlencode(idna_link($url));
        }
        if ($LOAD_EXTERNAL_MEDIA === 'allow') {
            error_log('load external');
            return $url;
        }
        $GLOBALS['msg'][] = 'External media denied: ' . htmlentities($url);
        return NULL; // deny external media ==> remove <img src> attribute
    }

    /**
     * Test if given URL points to an internal Stud.IP resource.
     * @param string $url   The URL that is tested.
     * @return boolean      TRUE if URL points to internal Stud.IP resource,
     *                      otherwise FALSE.
     */
    public static function isStudipUrl($url) {
        $studip_url = @parse_url($GLOBALS['ABSOLUTE_URI_STUDIP']);
        assert(is_array($studip_url)); // otherwise something's wrong with studip

        error_log('studip url: ' . print_r($studip_url, 1));

        $parsed_url = @parse_url($url);
        if ($parsed_url === FALSE) {

            error_log('url is seriously malformed');

            return FALSE; // url is seriously malformed
        }

        error_log('parsed url: ' . print_r($parsed_url, 1));

        $studip_schemes = array($studip_url['scheme'], 'http', 'https', NULL);
        $studip_hosts = array($studip_url['host'], NULL);
        $studip_ports = array($studip_url['host'], NULL);

        $is_scheme = in_array($parsed_url['scheme'], $studip_schemes);
        $is_host = in_array($parsed_url['host'], $studip_hosts);
        $is_port = in_array($parsed_url['port'], $studip_ports);
        $is_path = RichTextPluginUtils::startsWith($parsed_url['path'], $studip_url['path']);
        $is_studip = $is_scheme && $is_host && $is_port && $is_path;

        error_log('is scheme: ' . $is_scheme);
        error_log('is host: ' . $is_host);
        error_log('is port: ' . $is_port);
        error_log('is path: ' . $is_path);
        error_log('is studip:' . $is_studip);

        // TODO what about studip's that are accessible from multiple urls? 
        // (like: uos.de, uni-osnabrueck.de)
        return $is_studip;
    }

    /**
     * Test if given URL path is valid for internal Stud.IP media files.
     * @params string $path The path component of an URL.
     * return boolean       TRUE for valid media paths, FALSE otherwise.
     */
    public function isStudipMediaUrlPath($path) {
        list($path_head) = explode('/', $path);
        $valid_paths = array('sendfile.php', 'download', 'assets', 'pictures');
        return in_array($path_head, $valid_paths);
    }

    /**
     * Verify that user has needed permission.
     * @param string $permission Minimum requested permission level.
     * @throws AccessDeniedException if user does not have permission.
     */
    public function verifyPermission($permission) {
        $context = RichTextPluginUtils::getSeminarId();
        if (!$GLOBALS['perm']->have_studip_perm($permission, $context)) {
            throw new AccessDeniedException(sprintf(studip_utf8decode(_('Es werden mindestens "%s"-Zugriffsrechte benötigt.')), $permission));
        }
    }

    /**
     * Verify that HTTP request was send as HTTP POST
     * @throws AccessDeniedException if request was not send as HTTP POST.
     */
    public function verifyPostRequest() {
        if (!Request::isPost()) {
            throw new AccessDeniedException(_('Die Anfrage muss als HTTP POST gestellt werden.'));
        }
    }
}

