<?php
/**
 * RichTextPlugin.php - A single-page HTML5 WYSIWYG editor for Stud.IP.
 *
 * This file contains the plugin's main class.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Robert Costa <zabbarob@gmail.com>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 */
require_once 'HTMLPurifier/HTMLPurifier.auto.php';
require_once 'RichTextPluginUtils.php';

/**
 * Initializes and displays the plugin.
 **/
class RichTextPlugin extends StudIPPlugin implements StandardPlugin
{
    /**
     * Constructor of the class.
     * 
     * Configures the navigation link where the plugin can be reached in
     * Stud.IP and does some general plugin initialization stuff.
     */
    public function __construct() {
        parent::__construct();

        $navigation = new AutoNavigation(_('RichText'));
        $navigation->setURL(PluginEngine::GetLink($this, array(), 'show'));
        $navigation->setImage(Assets::image_path('blank.gif'));
        Navigation::addItem('/course/rich', $navigation);

        $this->template_factory = new Flexi_TemplateFactory($this->getPluginPath() . '/templates');
    }

    /**
     * Loads stylesheets and scripts needed for executing the plugin.
     */
    public function initialize () {
        $assets = $this->getPluginURL() . '/assets/';
        PageLayout::addStylesheet($assets . 'editor.css');
        //PageLayout::addStylesheet('http://yui.yahooapis.com/2.9.0/build/reset/reset-min.css');
        PageLayout::addStylesheet($assets . 'styles.css');
        PageLayout::addScript($assets . 'advanced.js');
        PageLayout::addScript($assets . 'wysihtml5-0.3.0.js');
        PageLayout::addScript($assets . 'formdata.js');
        PageLayout::addScript($assets . 'script.js');
    }

    /**
     * Implements abstract method of base class.
     */
    public function getIconNavigation($course_id, $last_visit, $user_id) {
    }

    /**
     * Implements abstract method of base class.
     */
    public function getInfoTemplate($course_id) {
    }

    /**
     * Implements abstract method of base class.
     */
    public function getTabNavigation($course_id) {
    }
 
    /**
     * Implements abstract method of base class.
     */
    public function getNotificationObjects($course_id, $since, $user_id) {
    }

    /**
     * Sets the fields in the plugin's show.php template to correct values.
     */
    public function show_action() {
        if (Request::submitted('save')) {
            $this->setBody(Request::get('body'));
        }

        $template = $this->template_factory->open('show');
        $template->set_layout($GLOBALS['template_factory']->open('layouts/base'));

        $template->body = $this->getBody();
        if (!$template->body) {
            $template->nothing = _('Bisher wurde noch kein Text eingetragen.');
        }

        echo $template->render();
    }

    /**
     * Sets the fields in the plugin's show.php template to correct values.
     */
    public function edit_action() {
        $template = $this->template_factory->open('edit');
        $template->set_layout($GLOBALS['template_factory']->open('layouts/base'));

        $template->title = $this->getPluginName();
        $template->icon_url = $this->getPluginURL() . '/images/icon.gif';
        $template->body = $this->getBody();

        echo $template->render();
    }

    public function post_file_action() {
        $context = RichTextPluginUtils::getSeminarId();
        /* TODO security-check?
        $context = Request::option("context") ? Request::get("context") : $GLOBALS['user']->id;
        $context_type = Request::option("context_type");
        if (!Request::isPost()
                || ($context_type === "course" && !$GLOBALS['perm']->have_studip_perm("autor", $context))) {
            throw new AccessDeniedException("Kein Zugriff");
        }
        */

        // get file folder, create if it doesn't exist
        $folder_id = md5('RichText_' . $context);
        if (!RichTextPluginUtils::getFolder($folder_id)) {
            // TODO add description (shown in studip document browser)
            RichTextPluginUtils::createFolder($context, $folder_id, 'RichText');
        }

        // store uploaded files as StudIP documents
        $output = array();

        foreach ($_FILES as $file) {
            if (!$file['size']) {
                continue; // ignore empty files TODO really?
            }

            // retrieve information for creating file
            $filename = studip_utf8decode($file['name']);
            $document['name'] = $document['filename'] = $filename;
            $document['user_id'] = $GLOBALS['user']->id;
            $document['author_name'] = get_fullname();
            $document['seminar_id'] = $context;
            $document['range_id'] = $folder_id;
            $document['filesize'] = $file['size'];

            // create file
            // TODO check why README.md was uploaded but then copied with 0 bytes
            $newfile = StudipDocument::createWithFile($file['tmp_name'], $document);
            if (!$newfile) {
                continue; // file creation failed TODO store error message
            }

            // get download link
            $url = GetDownloadLink($newfile->getId(), $newfile['filename']);

            // determine data type of file / determine markup tag
            // TODO tags should be created by client-side, server should return mime-type
            $type = null;
            if (strpos($file['type'], 'image')) {
                $type = "img";
            } else if (strpos($file['type'], 'video')) {
                $type = "video";
            } else if (strpos($file['type'], 'audio')) {
                $type = "audio";
            } else {
                // TODO insert link for unknown file types
                $type = $newfile['filename'];
            }

            // return link to file, enclosed in required markup tag
            $output['inserts'][] = "[" . $type . "]" . $url;
        }
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($output);
    }

    /**
     * Retrieve text body from database.
     * 
     * @return mixed  Text from database or FALSE if there is no text.
     */
    protected function getBody() {
        $db = DBManager::get();
        $stmt = $db->prepare('SELECT body FROM plugin_rich_text WHERE range_id = ?');
        $stmt->execute(array(RichTextPluginUtils::getSeminarId()));
        return RichTextPlugin::purify($stmt->fetchColumn());
    }

    /**
     * Store text body in database.
     *
     * @param string $body  Text that is stored in the database.
     */
    protected function setBody($body) {
        $clean_body = RichTextPlugin::purify($body);
        $db = DBManager::get();
        $stmt = $db->prepare("REPLACE INTO plugin_rich_text VALUES(?, ?)");
        $stmt->execute(array(RichTextPluginUtils::getSeminarId(), $clean_body));
    }

    /**
     * Call HTMLPurifier to create safe HTML.
     *
     * @param string $dirty_html Unsafe or 'uncleaned' HTML code.
     *
     * @return string  Clean and safe HTML code.
     */
    protected static function purify($dirty_html) {
        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);
        return $purifier->purify($dirty_html);
     }
}

