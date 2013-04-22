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
        PageLayout::addStylesheet($assets . 'styles.css');
        PageLayout::addScript($assets . 'advanced.js');
        PageLayout::addScript($assets . 'wysihtml5-0.3.0.js');
        PageLayout::addScript($assets . 'script.js');
    }

    /**
     * Implements abstract method of base class.
     */
    public function getIconNavigation($course_id, $last_visit) {
    }

    /**
     * Implements abstract method of base class.
     */
    public function getInfoTemplate($course_id) {
    }

    /**
     * Sets the fields in the plugin's show.php template to correct values.
     */
    public function show_action() {
        if (Request::submitted('save')) {
            $this->setBody(RichTextPlugin::purify(Request::get('body')));
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

    /**
     * Retrieve text body from database.
     * 
     * @return mixed  Text from database or FALSE if there is no text.
     */
    protected function getBody() {
        $db = DBManager::get();
        $stmt = $db->prepare('SELECT body FROM plugin_rich_text WHERE range_id = ?');
        $stmt->execute(array(RichTextPluginUtils::getSeminarId()));
        return $stmt->fetchColumn();
    }

    /**
     * Store text body in database.
     *
     * @param string $body  Text that is stored in the database.
     */
    protected function setBody($body) {
        $db = DBManager::get();
        $stmt = $db->prepare("REPLACE INTO plugin_rich_text VALUES(?, ?)");
        $stmt->execute(array(RichTextPluginUtils::getSeminarId(), $body));
    }
}

