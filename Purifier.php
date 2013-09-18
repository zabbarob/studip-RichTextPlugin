<?php namespace RichTextPlugin\Purifier;
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
if (!class_exists('HTMLPurifier_Bootstrap')) {
    require_once 'HTMLPurifier/HTMLPurifier.auto.php';
}
require_once 'Utils.php';
use RichTextPlugin\Utils as Utils;

/**
 * Remove invalid <img src> attributes.
 */
class AttrTransform_Image_Source extends \HTMLPurifier_AttrTransform {
    /**
     * Implements abstract method of base class.
     */
    function transform($attr, $config, $context) {
        $attr['src'] = Utils\getMediaUrl($attr['src']);
        return $attr;
    }
}

/**
 * Create HTML purifier instance with Stud.IP-specific configuration.
 * @return HTMLPurifier A new instance of the HTML purifier.
 */
function createPurifier() {
    $config = \HTMLPurifier_Config::createDefault();
    $config->set('Core.Encoding', 'ISO-8859-1');
    $config->set('Core.RemoveInvalidImg', true);
    $config->set('Attr.AllowedFrameTargets', array('_blank'));
    $config->set('Attr.AllowedRel', array('nofollow'));

    # TODO remove this setting when Stud.IP runs with UTF-8!!!
    #
    # This setting is necessary since ISO-8859-1 cannot encode all
    # characters (e.g. math symbols like ∑, ∫, are unavailable).
    #
    # The HTML purifier developers strongly recommend using UTF-8 and state on
    # their homepage:
    # "HTML Purifier, in order to protect against sophisticated escaping
    #  schemes, normalizes all character and numeric entity references before
    #  processing the text."
    #  http://htmlpurifier.org/docs/enduser-utf8.html#whyutf8-htmlpurifier
    #
    # Also have a look at the documentation of this setting at
    # http://htmlpurifier.org/live/configdoc/plain.html#Core.EscapeNonASCIICharacters
    $config->set('Core.EscapeNonASCIICharacters', true);

    // avoid <img src="evil_CSRF_stuff">
    $def = $config->getHTMLDefinition(true);
    $img = $def->addBlankElement('img');
    $img->attr_transform_post[] = new AttrTransform_Image_Source();

    return new \HTMLPurifier($config);
}

/**
 * Call HTMLPurifier to create safe HTML.
 *
 * @param   string $dirty_html  Unsafe or 'uncleaned' HTML code.
 * @return  string              Clean and safe HTML code.
 */
function purify($dirty_html) {
    // remember created purifier so it doesn't have to be created again
    static $purifier = NULL;
    if ($purifier === NULL) {
        $purifier = createPurifier();
    }
    return $purifier->purify($dirty_html);
}
