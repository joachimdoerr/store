<?php

// set get or request parameters
$params = array(
    'func' => rex_request::request('func', 'string'),
    'id' => rex_request::request('id', 'int'),
    'start' => rex_request::request('start', 'int', NULL),
    'channel' => rex_request::request('channel', 'int'),
    'channel_fail' => rex_request::request('channel_fail', 'boolean', false)
);

// defaults
$message = '';

// TODO actions, online/offline, delete
// TODO show msg

// actions
if ($params['channel_fail'] == 1) {

    // print content to fragment
    $fragment = new rex_fragment();
    $fragment->setVar('class', 'warning', false);
    $fragment->setVar('title', rex_i18n::msg('store_categories_channel_fail_headline'));
    $fragment->setVar('content', '<div class="panel-body">'.rex_i18n::msg('store_categories_channel_fail_msg').'</div>', false);
    echo $fragment->parse('core/page/section.php');

    // unset all views
    $params['func'] = 'fail';
}

// load channel
$sql = rex_sql::factory();
$sql->setQuery('SELECT * FROM rex_store_channels AS sc WHERE id="'.$params['channel'].'"');
$channel = ($sql->getRows() > 0) ? $sql->getRow() : array();

// views
// show list
if ($params['func'] == '' || $params['func'] == 'filter') {

    // create list
    $list = new AlfredListView($this->getAddon()->getName(), 'categories', 30, false, false); // don't create list in constructor
    // create query by definition
    $selects = $list->createSelect();

    // set name
    $name = 'name_' . rex_clang::getCurrentId();

    // use name from selects also remove name form select
    foreach ($selects as $key => $select) {
        if (strpos($select, 'name') !== false) {
            unset($selects[$key]);
        }
    }

    // set table key
    $k = $list->getDefinition()->getPayload('table_key');

    // create query for sql list for channel
    $query = "
SELECT  CONCAT(REPEAT('--', level - 1), ' ', $k.$name) AS name,
        category_sys_connect_by_path('/', $k.id) AS path,
        parent, level, ".implode(", ", $selects)."
FROM    (
        SELECT  category_connect_by_parent_eq_prior_id_with_level(id, 10) AS id,
                CAST(@level AS SIGNED) AS level
        FROM    (
                SELECT  @start_with := {$params['channel']},
                        @id := @start_with,
                        @level := 0
                ) vars, rex_store_categories
        WHERE   @id IS NOT NULL
        ) ho
JOIN    rex_store_categories $k
ON      $k.id = ho.id
ORDER BY path
    ";

    // execute for count
    $sql = rex_sql::factory();
    $sql->setQuery($query);

    // create list by query
    $list->createList($query);

    // add parameter for navigation and all other links
    $list->list->addParam('channel', rex_request::request('channel', 'int'));
    // set all rows for pagination
    $list->list->getPager()->setRowCount($sql->getRows());

    // remove columns for custom sql
    $list->list->removeColumn('path');
    $list->list->removeColumn('parent');
    $list->list->removeColumn('prio');
    $list->list->removeColumn('level');

    // use definition for default elements
    // add id element
    $list->addIdElement(array(), 'fa-folder-o fa-folder-###level###');
    // add all default elements by definitions
    $list->addDefaultElements();


    $content = '<div class="alfred-list">' . $message . $list->show() . '</div>';

    // print content to fragment
    $fragment = new rex_fragment();
    $fragment->setVar('title', sprintf(rex_i18n::msg('store_categories_list_channel_view'), '"'.$channel['sc.name'].'"'));
    $fragment->setVar('content', $content, false);
    echo $fragment->parse('core/page/section.php');


// show form
} elseif ($params['func'] == 'edit' || $params['func'] == 'add') {

    // create formular element
    $form = new AlfredFormView($this->getAddon()->getName(), 'categories', '', $params['id'], false, [array('key'=>'channel', 'type' => 'int', 'default'=>'')]);
    // add field elements by deifintions
    $form->addFieldElements();

    // print form to fragment
    $fragment = new rex_fragment();
    $fragment->setVar('class', 'edit', false);
    $fragment->setVar('title', ($params['func'] == 'edit') ? rex_i18n::msg('store_category_edit') : rex_i18n::msg('store_add_categories'));
    $fragment->setVar('body', $message . $form->show(), false);
    echo $fragment->parse('core/page/section.php');

}