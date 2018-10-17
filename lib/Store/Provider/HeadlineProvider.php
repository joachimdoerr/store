<?php
/**
 * @package store
 * @author Joachim Doerr
 * @copyright (C) mail@doerr-softwaredevelopment.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Store\Provider;


use Basecondition\Navigation\NavigationProvider;
use DOMDocument;
use DOMElement;
use rex_addon_interface;
use rex_extension_point;
use rex_i18n;
use rex_plugin_interface;
use rex_request;

class HeadlineProvider
{
    /**
     * @param rex_extension_point $params
     * @param null $title
     * @param null $subtitle
     * @param null $icon
     * @param bool $replace
     * @return bool
     * @author Joachim Doerr
     */
    public function replaceDefaultHeadline(rex_extension_point $params, $title = null, $subtitle = null, $icon = null, $replace = true)
    {
        $dom = new DomDocument();
        $searchPage = mb_convert_encoding($params->getSubject(), 'HTML-ENTITIES', "UTF-8");
        @$dom->loadHTML($searchPage);
        $dom->preserveWhiteSpace = false;

        // get page header element
        $divs = $dom->getElementsByTagName('div');

        // get html
        $headline = $this->getCustomHeadline($title, $subtitle, $icon);

        if ($replace == false) {
            return false;
        }

        // create fragment
        $fragment = $dom->createDocumentFragment();
        $fragment->appendXML($headline);

        if ($divs->item(0)->getAttribute('class') == 'page-header') {

            /** @var DOMElement $childNode */
            foreach ($divs->item(0)->childNodes as $childNode) {
                if ($childNode->nodeName == 'h1') {
                    // remove default headline
                    $divs->item(0)->removeChild($childNode);
                }
            }
            // add fragment to
            $divs->item(0)->appendChild($fragment);
        }

        $params->setSubject($dom->saveHTML());
        return true;
    }

    /**
     * @param null $title
     * @param null $subtitle
     * @param null $icon
     * @param null $button
     * @return array
     * @author Joachim Doerr
     */
    public function getCustomHeadline($title = null, $subtitle = null, $icon = null, $button = null)
    {

        // TODO use fragment

        return '
<div class="row">
    <div class="col-xs-12 col-md-8">
        <div class="store-header">
            <i class="circular icon rex-icon ' . $icon . '"></i>
            <div class="headline">
                <h1>' . $title . '</h1>
                <h2>' . $subtitle . '</h2>
            </div>
        </div>  
        <div class="store-breadcrumb">
        </div>
    </div>
    <div class="col-xs-6 col-md-4">
        <div class="ui right floated buttons">' . $button . '</div>
    </div>
</div>
';

    }

    /**
     * @param rex_addon_interface $addon
     * @return array
     * @author Joachim Doerr
     */
    public static function getPluginSiteHeadlinesByConfig(rex_addon_interface $addon)
    {
        $page = rex_request::request('page', 'string');
        if (!empty(rex_request::request('base_path', 'string'))) {
            $page = rex_request::request('base_path', 'string');
        }

        $p = explode('/', $page);
        $func = rex_request::request('func', 'string', '');

        if ($func != 'add') {
            $func = '';
        }

        $title = null;
        $subtitle = null;
        $icon = null;
        $replace = false;

        if (is_array($p)) {
            if ($addon->getName() == $p[0]) {
                if ($addon->hasConfig('headline_' . $page) && $addon->hasConfig('subheadline_' . $page) && $addon->hasConfig('icon_' . $page)) {
                    $title = rex_i18n::msg($addon->getConfig('headline_' . $page));
                    $subtitle = rex_i18n::msg($addon->getConfig('subheadline_' . $page));
                    $icon = $addon->getConfig('icon_' . $page);

                    if ($func) {
                        $replace = true;
                    }
                }
            }
            if (isset($p[1]) && $addon->pluginExists($p[1])) {
                /** @var rex_plugin_interface $plugin */
                $plugin = $addon->getPlugin($p[1]);
                if ($plugin->hasConfig('headline_' . $page) && $plugin->hasConfig('subheadline_' . $page) && $plugin->hasConfig('icon_' . $page)) {
                    $title = rex_i18n::msg($plugin->getConfig('headline_' . $page));
                    $subtitle = rex_i18n::msg($plugin->getConfig('subheadline_' . $page));
                    $icon = (is_null($icon)) ? $plugin->getConfig('icon_' . $page) : $icon;
                    $replace = true;
                }
            }
        }
        return array('title' => $title, 'subtitle' => $subtitle, 'icon' => $icon, 'replace' => $replace);
    }

    /**
     * @param rex_addon_interface $addon
     * @param null $pluginKey
     * @return bool
     * @author Joachim Doerr
     */
    public static function addPluginSiteHeadlinesToConfig(rex_addon_interface $addon, $pluginKey = null)
    {
        // plugin exist
        if (!is_null($pluginKey) && !$addon->pluginExists($pluginKey)) {
            return false;
        }

        // set addon config
        if (is_null($pluginKey)) {
            // write addon config
        } else {
            // write plugin config
            $plugin = $addon->getPlugin($pluginKey);
            $nav_definition = NavigationProvider::getNavigationDefinition($addon->getName(), $addon->getDataPath('resources'));

            if (isset($nav_definition['data']) && is_array($nav_definition['data']) && sizeof($nav_definition['data']) > 0) {
                foreach ($nav_definition['data'] as $key => $main) {
                    foreach ($main as $k => $page) {
                        if (is_array($page)) {
                            foreach ($page as $ik => $item) {
                                //
                                //echo '<pre>';
                                //echo 'headline_' . $item['path'] . ' -> ' . $addon->getName().'_'.$item['name'].'_master_headline' ."\n";
                                //echo 'subheadline_' . $item['path'] . ' -> ' . $addon->getName().'_'.$item['name'].'_master_description'."\n";
                                //echo 'icon_' . $item['path'] . ' -> ' .  $item['icon']."\n";
                                //echo '</pre>';
                                //
                                //die;
                                if (isset($item['path']) && strpos($item['path'], $pluginKey) !== false) {
                                    $plugin->setConfig('headline_' . $item['path'], $addon->getName() . '_' . $item['name'] . '_master_headline');
                                    $plugin->setConfig('subheadline_' . $item['path'], $addon->getName() . '_' . $item['name'] . '_master_description');
                                    $plugin->setConfig('icon_' . $item['path'], $item['icon']);
                                } else if (isset($item['url_parameter']['base_path']) && strpos($item['url_parameter']['base_path'], $pluginKey) !== false) {
                                    $plugin->setConfig('headline_' . $item['url_parameter']['base_path'], $addon->getName() . '_' . $item['name'] . '_master_headline');
                                    $plugin->setConfig('subheadline_' . $item['url_parameter']['base_path'], $addon->getName() . '_' . $item['name'] . '_master_description');
                                    $plugin->setConfig('icon_' . $item['url_parameter']['base_path'], $item['icon']);
                                }
                            }
                        }
                    }
                }
            }
        }

        return true;
    }
}