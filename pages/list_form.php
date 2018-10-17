<?php
/**
 * @package store
 * @author Joachim Doerr
 * @copyright (C) mail@doerr-softwaredevelopment.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//////////////////////////////
// params
use Basecondition\Utils\FormHelper;
use Basecondition\View\FormView;
use Basecondition\Utils\ListHelper;
use Basecondition\View\ListView;
use Basecondition\Utils\ViewHelper;

$params = array(
    'base_path' => rex_request::request('base_path', 'string'),
    'addon' => ViewHelper::getAddonByBasePath(),
    'plugin' => ViewHelper::getPluginByBasePath(),
    'search_file' => ViewHelper::getSearchFileByBasePath(),
    'func' => rex_request::request('func', 'string'),
    'sub_func' => rex_request::request('sub_func', 'string'),
    'rows' => rex_request::request('rows', 'int', 30),
    'id' => rex_request::request('id', 'int'),
    'list_icon' => rex_request::request('list_icon', 'string'),
    'debug' => false,
    'list_init' => true,
    'sort' => rex_request::request('sort', 'string'),
    'sorttype' => rex_request::request('sorttype', 'string'),
    'message' => '',
);
// url parameter
$params['url_parameters'] = array(
    'base_path' => $params['base_path'],
    'rows' => $params['rows'],
    'list_icon' => $params['list_icon'],
    'list' => $params['search_file'],
    'sort' => $params['sort'],
    'sorttype' => $params['sorttype'],
);


//////////////////////////////
// STORE_FUNC_ACTION
// rest parameter
$params = rex_extension::registerPoint(new rex_extension_point('STORE_FUNC_ACTION', $params));

///** @var GenericEvent $funcActionEvent */
//$funcActionEvent = StoreEvent::dispatch('', new StoreFuncActionEvent($params));
//$params = $funcActionEvent->getSubject();


// ACTION
if ($params['func'] != '' && !empty($params['search_file'])) {

//    $action = new StoreActionView(
//        $params['addon']->getName(),
//        $params['search_file'],
//        $func,
//        $params['id'],
//        $params['debug'],
//        $params['url_parameters']
//    );
//    $params['func'] = $action->getFunc();
//    $params['message'] = $action->getMessage();
}


//////////////////////////////
// show list
if ($params['func'] == '' && !empty($params['search_file'])) {

    //////////////////////////////
    // create list
    $list = new ListView(
        $params['addon']->getName(),
        $params['search_file'],
        $params['rows'],
        $params['debug'],
        $params['list_init'],
        $params['url_parameters']
    );
    $list->addIdElement(array('base_path'=>rex_request::request('base_path')), $params['list_icon']); // add id element
    $list->addDefaultElements(); // add defaults by definitions

    // parse list to fragment
    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg($params['base_path'] . '_list_view'));
    $fragment->setVar('content', ListHelper::wrapList($params['message'], $list), false);
    echo $fragment->parse('core/page/section.php');


//////////////////////////////
// show edit
} elseif (($params['func'] == 'edit' || $params['func'] == 'add') && !empty($params['search_file'])) {

    //////////////////////////////
    // show edit form
    if ($params['sub_func'] == '') {
        // add list parameter
        foreach (array('start', 'sort', 'sorttype') as $parameter) {
            $params['url_parameters'][$parameter] = rex_request::request($parameter, 'string');
        }

        // create form
        $form = new FormView(
            $params['addon']->getName(),
            $params['search_file'],
            '',
            $params['id'],
            $params['debug'],
            $params['url_parameters'], true
        );
        $form->addFieldElements(); // add field elements by defaults

        // parse form to fragment
        $fragment = new rex_fragment();
        $fragment->setVar('class', 'edit', false);
        $fragment->setVar('title', ($params['func'] == 'edit') ? rex_i18n::msg($params['base_path'] . '_edit') : rex_i18n::msg($params['base_path'] . '_add'));
        $fragment->setVar('body', FormHelper::wrapForm($params['message'], $form), false);
        echo $fragment->parse('core/page/section.php');


    //////////////////////////////
    // show sub func
    } else {

        //////////////////////////////
        // execute sub_func
        echo '<pre>';
        print_r($params);
        echo '</pre>';


    }
}
