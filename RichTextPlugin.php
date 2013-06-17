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
require_once 'RichTextPluginPurifier.php';
require_once 'RichTextPluginUtils.php';

/**
 * Initializes and displays the plugin.
 **/
class RichTextPlugin extends StudIPPlugin implements StandardPlugin
{
    protected $navlink = '/course/rich'; // plugin location in tab navigation bar
    protected $assets; // URL of assets folder; set in __construct()
    protected $edit_permission = 'autor'; // minimum permission level for editing

    /**
     * Constructor of the class.
     * 
     * Configures the navigation link where the plugin can be reached in
     * Stud.IP and does some general plugin initialization stuff.
     */
    public function __construct() {
        parent::__construct();
        $this->template_factory = new Flexi_TemplateFactory($this->getPluginPath() . '/templates');
        $this->assets = $this->getPluginURL() . '/assets/';
    }

    /**
     * Loads stylesheets and scripts needed for executing the plugin.
     */
    public function initialize () {
        PageLayout::addStylesheet($this->assets . 'styles.css');
        PageLayout::addStylesheet($this->assets . 'wysihtml5-colors.css');
        $this->addScript('advanced.js');
        $this->addScript('formdata.js');
        $this->addScript('script.js');
    }

    /**
     * Implements abstract method of base class.
     */
    public function getIconNavigation($course_id, $last_visit, $user_id) {
        if (!$this->isActivated($course_id)) {
            return;
        }
        $icon = new AutoNavigation(_("RichText"), PluginEngine::getLink($this, array(), "show"));
        $icon->setImage($this->getIcon('grey'));
        $icon->setTitle(_("RichText-Editor"));
        return $icon;
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
        Navigation::addItem($this->navlink, $navigation);

        $this->setTabNavigationIcon('white');
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
            CSRFProtection::verifyUnsafeRequest();
            $this->setBody(Request::get('body'));
        }
        $this->actionHeader();

        $template = $this->template_factory->open('show');
        $template->set_layout($GLOBALS['template_factory']->open('layouts/base'));

        $template->body = $this->getBody();
        if (!$template->body) {
            $template->nothing = _('Bisher wurde noch kein Text eingetragen.');
        }

        echo $template->render();
    }

    /**
     * Initialize edit_wysihtml5.php template for editing the page.
     */
    public function edit_wysihtml5_action() {
        PageLayout::addStylesheet($this->assets . 'wysihtml5.css');
        $this->initializeEditor('wysihtml5-0.3.0.js', 'edit_wysihtml5');
    }

    /**
     * Initialize edit_tinymce.php template for editing the page.
     */
    public function edit_tinymce_action() {
        $this->initializeEditor('tinymce/tinymce.min.js', 'edit_tinymce');
    }

    /**
     * Initialize edit_nicedit.php template for editing the page.
     */
    public function edit_nicedit_action() {
        $this->initializeEditor('nicEdit.js', 'edit_nicedit');
    }

    /**
     * Initializes the editor given by its script and template.
     * @param string $script Path to the editor's main JavaScript file.
     * @param string $template Path to the editor's PHP template file.
     */
    public function initializeEditor($script, $template) {
        $this->verifyUnsafeRequest();
        $this->actionHeader();
        $this->addScript($script);

        $template = $this->template_factory->open($template);
        $template->set_layout($GLOBALS['template_factory']->open('layouts/base'));

        $template->body = $this->getBody();

        echo $template->render();
    }

    /**
     * Handle file upload requests.
     */
    public function post_file_action() {
        RichTextPluginUtils::verifyPostRequest();
        $this->verifyUnsafeRequest();

        // store uploaded files as StudIP documents
        $output = array(); // data for HTTP response
        $folder_id = RichTextPluginUtils::getFolderId(
            'RichText',
            studip_utf8decode(_('Enthält vom RichText-Plugin hochgeladene Dateien.')));

        foreach ($_FILES as $file) {
            try {
                $newfile = RichTextPluginUtils::uploadFile($file, $folder_id);
                $output['inserts'][] = Array(
                    'name' => $newfile['filename'],
                    'type' => $file['type'],
                    'url' => GetDownloadLink($newfile->getId(), $newfile['filename']));
            } catch (AccessDeniedException $e) { // creation of Stud.IP doc failed
                // TODO output format for errors should be similar to inserts
                $output['errors'][] = $file['name'] . ': ' . $e->getMessage();
            }
        }

        header('Content-type: application/json; charset=utf-8');
        echo json_encode($output);
    }

    /**
     * Retrieve text body from database.
     * @return mixed  Text from database or FALSE if there is no text.
     */
    protected function getBody() {
        $db = DBManager::get();
        $stmt = $db->prepare('SELECT body FROM plugin_rich_text WHERE range_id = ?');
        $stmt->execute(array(RichTextPluginUtils::getSeminarId()));
        return \RichTextPluginPurifier\purify($stmt->fetchColumn());
    }

    /**
     * Store text body in database.
     * @param string $body  Text that is stored in the database.
     */
    protected function setBody($body) {
        $clean_body = \RichTextPluginPurifier\purify($body);
        $db = DBManager::get();
        $stmt = $db->prepare("REPLACE INTO plugin_rich_text VALUES(?, ?)");
        $stmt->execute(array(RichTextPluginUtils::getSeminarId(), $clean_body));
    }

    /**
     * Point to a JavaScript file in HTML <head>.
     * @param string $path Path to the file. 
     */
    protected function addScript($path) {
        PageLayout::addScript($this->assets . $path);
    }

    /**
     * Set the plugin icon in Stud.IP's navigation bar.
     * @param string $color Color in which the icon is displayed.
     */
    protected function setTabNavigationIcon($color) {
        $this->getTabNavigationItem()->setImage($this->getIcon($color));
    }

    /**
     * Get the plugin's navigation bar item entry.
     * @return object Navigation bar item.
     */
    protected function getTabNavigationItem() {
        return Navigation::getItem($this->navlink);
    }

    /**
     * Get image path to the plugin icon.
     * @param string $color The color of the icon.
     * return string Image path to the icon.
     */
    protected function getIcon($color) {
        return Assets::image_path('icons/16/' . $color . '/forum.png');
    }

    /**
     * Executes functions that have to be called by each action handler.
     */
    protected function actionHeader() {
        Navigation::activateItem($this->navlink);
        $this->setTabNavigationIcon('black');
        $this->getTabNavigationItem()->addSubNavigation('show', new Navigation(_('RichText'), PluginEngine::getLink($this, array(), 'show')));
    }

    /**
     * Verify that user has edit permission and correct security token.
     */
    public function verifyUnsafeRequest() {
        RichTextPluginUtils::verifyPermission($this->edit_permission);
        CSRFProtection::verifyUnsafeRequest();
    }
}

