<?php namespace RichTextPluginPurifier;
/**
 * RichTextPluginPurifier.php - Auxilliary function for using HTML Purifier.
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

/**
 * Remove invalid <img src> attributes.
 */
class AttrTransform_Image_Source extends \HTMLPurifier_AttrTransform
{
    /**
     * Change attributes, if necessary. Gets called back by HTML Purifier.
     * Implements abstract base class method.
     */
    function transform($attr, $config, $context) {
        $attr['src'] = \RichTextPluginUtils::getMediaUrl($attr['src']);
        return $attr;
    }
}

/**
 * Call HTMLPurifier to create safe HTML.
 *
 * @param   string $dirty_html  Unsafe or 'uncleaned' HTML code.
 * @return  string              Clean and safe HTML code.
 */
function purify($dirty_html) {
    $config = \HTMLPurifier_Config::createDefault();
    $config->set('Core.Encoding', 'ISO-8859-1');
    $config->set('Core.RemoveInvalidImg', true);
    $config->set('Attr.AllowedFrameTargets', array('_blank'));
    $config->set('Attr.AllowedRel', array('nofollow'));

    // avoid <img src="evil_CSRF_stuff">
    $def = $config->getHTMLDefinition(true);
    $img = $def->addBlankElement('img');
    $img->attr_transform_post[] = new AttrTransform_Image_Source();

    $purifier = new \HTMLPurifier($config);
    return $purifier->purify($dirty_html);
}

