<?php

// create database
//$databaseManager = new StoreDatabaseManager($this->getName(),  "*/test_categories.yml", "yform");
//$databaseManager->executeCustomTablesHandling();

// yform test db manager table creation etc...  todo remove
//    // init manager
//    $dm = new StoreDefinitionManager("store", "*/test_categories.yml", "yform");
////    $dm = new StoreDefinitionManager("store", "*/form/products.yml", "form");
////    $dm = new StoreDefinitionManager("store", "*/nav/navigation.yml", "nav");
////    $dm = new StoreDefinitionManager("store", "*/list/products.yml", "list");
//    // create definition list
//    $dm->createDefinition(); // read cache or regenerate...
//    // get the definition list
////    echo '<pre>';
//
////    foreach ($dm->getDefinition() as $definitionItem) {
////        if ($definitionItem->getName() == 'countries') {
////            print_r($definitionItem);
////        }
////    }
////    print_r($dm->getDefinition());
////die;
//
//    foreach ($dm->getDefinition() as $definitionItem) {
//        rex_yform_manager_table_api::setTable(array('table_name' => $definitionItem->getPayload('table'), 'hidden' => 1, 'status' => 1));
//
//        $fieldSet = array();
//
//        foreach ($definitionItem->getDefinitions() as $key => $definition) {
//            switch (TRUE) {
//                case (strpos($key, 'lang') !== false):
//                    $fieldSet = array_merge($fieldSet, StoreYManagerHandler::handleLangDatabaseFieldset($definition, $definitionItem->getPayload('table')));
//                    break;
//                case (strpos($key, 'fields') !== false):
//                    $fieldSet = array_merge($fieldSet, StoreYManagerHandler::handleDatabaseFieldset($definition, $definitionItem->getPayload('table')));
//                    break;
//            }
//        }
//
//        $table = rex_yform_manager_table_api::setTable(array('table_name' => $definitionItem->getPayload('table')), $fieldSet);
//    }
