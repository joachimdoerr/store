<?php
/**
 * @author Joachim Doerr
 * @support www.dev51.com
 * @package store.dev51
 */

//////////////////////////////
// is backend
if (rex::isBackend() && rex::getUser()) {
    //////////////////////////////
    // add assets
    rex_view::addCssFile($this->getAssetsUrl('css/' . $this->getAddon()->getName() . '_channels.css'));

    //////////////////////////////
    // register store func
    rex_extension::register('STORE_FUNC', function (rex_extension_point $params) {
        $parameter = $params->getSubject();

        if (is_array($parameter)
            && isset($parameter['func'])
            && isset($parameter['store_path'])
            && $parameter['store_path'] == 'store/channels/channels'
        ) {

            switch ($parameter['func']) {
                case 'setcat':
                    $parameter['message'] = StoreChannelsActions::createChannelCategory($parameter);
                    $parameter['func'] = ''; // no redirect?
                    break;
                case 'delete':
                    $parameter['message'] = StoreChannelsActions::deleteChannel($parameter);
                    $parameter['func'] = ''; // go to list...
                    break;
                case 'status':
                    $parameter['message'] = StoreChannelsActions::onlineOfflineChannel($parameter);
                    $parameter['func'] = ''; // go to list...
                    break;
                case 'edit':
                    // check sub func
                    if ($parameter['sub_func'] == 'delete_cat') { // delete cate
                        $parameter['message'] = StoreChannelsActions::deleteChannelCategory($parameter);
                    }
                    if ($parameter['sub_func'] == 'add_cat') { // add cat
                        $parameter['message'] = StoreChannelsActions::createChannelCategory($parameter);
                    }
                    break;
            }

        }
        return $parameter;
    });

    //////////////////////////////
    // register control fields for edit
    if (rex_request::request('func', 'string') == 'edit') {
        // remove delete button in form
        rex_extension::register('REX_FORM_CONTROL_FIELDS', function (rex_extension_point $params) {
            StoreChannelFormHelper::removeDeleteButton($params);
        });
    }

    //////////////////////////////
    // register form saved action
    rex_extension::register('REX_FORM_SAVED', function (rex_extension_point $params) {
        if (rex_request::request('store_path', 'string', '') ==  'store/channels/channels')
            StoreChannelsActions::postSaveChannel($params);
    });

    //////////////////////////////
    // register for replace default with cool headlines
//    rex_extension::register('STORE_TITLE', function (rex_extension_point $params) {
//        // read headline by config
//        $headline = StoreHeadlineProvider::getPluginSiteHeadlinesByConfig($this->getAddon());
//        /** @var rex_extension_point $params */
//        $headlineProvider = new StoreHeadlineProvider();
//        $headlineProvider->replaceDefaultHeadline($params, $headline['title'], $headline['subtitle'], $headline['icon'], $headline['replace']);
//    });
}