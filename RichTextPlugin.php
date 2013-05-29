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
//include_once 'csrf-magic/csrf-magic.php';

//http://stackoverflow.com/q/2638640/641481
//http://htmlpurifier.org/phorum/read.php?3,4254
class HTMLPurifier_AttrTransform_Image_Source extends HTMLPurifier_AttrTransform
{
    function transform($attr, $config, $context) {
        //$this->confiscateAttr($attr, 'src');
        $attr['src'] = HTMLPurifier_AttrTransform_Image_Source::getMediaUrl($attr['src']);
        return $attr;
    }

    /**
     * Check if media proxy should be used and if so return the respective URL.
     *
     * @param string $url   URL to media file.
     * @return mixed        URL string to media file (possibly 'proxied')
     *                      or NULL if URL is invalid.
     */
    protected static function getMediaUrl($url) {
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
            return NULL; // invalid internal link ==> remove <img src> attribute
        }
        if ($LOAD_EXTERNAL_MEDIA === "proxy" && Seminar_Session::is_current_session_authenticated()) {
            return $GLOBALS['ABSOLUTE_URI_STUDIP'] . 'dispatch.php/media_proxy?url=' . urlencode(idna_link($url));
        }
        if ($LOAD_EXTERNAL_MEDIA === 'allow') {
            return $url;
        }
        return NULL; // deny external media ==> remove <img src> attribute
    }
}

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
        if (!$this->isActivated($course_id)) {
            return;
        }
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
        if (!$this->isActivated($course_id)) {
            return;
        }
        $navigation = new AutoNavigation(_('RichText'));
        $navigation->setURL(PluginEngine::GetLink($this, array(), 'show'));
        $navigation->setImage(Assets::image_path('blank.gif'));

        Navigation::addItem('/course/rich', $navigation);
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
        Navigation::activateItem("/course/rich");

        $template = $this->template_factory->open('edit');
        $template->set_layout($GLOBALS['template_factory']->open('layouts/base'));

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
        $description = studip_utf8decode(_('Enthält vom RichText-Plugin hochgeladene Dateien.'));
        RichTextPluginUtils::createFolder($context, $folder_id, 'RichText', $description);

        // store uploaded files as StudIP documents
        $output = array();

        foreach ($_FILES as $file) {
            /*
            $GLOBALS['msg'] = '';
            if ($context_type === "course") {
                validate_upload($file);
                if ($GLOBALS['msg']) {
                    $output['errors'][] = $file['name'] . ': ' . studip_utf8encode(decodeHTML(trim(substr($GLOBALS['msg'],6), '§')));
                    continue;
                }
            }
            */

            // create studip file
            $document = RichTextPluginUtils::getStudipDocumentData($context, $folder_id, $file);
            $newfile = StudipDocument::createWithFile($file['tmp_name'], $document);
            if (!$newfile) {
                continue; // file creation failed TODO store error message
            }

            $url = GetDownloadLink($newfile->getId(), $newfile['filename']);

            // return file info (name, type, url)
            $output['inserts'][] = Array(
                'name' => $newfile['filename'],
                'type' => $file['type'],
                'url' => $url);
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
        $config->set('Core.Encoding', 'ISO-8859-1');
        $config->set('Core.RemoveInvalidImg', true);
        $config->set('Attr.AllowedFrameTargets', array('_blank'));
        $config->set('Attr.AllowedRel', array('nofollow'));

        // avoid <img src="evil_CSRF_stuff">
        $def = $config->getHTMLDefinition(true);
        $img = $def->addBlankElement('img');
        $img->attr_transform_post[] = new HTMLPurifier_AttrTransform_Image_Source();

        $purifier = new HTMLPurifier($config);
        return $purifier->purify($dirty_html);
     }
}

