<?php
/**
 * @package store
 * @author Joachim Doerr
 * @copyright (C) mail@doerr-softwaredevelopment.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class StoreFormView
{
    const SEARCH_SCHEMA = "*/default/%s.yml";

    /**
     * @var string
     */
    public $addonKey;

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
     * @var rex_form|mblock_rex_form
     */
    public $form;

    /**
     * @var string
     */
    public $namePrefix;

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $legend;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @var array
     */
    public $urlParameters;

    /**
     * @var StoreDefinitionManager
     */
    private $definitionManager;

    /**
     * StoreFormView constructor.
     * @param $addonKey
     * @param $searchFile
     * @param string $legend
     * @param int $id
     * @param bool $debug
     * @param array $urlParameters
     * @param bool $initForm
     * @author Joachim Doerr
     */
    public function __construct($addonKey, $searchFile, $legend = '', $id = 0, $debug = false, array $urlParameters = array(), $initForm = true)
    {
        $this->addonKey = $addonKey;
        $this->searchFile = $searchFile;
        $this->definitionManager = new StoreDefinitionManager($this->addonKey, sprintf(self::SEARCH_SCHEMA, $this->searchFile));
        $this->definitionManager->createDefinition();

        $this->id = $id;
        $this->legend = $legend;
        $this->debug = $debug;
        $this->urlParameters = $urlParameters;

        // definition exist.
        if ($this->getDefinitionManager() instanceof StoreDefinitionManager && is_array($this->getDefinitionManager()->getDefinition()) && sizeof($this->getDefinitionManager()->getDefinition()) > 0)
            $this->definition = $this->getDefinitionManager()->getDefinition()[0];

        // check is definition given
        if (is_null($this->getDefinition()->getDefinitions()) && !is_null($this->searchFile))
            rex_logger::logError(002, 'Definitions is null cannot create rex form', StoreFormView::class, 97); // error

        // set items
        if ($this->getDefinitionManager() instanceof StoreDefinitionManager)
            $this->items = $this->getDefinition()->getDefinitions();

        if ($initForm === true)
            $this->initForm();
    }

    /**
     * @author Joachim Doerr
     */
    public function initForm()
    {
        $form_class = (class_exists(mblock_rex_form::class)) ? 'mblock_rex_form' : 'rex_form';

        // init form
        $this->form = $form_class::factory($this->getDefinition()->getPayload('table'), $this->legend, 'id = ' . $this->id, 'post', $this->debug);

        if ($this->id > 0)
            $this->form->addParam('id', $this->id);

        $this->form->setApplyUrl(rex_url::currentBackendPage());
        $this->form->setEditMode(($this->id > 0));
        $this->form->addParam('start', rex_request::request('start', 'int'));

        if (sizeof($this->urlParameters) > 0)
            foreach ($this->urlParameters as $parameter => $value)
                if (is_array($value))
                    $this->form->addParam($parameter['key'], rex_request::request($parameter['key'], $parameter['type'], $parameter['default']));
                else
                    $this->form->addParam($parameter, $value);
    }

    /**
     * @author Joachim Doerr
     */
    public function addFieldElements()
    {
        if (is_array($this->items))
            foreach ($this->items as $key => $item) {

                if (strpos($key, 'lang') !== false)
                    $this->addLangFieldset($item); // it is a langfieldset

                else if (strpos($key, 'fields') !== false)
                    $this->addDefaultFieldset($item); // it is a fieldset

                else if (strpos($key, 'panel') !== false)
                    $this->addPanelFieldset($item); // it is a fieldset
            }
    }

    /**
     * @param array $fieldset
     * @author Joachim Doerr
     */
    public function addLangFieldset(array $fieldset)
    {
        // start lang tabs
        StoreFormHelper::addLangTabs($this->form, 'wrapper', 1);

        // add inner wrapper -> tabs content
        foreach (rex_clang::getAll() as $key => $clang) {
            // open inner wrapper
            StoreFormHelper::addLangTabs($this->form, 'inner_wrapper', $clang->getId(), rex_clang::getCurrentId());

            // add fieldsets to tab content field
            foreach ($fieldset as $value) {
                // is the value a array
                if (is_array($value)) { // yes go on
                    if (array_key_exists('panel_name', $value) && array_key_exists('fields', $value)) // foreach the fieldsets form langfieldset
                        $this->addPanelFieldset($value, $clang->getId()); // panel field
                    else
                        $this->addDefaultFieldset($value, $clang->getId()); // default lang fields
                }
            }
            // close inner wrapper
            StoreFormHelper::closeLangTabs($this->form, 'close_inner_wrapper');
        }
        // close lang tabs
        StoreFormHelper::closelangTabs($this->form, 'close_wrapper');
    }

    /**
     * @param array $panel
     * @param null $clang
     * @author Joachim Doerr
     */
    public function addPanelFieldset(array $panel, $clang = null)
    {
        if (array_key_exists('panel_name', $panel) && array_key_exists('fields', $panel)) {
            // ADD PANEL
            StoreFormHelper::addCollapsePanel($this->form, 'wrapper');
            StoreFormHelper::addCollapsePanel($this->form, 'inner_wrapper', StoreHelper::getLabel($panel));

            // panel lang fields
            $this->addDefaultFieldset($panel['fields'], $clang);

            // close
            StoreFormHelper::closeCollapsePanel($this->form, 'close_inner_wrapper');
            StoreFormHelper::closeCollapsePanel($this->form, 'close_wrapper');
        }
    }

    /**
     * @param array $fieldset
     * @param null $clang
     * @author Joachim Doerr
     */
    public function addDefaultFieldset(array $fieldset, $clang = null)
    {
        // field row
        $fieldRow = false;
        $fieldColumn = false;

//        echo '<pre>';
//        print_r($_POST);
//        echo '</pre>';

        foreach ($fieldset as $item) {
            if (is_array($item) && array_key_exists('field_row', $item) && $item['field_row'] == 'open')
                StoreFormHelper::addColumns($this->form, 'wrapper', $item);

            if (is_array($item) && array_key_exists('field_row', $item) && $item['field_row'] == 'close')
                $fieldRow = true;

            if (is_array($item) && array_key_exists('field_column', $item))
                $fieldColumn = StoreFormHelper::addColumns($this->form, 'column', $item);
        }

        foreach ($fieldset as $item) {
            // break is not a array
            if (!is_array($item))
                continue;

            if ($this->namePrefix)
                $item['name'] = $this->namePrefix . $item['name'];

            if (!isset($item['mblock_callable']) && isset($item['mblock_definition_table']))
                $item['mblock_callable'] = 'StoreMBlockHelper::getMBlockDefinitions';

            if (isset($item['mblock_callable'])) {
                $result = call_user_func_array($item['mblock_callable'], array($this, $item));

                if (isset($result['continue']) && $result['continue'] === true)
                    continue;

                if (isset($result['item']))
                    $item = $result['item'];
            }

            // fix definition for mblock area
            if (isset($item['form_type']) && $item['form_type'] == 'mblock' or isset($item['mblock_definition'])) {

                if (is_array($item['mblock_definition'])) {
                    // more definitions by table or files
                    $this->addMBlockSetFieldset($item);
                    continue;
                }
                // one definition file...
                $this->addMBlockFieldset($item);
                continue;
            }

            // is field a lang field
            if (!is_null($clang) && is_array($item) && array_key_exists('name', $item))
                $item['lang_name'] = $item['name'] . '_' . $clang; // add lang name

            // set element for add more...
            $element = StoreFormHelper::addFormElementByField($this->form, $item, $this->id);

//            // TODO validation...
//            if ($element instanceof rex_form_element) {
//                $validator = $element->getValidator();
//                $validator->add('notempty', 'darf nicht');
//            }


            // set element properties by item array
            StoreFormHelper::setElementProperties($element, $item);
        }

        if ($fieldColumn)
            StoreFormHelper::addColumns($this->form, 'close_column');

        if ($fieldRow)
            StoreFormHelper::addColumns($this->form, 'close_wrapper');
    }

    /**
     * @param array $item
     * @author Joachim Doerr
     */
    private function addMBlockSetFieldset(array $item)
    {
        $active = array();
        // is edit?
        if ($this->id > 0) {
            $row = json_decode($this->form->getSql()->getRow()[$this->form->getTableName() .'.'. $item['name']], true);
            if (is_array($row) && sizeof($row) > 0)
                foreach ($row as $key => $value)
                    $active[] = $key;
        }

        $uid = uniqid();
        // create navigation
        StoreMBlockHelper::addMBlockSetNavigation($this->form, $item, $this->urlParameters, $this->id, $active, $uid);
        // create mblock fieldsets
        StoreMBlockHelper::addMBlockSetFieldset($this, $item, $active, $uid);
    }

    /**
     * @param array $item
     * @param null $type
     * @param array $settings
     * @return rex_form_raw_element
     * @author Joachim Doerr
     */
    public function addMBlockFieldset(array $item, $type = null, $settings = array())
    {
        $settings = array_merge($settings, StoreMBlockHelper::getSettings($item, $type));
        return $this->form->addRawField(self::createMBlockFieldset($item, $type, $settings));
    }

    /**
     * @param array $item
     * @param null $type
     * @param array $settings
     * @return mixed
     * @author Joachim Doerr
     */
    public function createMBlockFieldset(array $item, $type = null, $settings = array())
    {
        $table = $this->getDefinition()->getPayload('table') . '::' . $item['name'];

        if (!is_null($type)) {
            $table = $table . '::' . $type;
            $type = '[' . $type . ']';
        }

        $mblockView = new StoreFormView($this->addonKey, $item['mblock_definition'], '', $this->id, $this->debug, $this->urlParameters, false);
        $mblockView->namePrefix = $item['name'] . ']' . $type . '[0][';
        $mblockView->getDefinition()->setPayload($this->getDefinition()->getPayload());
        $mblockView->initForm();
        $mblockView->addFieldElements(); // add field elements by defaults

        return mblock::show($table, $mblockView->showElements(), array_merge(array('min'=>0), $settings));
    }

    /**
     * @return rex_form
     * @author Joachim Doerr
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @return string
     * @author Joachim Doerr
     */
    public function show()
    {
        return $this->form->get();
    }

    /**
     * @param string $legend
     * @return string
     * @author Joachim Doerr
     */
    public function showElements($legend = '')
    {
        return $this->form->getElements($legend);
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