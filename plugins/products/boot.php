<?php
/**
 * User: joachimdoerr
 * Date: 05.12.16
 * Time: 08:17
 */

if (rex::isBackend() && rex::getUser()) {
    // create plugin database schema
//    $databaseManager = new StoreDatabaseManager($this->getAddon()->getName());
//    $databaseManager->executeCustomTablesHandling();
//    StoreHeadlineProvider::addPluginSiteHeadlinesToConfig($this->getAddon(), $this->getName());


    //////////////////////////////
    // register store func
    rex_extension::register('STORE_FUNC_ACTION', function (rex_extension_point $params) {

        $parameter = $params->getSubject();

        // actions for products
        if (is_array($parameter)
            && isset($parameter['func'])
            && isset($parameter['store_path'])
            && $parameter['store_path'] == 'store/products/products'
        ) {

            switch ($parameter['func']) {
                case 'delete':
//                    $parameter['message'] = StoreChannelsActions::deleteChannel($parameter);
//                    $parameter['func'] = ''; // go to list...
                    echo 'DELETE';
                    break;
                case 'status':
//                    $parameter['message'] = StoreChannelsActions::onlineOfflineChannel($parameter);
//                    $parameter['func'] = ''; // go to list...
                    echo 'STATUS';
                    break;
                case 'edit':
                    // check sub func
                    if (isset($parameter['sub_func_action']) && $parameter['sub_func_action'] == 'lala') { // delete cate
                    }
                    break;
            }

        }

        // actions for attributes
        if (is_array($parameter)
            && isset($parameter['func'])
            && isset($parameter['store_path'])
            && $parameter['store_path'] == 'store/products/attributes'
        ) {

            switch ($parameter['func']) {
                case 'delete':
//                    $parameter['message'] = StoreChannelsActions::deleteChannel($parameter);
//                    $parameter['func'] = ''; // go to list...
                    echo 'Attr DELETE';
                    break;
            }

        }

        // actions for datasheets
        if (is_array($parameter)
            && isset($parameter['func'])
            && isset($parameter['store_path'])
            && $parameter['store_path'] == 'store/products/datasheets'
        ) {

            switch ($parameter['func']) {
                case 'delete':
//                    $parameter['message'] = StoreChannelsActions::deleteChannel($parameter);
//                    $parameter['func'] = ''; // go to list...
                    echo 'Datas DELETE';
                    break;
            }

        }

        // actions for options
        if (is_array($parameter)
            && isset($parameter['func'])
            && isset($parameter['store_path'])
            && $parameter['store_path'] == 'store/products/options'
        ) {

            switch ($parameter['func']) {
                case 'delete':
//                    $parameter['message'] = StoreChannelsActions::deleteChannel($parameter);
//                    $parameter['func'] = ''; // go to list...
                    echo 'Options DELETE';
                    break;
            }

        }
        return $parameter;
    });

}