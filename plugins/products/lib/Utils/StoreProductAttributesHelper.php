<?php

/**
 * User: joachimdoerr
 * Date: 26.03.17
 * Time: 18:53
 */
class StoreProductAttributesHelper
{
    const ATTRIBUTES_TABLE = 'rex_store_attributes';
    const TEMP_FILE = 'definitions/default/temp/%s.yml';

    /**
     * @param FormView $storeFormView
     * @param array $item
     * @author Joachim Doerr
     * @return array
     */
    public static function getAttributeDefinitions(FormView $storeFormView, array $item)
    {
        $definitions = self::loadDefinitions();

        foreach ($definitions as $definition) {
            $item['mblock_definition'][$definition['code']] = $definition;
        }

        return array('continue' => false, 'item' => $item);
    }

    public static function getAttributeMBlock()
    {

    }

    /**
     * @param $table
     * @param string $tempfile
     * @return array
     * @author Joachim Doerr
     */
    private static function loadDefinitions()
    {
        $sql = rex_sql::factory();
        $sql->setQuery("SELECT * FROM " . self::ATTRIBUTES_TABLE . " ");

        $plugin = rex_plugin::get('store', 'products');
        $definitions = array();

        // save as temp
        foreach ($sql->getArray() as $key => $item) {

            $file = $plugin->getPath(sprintf(self::TEMP_FILE, $item['code']));
            $definitions[] = array_merge(
                array(
                    'path' => $file,
                    'file' => sprintf(self::TEMP_FILE, $item['code']),
                    'callback' => 'StoreProductAttributesHelper::getAttributeMBlock'
                ), $item);
            $create = true;

            if (file_exists($file)) {
                $update = new DateTime($item['updatedate']);
                $create = ($update->getTimestamp() > filectime($file));
            }

            if ($create === true)
                rex_file::put($plugin->getPath(sprintf(self::TEMP_FILE, $item['code'])), $item['definition']) ;
        }
        return $definitions;
    }
}