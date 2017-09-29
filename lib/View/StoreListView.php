<?php
/**
 * @package store
 * @author Joachim Doerr
 * @copyright (C) mail@doerr-softwaredevelopment.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class StoreListView
{
    const SEARCH_SCHEMA = "*/default/%s.yml";

    /**
     * @var string
     */
    public $addonKey;

    /**
     * @var array
     */
    public $urlParameters = array();

    /**
     * @var array
     */
    public $items;

    /**
     * @var DefinitionItem
     */
    public $definition;

    /**
     * @var string
     */
    public $searchFile;

    /**
     * @var rex_list
     */
    public $list;

    /**
     * @var array
     */
    public $listGroup = array();

    /**
     * @var
     */
    private $query;

    /**
     * @var string
     */
    private $where;

    /**
     * @var string
     */
    private $orderBy;

    /**
     * @var array
     */
    private $selects;

    /**
     * @var int
     */
    private $rows;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @var StoreDefinitionManager
     */
    private $definitionManager;

    /**
     * StoreListView constructor.
     * @param $addonKey
     * @param $searchFile
     * @param int $rows
     * @param bool $debug
     * @param bool $initList
     * @param array $urlParameters
     * @author Joachim Doerr
     */
    public function __construct($addonKey, $searchFile, $rows = 30, $debug = false, $initList = true, array $urlParameters = array())
    {
        $this->urlParameters = $urlParameters;
        $this->addonKey = $addonKey;
        $this->searchFile = $searchFile;
        $this->definitionManager = new StoreDefinitionManager($this->addonKey, sprintf(self::SEARCH_SCHEMA, $this->searchFile));
        $this->definitionManager->createDefinition();
        $this->rows = $rows;
        $this->debug = $debug;

        if (array_key_exists('sort', $urlParameters) && !empty($urlParameters['sort'])) {
            $direction = 'ASC';
            if (array_key_exists('sorttype', $urlParameters)) {
                $direction = $urlParameters['sorttype'];
            }
            $this->createOrderBy($urlParameters['sort'], $direction);
        }

        // definition exist.
        if (is_array($this->getDefinitionManager()->getDefinition()) && sizeof($this->getDefinitionManager()->getDefinition()) > 0) {
            $this->definition = $this->getDefinitionManager()->getDefinition()[0];
        }

        // check is definition given
        if (is_null($this->getDefinition()->getDefinitions())) {
            rex_logger::logError(001, 'Definitions is null cannot create rex list', StoreListView::class, 97); // error
        }

//        if (in_array(strtolower('list_order_by'), array_map('strtolower', array_keys($this->getDefinition()->getDefinitions())))) {
//            echo '<pre>';
//            print_r($this->getDefinition()->getDefinitions());
//            echo '</pre>';
//        }

        // set helper definition var
        foreach ($this->getDefinition()->getDefinitions() as $key => $value) {
            if (strpos($key, 'lang') !== false ||
                strpos($key, 'panel') !== false
            ) {
                $addlang_key = '';
                if (strpos($key, 'lang') !== false)
                    $addlang_key = '_'.rex_clang::getCurrentId();

                // panel in lang
                if (is_array($value)) {
                    foreach ($value as $k => $v) {
                        if (strpos($k, 'panel') !== false) {
                            // get fields
                            foreach ($v as $kv => $vv) {
                                if (strpos($kv, 'fields') !== false) {
                                    // do it
                                    foreach ($vv as $vn)
                                        $this->addItem($vn, $addlang_key);
                                }
                            }
                        } else if (strpos($k, 'field') !== false) {
                            // do it
                            if (is_array($v)) {
                                foreach ($v as $vg)
                                    $this->addItem($vg, $addlang_key);
                            }
                        }
                    }
                }
            }
            if (strpos($key, 'fields') !== false) {
                // do it
                foreach ($value as $ve)
                    $this->addItem($ve);
            }

            if (is_array($value)) {
                foreach ($value as $v) {
                    if (is_array($v) && array_key_exists('list_order_by', $v)) {
                        $this->createOrderBy($v['list_order_by']);
                    }
                }
            }
        }

        foreach ($this->items as $item) {
            if (array_key_exists('list_prio', $item)) {
                StoreHelper::arraySortByColumn($this->items, 'list_prio');
                break;
            }
        }

//        echo '<pre>';
//        print_r($this->items);
//        echo '</pre>';

        // init list
        if ($initList === true)
            $this->createList();
    }

    /**
     * @param $fieldset
     * @param string $addlang_key
     * @return bool
     * @author Joachim Doerr
     */
    public function addItem($fieldset, $addlang_key = '')
    {
        if (!is_array($fieldset)) {
            return false;
        }
        if (!isset($fieldset['list_load'])) {
            $fieldset['list_load'] = 0;
        }
        if (array_key_exists('list_hidden', $fieldset) && $fieldset['list_hidden'] == 1
            xor $fieldset['list_load'] == 1
            or !array_key_exists('name', $fieldset)
        ) {
            return false;
        } else {
            $fieldset['name'] = $fieldset['name'] . $addlang_key;
            $this->items[] = $fieldset;
            return true;
        }
    }

    /**
     * @param null $query
     * @param null $rows
     * @param null $debug
     * @author Joachim Doerr
     */
    public function createList($query = null, $rows = null, $debug = null)
    {
        if (is_null($query)) {
            $query = $this->createQuery();
        }
        if (is_null($rows)) {
            $rows = $this->rows;
        }
        if (is_null($debug)) {
            $debug = $this->debug;
        } else {
            if (!is_bool($debug)) {
                $debug = false;
            }
        }

        $this->list = rex_list::factory($query, $rows, $this->getDefinition()->getName(), $debug);

        if (sizeof($this->urlParameters) > 0) {
            foreach ($this->urlParameters as $parameter => $value) {
                $this->list->addParam($parameter, $value);
            }
        }
    }

    /**
     * @author Joachim Doerr
     */
    public function createWhere()
    {
//        if (count($this->where) > 0) {
//            #$this->where = 'WHERE ' . implode(' ', $this->where);
//        } else {
//            #$this->where = '';
//        }
        return $this->where;
    }

    /**
     * @author Joachim Doerr
     */
    public function createSelect()
    {
        $select = array($this->getDefinition()->getPayload('table_key') . '.id');

        if (is_array($this->items)) {
            foreach ($this->items as $item) {
                if (array_key_exists('no_db', $item) && $item['no_db'] == 1) {
                    continue;
                }
                if (!array_key_exists('callable', $item)) {
                    $select[] = $this->getDefinition()->getPayload('table_key') . '.' . $item['name'];
                } else {
                    if ($item['status'] == 1) {
                        $select[] = $this->getDefinition()->getPayload('table_key') . '.status';
                    }
                }
            }
        }

        $this->selects = $select;
        return $this->selects;
    }

    /**
     * @param $value
     * @param string $direction
     * @return string
     * @author Joachim Doerr
     */
    public function createOrderBy($value, $direction = 'ASC')
    {
        $this->orderBy = ' ORDER BY ' . $value . ' ' . $direction;
        return $this->orderBy;
    }

    /**
     * @return string
     * @author Joachim Doerr
     */
    public function createQuery()
    {
        // TODO add extension point
        // TODO add option for join

        if (empty($this->orderBy)) {
            $this->orderBy = '';
        }
        if (empty($this->where)) {
            $this->createWhere();
        }
        if (empty($this->selects)) {
            $this->createSelect();
        }
        $this->query = 'SELECT ' . implode(', ', $this->selects) . ' FROM ' . $this->getDefinition()->getPayload('table') . ' AS ' . $this->getDefinition()->getPayload('table_key') . ' ' . $this->where . ' ' . $this->orderBy;
        return $this->query;
    }

    /**
     * @author Joachim Doerr
     * @param array $parameter
     * @param string $icon
     */
    public function addIdElement($parameter = array(), $icon = 'fa-file-text-o')
    {
        // Column 1: Action (add/edit button)
        $thIcon = '<a href="' . $this->list->getUrl(['func' => 'add']) . '" title="' . rex_i18n::msg($this->addonKey . '_add_' . $this->getDefinition()->getName()) . '"><i class="rex-icon rex-icon-add-action"></i></a>';
        $tdIcon = '<i class="rex-icon ' . $icon . '"></i>';

        $this->list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
        $this->list->setColumnParams($thIcon, array_merge(['func' => 'edit', 'id' => '###id###', 'start' => rex_request::request('start', 'int', NULL)], $parameter, $this->urlParameters));
        $this->list->removeColumn('id');

        $this->listGroup[] = 40;
    }

    /**
     * @param array $parameter
     * @return $this
     * @author Joachim Doerr
     */
    public function addDefaultElements($parameter = array())
    {
        if (is_array($this->items)) {
            foreach ($this->items as $item) {
                // name list hidden?
                if (!array_key_exists('name', $item) or (array_key_exists('list_hidden', $item) && $item['list_hidden'] == 1)) {
                    continue;
                }

                // default parameter
                $defaultParam = array('id' => '###id###', 'start' => rex_request::request('start', 'int', NULL));

                // label
                StoreListHelper::setLabel($this->list, $item, $item['name'], $this->addonKey . '_' . $this->definition->getName() . '_');

                // functions callable
                if (array_key_exists('list_callable', $item)) {
                    call_user_func_array($item['list_callable'], array(
                        $this->list,
                        $item,
                        array_merge($defaultParam, $parameter, array('addon_key' => $this->addonKey . '_' . $this->definition->getName()))
                    ));

                    // add to list group
                    if (array_key_exists('list_group', $item)) {
                        if (is_array($item['list_group'])) {
                            $this->listGroup = array_merge($this->listGroup, $item['list_group']);
                        } else {
                            $this->listGroup[] = $item['list_group'];
                        }
                    } else {
                        $this->listGroup[] = '*';
                    }
                    continue;
                }
                // format callback
                if (array_key_exists('list_format_callable', $item)) {
                    $callable = explode('::', $item['list_format_callable']);
                    $item['list_editable'] = true;
                    $this->list->setColumnFormat($item['name'], 'custom', array($callable[0], $callable[1]), array("item"=>$item));
                }
                // editable
                if (array_key_exists('list_editable', $item) && $item['list_editable'] == true) {
                    $defaultParam['func'] = (array_key_exists('list_func', $item)) ? $item['list_func'] : 'edit';
                    $param = array_merge($defaultParam, $parameter);
                    $this->list->setColumnParams($item['name'], $param);
                }
                // sortable
                if (array_key_exists('list_sort', $item) && $item['list_sort'] == true) {
                    $this->list->setColumnSortable($item['name']);
                }

                $this->listGroup[] = (array_key_exists('list_group', $item)) ? $item['list_group'] : '*';
            }
        }
        return $this;
    }

    /**
     * @return string
     * @author Joachim Doerr
     */
    public function show()
    {
        $this->list->addTableColumnGroup($this->listGroup);

        // Example
        // extension point
        /*
        rex_extension::registerPoint(new rex_extension_point(
            'SHOP_PRE_LIST_VIEW',
            $this->definitionFile,
            array(
                'list' => $this->list,
                'items' => $this->items,
                'definition_file' => $this->definitionFile,
                'definition' => $this->definition
            )
        ));
        */

        return $this->list->get();
    }

    /**
     * @return DefinitionItem
     * @author Joachim Doerr
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * @return StoreDefinitionManager
     * @author Joachim Doerr
     */
    public function getDefinitionManager()
    {
        return $this->definitionManager;
    }
}