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
require_once 'Purifier.php';
require_once 'Utils.php';
use RichTextPlugin\Purifier as Purifier;
use RichTextPlugin\Utils as Utils;

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
        $this->addScript('script.js');
    }

    /**
     * Implements abstract method of base class.
     */
    public function getIconNavigation($course_id, $last_visit, $user_id) {
        if (!$this->isActivated($course_id)) {
            return;
        }
        $icon = new AutoNavigation(_('RichText'), PluginEngine::getLink($this, array(), "show"));
        $icon->setImage($this->getIcon('grey'));
        $icon->setTitle(_('RichText-Editor'));
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

    public function test_action() {
        Utils\testGetMediaUrl();
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
        $this->renderBodyTemplate('show');
    }

    /**
     * Initialize edit_ckeditor.php template for editing the page.
     */
    public function edit_action() {
        Utils\verifyPermission($this->edit_permission);
        $this->actionHeader();
        $this->addScript('jquery.autosize-min.js');
        $this->addScript('vendor/jquery.ui.widget.js');
        $this->addScript('jquery.iframe-transport.js');
        $this->addScript('jquery.fileupload.js');
        $this->addScript('ckeditor/ckeditor.js');
        $this->renderBodyTemplate('edit');
    }

    public function benchmark_action() {
        $db = DBManager::get();
        $stmt = $db->prepare('SELECT body FROM wiki WHERE range_id like "8%"');
        $stmt->execute();
        $time_formatReady = 0;
        $time_purifier = 0;
        $count = 0;

        $hist = Array(
            100 => [0, 0, 0],
            1000 => [0, 0, 0],
            10000 => [0, 0, 0],
            100000 => [0, 0, 0],
            'greater' => [0, 0, 0]);

        function p($count, $time_formatReady, $time_purifier, $hist) {
            echo '<p>';
            echo "count $count formatReady $time_formatReady purifier $time_purifier";
            echo '<br>';
            echo json_encode($hist);
            echo '</p>';
        }

        while (($column = $stmt->fetchColumn()) !== FALSE) {
            //echo "<p>$column</p>"; flush(); ob_flush();
            $count += 1;
            $start = microtime(TRUE);
            $html = formatReady($column);
            $formatReady = microtime(TRUE) - $start;
            $start = microtime(TRUE);
            $clean_html = Purifier\purify($html);
            $purifier = microtime(TRUE) - $start;

            $time_formatReady += $formatReady;
            $time_purifier += $purifier;
    
            $len = strlen($column);
            $hist_len = &$hist['greater'];
            if ($len < 100) {
                $hist_len = &$hist[100];
            } else if ($len < 1000) {
                $hist_len = &$hist[1000];
            } else if ($len < 10000) {
                $hist_len = &$hist[10000];
            } else if ($len < 100000) {
                $hist_len = &$hist[100000];
            }
            $hist_len[0] += 1;
            $hist_len[1] += $formatReady;
            $hist_len[2] += $purifier;
    
            if (($count % 10) == 0) {
                p($count, $time_formatReady, $time_purifier, $hist);
                flush();
                ob_flush();
            }
        }
        p($count, $time_formatReady, $time_purifier, $hist);
    }


    /**
     * Opens, initializes and renders a template that gets the DB's text.
     * @params string $file Template file name, ommitting path.
     */
    protected function renderBodyTemplate($file) {
        $template = $this->template_factory->open($file);
        $template->set_layout($GLOBALS['template_factory']->open('layouts/base'));
        $template->body = $this->getBody();
        echo $template->render();
    }

    /**
     * Handle file upload requests.
     */
    public function post_file_action() {
        Utils\verifyPostRequest();
        $this->verifyUnsafeRequest();

        // store uploaded files as StudIP documents
        $response = array(); // data for HTTP response
        $folder_id = Utils\getFolderId(
            'RichText',
            studip_utf8decode(_('Durch das RichText-Plugin hochgeladene Dateien.')));

        foreach ($_FILES as $file) {
            try {
                $newfile = Utils\uploadFile($file, $folder_id);
                $response['files'][] = Array(
                    'name' => utf8_encode($newfile['filename']),
                    'type' => $file['type'],
                    'url' => GetDownloadLink($newfile->getId(), $newfile['filename']));
            } catch (AccessDeniedException $e) { // creation of Stud.IP doc failed
                $response['files'][] = Array(
                    'name' => $file['name'],
                    'type' => $file['type'],
                    'error' => $e->getMessage());
            }
        }
        Utils\sendAsJson($response);
    }

    /**
     * Retrieve text body from database.
     * @return mixed  Text from database or FALSE if there is no text.
     */
    protected function getBody() {
        $db = DBManager::get();
        $stmt = $db->prepare('SELECT body FROM plugin_rich_text WHERE range_id = ?');
        $stmt->execute(array(Utils\getSeminarId()));
        return Purifier\purify($stmt->fetchColumn());
    }

    /**
     * Store text body in database.
     * @param string $body  Text that is stored in the database.
     */
    protected function setBody($body) {
        $clean_body = Purifier\purify($body);
        $db = DBManager::get();
        $stmt = $db->prepare("REPLACE INTO plugin_rich_text VALUES(?, ?)");
        $stmt->execute(array(Utils\getSeminarId(), $clean_body));
    }

    /**
     * Point to a JavaScript file in HTML <head>.
     * @param string $path Path to the file. 
     */
    protected function addScript($path) {
        PageLayout::addHeadElement('script', array(
            'src' => $this->assets . $path,
            'charset' => 'utf-8'), '');
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
        $this->addSubNavigation(_('Lesen'), 'show');
        if (Utils\hasPermission($this->edit_permission)) {
            $this->addSubNavigation(_('Bearbeiten'), 'edit');
        }
    }

    /**
     * Add the given item to the subnavigation of this object.
     * @param string $title     Title displayed to user.
     * @param string $action    Executed action when title is clicked.
     */
    protected function addSubNavigation($title, $action) {
        $this->getTabNavigationItem()->addSubNavigation($action, new Navigation(
            $title, PluginEngine::getLink($this, array(), $action)));
    }

    /**
     * Verify that user has edit permission and correct security token.
     */
    public function verifyUnsafeRequest() {
        Utils\verifyPermission($this->edit_permission);
        CSRFProtection::verifyUnsafeRequest();
    }
}
