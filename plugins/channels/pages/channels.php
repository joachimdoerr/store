<?php

// set get or request parameters
$params = array(
    'func' => rex_request::request('func', 'string'),
    'sub_func' => rex_request::request('sub_func', 'string'),
    'id' => rex_request::request('id', 'int'),
    'start' => rex_request::request('start', 'int', NULL)
);

// defaults
$message = '';

// TODO actions, online/offline, delete
// TODO show msg

// action func
switch ($params['func']) {
    case 'setcat':
        $message = StoreChannelActions::createChannelCategory($params);
        $params['func'] = ''; // no redirect?
        break;
    case 'delete':
        $message = StoreChannelActions::deleteChannel($params);
        $params['func'] = ''; // go to list...
        break;
    case 'status':
        $message = StoreChannelActions::onlineOfflineChannel($params);
        $params['func'] = ''; // go to list...
        break;
    case 'edit':
        //if (is_array($_POST)) {
        //    foreach ($_POST as $key => $item) {
        //        if (strpos($key, 'delete') !== false && $item == 1) {
        //            echo 'delete';
        //            die;
        //        }
        //    }
        //}

        if ($params['sub_func'] == 'delete_cat') {
            // delete cate .
            echo 'delete cat';
            $message = StoreChannelActions::deleteChannelCategory($params);
        }
        if ($params['sub_func'] == 'add_cat') {
            // add cat.
            $message = StoreChannelActions::createChannelCategory($params);
        }

        rex_extension::register('REX_FORM_CONTROL_FIELDS', function (rex_extension_point $params) {
            // check is category set?
            $id = rex_request::get('id', 'int', 0);
            $subject = $params->getSubject();

            if ($id > 0) {
                $sql = rex_sql::factory();
                $sql->setQuery('SELECT * FROM rex_store_channels AS sh WHERE id = ' . $id);
                $channel = $sql->getRow();

                if (!empty($channel['sh.category'])) {
                    // yes it is
                    $subject['delete'] = ''; // remove delete button
                }
            }

            return $subject;
        });

        break;
}

// register form saved action
rex_extension::register('REX_FORM_SAVED', function (rex_extension_point $params) {
    StoreChannelActions::preSaveChannel($params);
});


// view func
// show list
if ($params['func'] == '' || $params['func'] == 'filter') {

    // create list
    $list = new AlfredListView($this->getAddon()->getName(), 'channels');
    $list->addIdElement(array(), 'fa-long-arrow-right'); // add id element
    $list->addDefaultElements(); // add defaults by definitions

    $content = '<div class="alfred-list">' . $message . $list->show() . '</div>';

    // parse list to fragment
    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('store_channels_list_view'));
    $fragment->setVar('content', $content, false);
    echo $fragment->parse('core/page/section.php');


// show form
} elseif ($params['func'] == 'edit' || $params['func'] == 'add') {

    // create form
    $form = new AlfredFormView($this->getAddon()->getName(), 'channels', '', $params['id']);
    $form->addFieldElements(); // add field elements by defaults

    $content = '<div class="alfred-form">' . $message . $form->show() . '</div>';

    // parse form to fragment
    $fragment = new rex_fragment();
    $fragment->setVar('class', 'edit', false);
    $fragment->setVar('title', ($params['func'] == 'edit') ? rex_i18n::msg('store_channel_edit') : rex_i18n::msg('store_add_channels'));
    $fragment->setVar('body', $content, false);
    echo $fragment->parse('core/page/section.php');

}

# http://nihonto/redaxo/index.php?page=store/channels/channels&id=1&start=0&func=edit&list=channels
# http://nihonto/redaxo/index.php?page=store/channels/channels&func=delete_cat&list=channels&id=1&start=0&form=b7caeb85a2fa55f6cc812f01b7fcf89d