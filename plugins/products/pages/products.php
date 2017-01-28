<?php

//$page = rex_be_controller::getPageObject('store');
//echo '<pre>';
//print_r($page);
//echo '</pre>';

$debug = false;

// set get or request parameters
$params = array(
    'func' => rex_request::request('func', 'string'),
    'id' => rex_request::request('id', 'int'),
    'start' => rex_request::request('start', 'int', NULL),
    'channel' => rex_request::request('channel', 'int')
);

// defaults
$message = '';

// TODO actions, online/offline, delete
// TODO show msg

//if ($params['func'] == 'status') {
//    $message = MCalendarListHelper::toggleBoolData($tableProducts, $params['id'], 'status');
//    $params['func'] = '';
//}

if ($params['func'] == '' || $params['func'] == 'filter') {

    // create list
    $list = new AlfredListView($this->getAddon()->getName(), 'products');
    $list->addIdElement(array(), 'fa-sticky-note-o'); // add id row
    $list->addDefaultElements(); // add default elements by definitions

    // add list to params
    $params['list'] = $list;

    $preList = rex_extension::registerPoint(new rex_extension_point('STORE_PRODUCTS_PRE_LIST', '', $params));
    $postList = rex_extension::registerPoint(new rex_extension_point('STORE_PRODUCTS_POST_LIST', '', $params));

    $content = $message . $preList . $list->show() . $postList;

    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('store_products_list_view'));
    $fragment->setVar('content', $content, false);
    echo $fragment->parse('core/page/section.php');


} elseif ($params['func'] == 'edit' || $params['func'] == 'add') {

    $form = new AlfredFormView($this->getAddon()->getName(), 'products', '', $params['id'], true);
    $form->addFieldElements();

    $title = ($params['func'] == 'edit') ? rex_i18n::msg('store_product_edit') : rex_i18n::msg('store_product_add');
    $preForm = '';
    $pastForm = '';

    // register extension point

    $content = $preForm . $form->show() . $pastForm;

    $fragment = new rex_fragment();
    $fragment->setVar('class', 'edit', false);
    $fragment->setVar('title', $title);
    $fragment->setVar('body', $content, false);
    echo $fragment->parse('core/page/section.php');

}