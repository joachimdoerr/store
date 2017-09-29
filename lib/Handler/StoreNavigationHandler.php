<?php
/**
 * @package store
 * @author Joachim Doerr
 * @copyright (C) mail@doerr-softwaredevelopment.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


class StoreNavigationHandler
{
    const BASE_PATH = 'store/store_edit';

    /**
     * @param array $definition
     * @param $addonName
     * @author Joachim Doerr
     */
    public static function addNavigation(array $definition, $addonName)
    {
        // clean array for pages
        $pages = array();

        // definition is greater than 0
        if (sizeof($definition) > 0) { // go!
            foreach ($definition as $mainPage) {
                // add pages to main navigation points
                if (array_key_exists('pages', $mainPage) && $mainPage['pages'] > 0) {
                    // add all main sub pages
                    foreach ($mainPage['pages'] as $key => $page) {

                        try {
                            // create be page main navigation
                            $be_page = new rex_be_page_main($mainPage['name'], $page['name'], StoreHelper::getTitle($page));
                            $be_page->setHref(str_replace('&amp;', '&', rex_url::backendPage(self::getPath($page), self::getUrlParameter($page)))); # . '&' . http_build_query(self::getUrlParameter($page))); // create url by path and parameter
                            $be_page->setIcon('rex-icon ' . $page['icon']);
                            $be_page->setPrio($page['position']);

                            // if page viewed
                            if (isset($page['path']) && rex_request('page', 'string', '') == $page['path'] OR
                                (isset($page['url_parameter']['store_path']) && rex_request('store_path', 'string', '') == $page['url_parameter']['store_path'])
                            ) { // yes
                                if (rex_request::request('page', 'string') != $addonName && rex_request::request('page', 'string') == self::BASE_PATH) $addonName = 'store';

                                self::hiddenAllPoints($addonName); // default all is hidden

                                // set our point as active
                                $be_page->setIsActive();

                                // unset active state of addon page
                                $main_page = rex_addon::get($addonName)->getProperty('page');
                                $main_page['isActive'] = false;

                                rex_addon::get($addonName)->setProperty('page', $main_page);

                                // and now we add subpages
                                self::add2ndLevelNavigation($page, $addonName);
                            }

                            // add navigation point to array
                            $pages[] = $be_page;

                        } catch (\Exception $exception) {
                            // TODO add ERROR MSG
                        }
                    }
                }
            }
            // add navigation
            rex_addon::get($addonName)->setProperty('pages', $pages);
        }
    }

    /**
     * @param array $page
     * @author Joachim Doerr
     */
    private static function add2ndLevelNavigation(array $page, $addonName)
    {
        // default array
        $subpages = self::getSubpages($page);

        // only form
        if (rex_request::get('func') == 'edit') {
            // subpages exist? add default!
            $subpages = array_merge($subpages, self::getSubpages($page, 'subpages_form'), self::getSubpages($page, 'subpages_edit')); // subpages exist? add default!
        } elseif (rex_request::get('func') == 'add') {
            $subpages = array_merge($subpages, self::getSubpages($page, 'subpages_form'), self::getSubpages($page, 'subpages_add')); // subpages exist? add default!
        } else {
            $subpages = array_merge($subpages, self::getSubpages($page, 'subpages_list')); // only list
        }

        // subpages to add exist?
        if (sizeof($subpages) > 0) {
            // to add subpages i will use in categories
            rex_extension::register('PAGES_PREPARED', function (rex_extension_point $params) {
                // create page object
                $page = rex_be_controller::getPageObject($params->getParam('addon'));
                // use subpage array
                $subsites = $params->getParam('subsites');
                $subsitesCount = 0;

                foreach ($subsites as $site) {
                    if (is_array($site) &&
                        array_key_exists('active_parameter', $site) &&
                        array_key_exists('url_parameter', $site) &&
                        array_key_exists('name', $site)
                    ) {
                        $subsitesCount++;
                    }
                }

                // go for it
                foreach ($subsites as $key => $site) {
                    if (!is_array($site) or (
                            !array_key_exists('active_parameter', $site) or
                            !array_key_exists('url_parameter', $site) or
                            !array_key_exists('name', $site) or
                            (array_key_exists('notonly', $site) && ($site['notonly'] == true && $subsitesCount == 1))
                        )
                    ) {
                        continue;
                    }

                    // create be page object
                    $bePage = new rex_be_page($params->getParam('base_path') . '/' . $site['active_parameter'] . '/' . $site['name'], StoreHelper::getTitle($site));
                    $bePage->setHref('index.php?page=' . $params->getParam('base_path') . '&' . http_build_query($site['url_parameter'], null, '&', PHP_QUERY_RFC3986));

                    foreach (
                        array(
                            'icon' => 'setIcon',
                            'item_class' => 'addItemClass',
                            'link_class' => 'addLinkClass',
                            'pjax' => 'setPjax',
                            'active' => 'setIsActive',
                            'href' => 'setHref'
                        ) as $property => $method) {
                        if (array_key_exists($property, $site) && !empty($site[$property])) {
                            $bePage->$method($site[$property]);
                        }
                    }

                    // if filter setted
                    if (array_key_exists($site['active_parameter'], $site['url_parameter']) && rex_request::request($site['active_parameter']) == $site['url_parameter'][$site['active_parameter']]) {
                        $bePage->setIsActive(true);

                        // TODO ...
                        // add subsub

                        if (array_key_exists('subpages', $site)) {
                            foreach ($site['subpages'] as $siteSubpage) {
                                $beSubpage = new rex_be_page($params->getParam('base_path') . '/' . $site['active_parameter'] . '/' . $site['name'] . '/' . $siteSubpage['name'], $siteSubpage['title']);
                                // TODO add href
                                // TODO active
                                $bePage->addSubpage($beSubpage);
                            }
                        }
                    }
                    // add navigation
                    $page->addSubpage($bePage);
                }

            }, 0, array('addon' => $addonName, 'subsites' => $subpages, 'base_path' => self::getPath($page)));
        }
    }

    /**
     * @param array $page
     * @param string $key
     * @return array
     * @author Joachim Doerr
     */
    private static function getSubpages(array $page, $key = 'subpages')
    {
        $subpages = array();

        // subpages exist? add default!
        if (array_key_exists($key, $page) && is_array($page[$key]) && sizeof($page[$key]) > 0) {
            foreach ($page[$key] as $subpage) { // add subpages to subpage array
                if (is_array($subpage)) { // subpage must to be an array
                    if (array_key_exists('callable', $subpage) && strpos($subpage['callable'], '::') !== false) {
                        // add callable for subpages
                        $result = call_user_func_array($subpage['callable'], array($subpage));
                        if (is_array($result)) {
                            $subpages = array_merge($subpages, $result);
                        }
                    } else {
                        $subpages[] = $subpage; // add default subpage
                    }
                }
            }
        }

        return $subpages;
    }

    /**
     * @param array $page
     * @return array
     * @author Joachim Doerr
     */
    private static function getUrlParameter(array $page)
    {
        $urlParameters = (array_key_exists('url_parameter', $page) && is_array($page['url_parameter'])) ? $page['url_parameter'] : array();

        // check is the parameter callable
        if (array_key_exists('url_parameter', $page) && is_string($page['url_parameter']) && strpos($page['url_parameter'], '::') !== false) {
            // callable
            $parameters = call_user_func($page['url_parameter']);
            if (is_array($parameters)) {
                $urlParameters = $parameters;
            }
        }

        return $urlParameters;
    }

    private static function getPath(array $page)
    {
        return (isset($page['path'])) ? $page['path'] : self::BASE_PATH;
    }

    /**
     * @author Joachim Doerr
     * @param $addonName
     */
    public static function hiddenAllPoints($addonName)
    {
        rex_extension::register('PAGES_PREPARED', function (rex_extension_point $params) {
            $page = rex_be_controller::getPageObject($params->getParam('addon'));
            foreach ($page->getSubPages() as $subPage)
                $subPage->setHidden(true);
        }, 0, array('addon' => $addonName));
    }
}