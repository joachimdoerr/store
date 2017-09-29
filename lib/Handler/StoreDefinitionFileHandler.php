<?php
/**
 * @package store
 * @author Joachim Doerr
 * @copyright (C) mail@doerr-softwaredevelopment.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Class StoreDefinitionFileHandler
 *
 * the definition file handler read all files from the definition list item and merge it to one definition array
 *
 * // create definition for definition list item
 * $definition = StoreDefinitionFileHandler::createDefinition($definitionItem);
 */
class StoreDefinitionFileHandler
{
    /**
     * @param DefinitionItem $definitionItem
     * @return DefinitionItem
     * @author Joachim Doerr
     */
    public static function createDefinition(DefinitionItem $definitionItem)
    {
        $definitions = array();
        $definition = null;
        $yml = new \Symfony\Component\Yaml\Yaml();

        if (sizeof($definitionItem->getYmlFiles()) > 0) {
            // read definition files
            foreach ($definitionItem->getYmlFiles() as $key => $file) {
                $definitions[] = $yml->parse(file_get_contents($file));
            }

            // merge not nav definitions to array
            switch($definitionItem->getType()) {
                case 'nav':
                    $definition = call_user_func_array('array_replace_recursive', $definitions);
                    break;
                default:
                case 'yform':
                    // merge it
                    $definition = call_user_func_array('array_merge_recursive', call_user_func_array('array_merge', $definitions));
                    // lang and panel
                    if (is_array($definition)) {
                        foreach ($definition as $key => $value) {
                            if (strpos($key, 'lang') !== false ||
                                strpos($key, 'panel') !== false
                            ) {
                                // merge it
                                $definition[$key] = call_user_func_array('array_merge_recursive', $value);
                                // panel in lang
                                if (is_array($definition[$key])) {
                                    foreach ($definition[$key] as $k => $v) {
                                        if (strpos($k, 'panel') !== false) {
                                            // merge it
                                            $definition[$key][$k] = call_user_func_array('array_merge_recursive', $v);

                                            // sort fields
                                            foreach ($definition[$key][$k] as $kv => $vv) {
                                                if (strpos($kv, 'fields') !== false) {
                                                    // sort it
                                                    StoreHelper::arraySortByColumn($definition[$key][$k][$kv], 'prio');
                                                }
                                            }
                                        } else if (strpos($k, 'field') !== false) {
                                            if (is_array($v)) {
                                                foreach ($v as $kk => $vk) {
                                                    if (array_key_exists('field_prio', $vk)) {
                                                        $definition[$key][$k]['field_prio'] = $vk['field_prio'];
                                                        unset($definition[$key][$k][$kk]);
                                                    }
                                                }
                                                // sort it
                                                StoreHelper::arraySortByColumn($definition[$key][$k], 'prio');
                                            }
                                        }
                                    }
                                }
                            }
                            if (strpos($key, 'fields') !== false) {
                                if (is_array($value)) {
                                    // sort it
                                    foreach ($value as $k => $v) {
                                        if (array_key_exists('field_prio', $v)) {
                                            $definition[$key]['field_prio'] = $v['field_prio'];
                                            unset($definition[$key][$k]);
                                        }
                                    }
                                    // sort it
                                    StoreHelper::arraySortByColumn($definition[$key], 'prio');
                                }
                            }
                            // sort it
                            # StoreHelper::arraySortByColumn($definition[$key], 'field_prio');
                        }
                        // sort it
                        StoreHelper::arraySortByColumn($definition, 'field_prio');
                        break;
                    }
            }
            // add definition top object
            $definitionItem->setDefinitions($definition);
        }
        return $definitionItem;
    }

    /**
     * @param DefinitionItem $definitionItem
     * @author Joachim Doerr
     */
    public static function writeDefinitionJson(DefinitionItem $definitionItem)
    {
        // create path
        if (!is_dir(pathinfo($definitionItem->getPayload('data_path'), PATHINFO_DIRNAME))) {
            mkdir(pathinfo($definitionItem->getPayload('data_path'), PATHINFO_DIRNAME), 0777, true);
        }
        // put file
        file_put_contents($definitionItem->getPayload('data_path'), json_encode($definitionItem->getDefinitions()));
    }

    /**
     * @param DefinitionItem $definitionItem
     * @param bool $assoc
     * @return mixed|null
     * @author Joachim Doerr
     */
    public static function readDefinitionJson(DefinitionItem $definitionItem, $assoc = false)
    {
        if (file_exists($definitionItem->getPayload('data_path'))) {
            return json_decode(file_get_contents($definitionItem->getPayload('data_path')), $assoc);
        }
        return null;
    }

    /**
     * @param DefinitionItem $definitionItem
     * @author Joachim Doerr
     */
    public static function removeDefinitionJson(DefinitionItem $definitionItem)
    {
        unlink($definitionItem->getPayload('data_path'));
    }
}