<?php

/**
 * RichTextPlugin.class.php
 *
 * A single-page rich text editor for Stud.IP.
 *
 * @author  Robert Costa <zabbarob@gmail.com>
 * @version 0.1a
 **/

class RichTextPlugin extends StudIPPlugin implements StandardPlugin
{

    public function __construct()
    {
        parent::__construct();

        $navigation = new AutoNavigation(_('Rich'));
        $navigation->setURL(PluginEngine::GetLink($this, array(), 'show'));
        $navigation->setImage(Assets::image_path('blank.gif'));
        Navigation::addItem('/course/rich', $navigation);

        $this->template_factory = new Flexi_TemplateFactory($this->getPluginPath() . '/templates');
    }

    public function initialize ()
    {
        PageLayout::addStylesheet($this->getPluginURL() . '/assets/styles.css');
        PageLayout::addScript($this->getPluginURL() . '/assets/simple.js');
        PageLayout::addScript($this->getPluginURL() . '/assets/wysihtml5-0.3.0.js');
        PageLayout::addScript($this->getPluginURL() . '/assets/script.js');
    }

    public function getIconNavigation($course_id, $last_visit)
    {
        // ...
    }

    public function getInfoTemplate($course_id)
    {
        // ...
    }

    public function show_action()
    {
        $template = $this->template_factory->open('show');
        $template->set_layout($GLOBALS['template_factory']->open('layouts/base'));

        $template->answer = 'Yes';

        echo $template->render();
    }
}
