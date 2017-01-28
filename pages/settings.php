<?php

// config
$config = rex_post('config', array(
    array('colors', 'string'),
    array('datepicker', 'int'),
    array('customfield_check', 'int'),
    array('editor', 'int'),
    array('submit', 'boolean')
));

// init form
$form = '';

// if submit set config
if ($config['submit']) {
    // show is saved field
    $this->setConfig('customfield_check', $config['customfield_check']);
    $this->setConfig('editor', $config['editor']);

    // add ever all editor sets
    // MCalendarEditorHelper::addEditorSets();

    $form .= rex_view::info(rex_i18n::msg('shop_config_saved'));
}

// open form
$form .= '
    <form action="' . rex_url::currentBackendPage() . '" method="post">
        <fieldset>
';

// begin textarea
// set arrays
$formElements = array();
$elements = array();
$elements['label'] = '<label for="color-config">' . rex_i18n::msg('shop_category_colors') . '</label>';
$elements['field'] = '<textarea id="color-config" class="form-control" rows="3" name="config[colors]">' . $this->getConfig('colors') . '</textarea>';
$formElements[] = $elements;
// parse select element
$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$form .= $fragment->parse('core/form/form.php');
// end textarea


// begin select field
// label
$formElements = array();
$elements = array();
$elements['label'] = '<label for="customfield_check">' . rex_i18n::msg('shop_customfield_check') . '</label>';
// create select
$select = new rex_select;
$select->setId('customfield_check');
$select->setSize(1);
$select->setAttribute('class', 'form-control');
$select->setName('config[customfield_check]');
// add options
$select->addOption(rex_i18n::msg('shop_not_check_customfield_changed'), 0);
$select->addOption(rex_i18n::msg('shop_check_customfield_changed'), 1);
$select->setSelected($this->getConfig('customfield_check'));
$elements['field'] = $select->get();
$formElements[] = $elements;
// parse select element
$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$form .= $fragment->parse('core/form/form.php');
// end select field


// TODO more options

// begin select field
// label
$formElements = array();
$elements = array();
$elements['label'] = '<label for="editor">' . rex_i18n::msg('shop_editor') . '</label>';
// create select
$select = new rex_select;
$select->setId('editor');
$select->setSize(1);
$select->setAttribute('class', 'form-control');
$select->setName('config[editor]');
// add options
$select->addOption(rex_i18n::msg('shop_redactor2'), 3);
//$select->addOption(rex_i18n::msg('shop_markitup_textile'), 2);
//$select->addOption(rex_i18n::msg('markitup_markdown'), 1);
//$select->addOption(rex_i18n::msg('none_editor'), 0);
$select->setSelected($this->getConfig('editor'));
$elements['field'] = $select->get();
$formElements[] = $elements;
// parse select element
$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$form .= $fragment->parse('core/form/form.php');
#



$form .= '
        </fieldset>
        <fieldset class="rex-form-action">
';

// create submit button
$formElements = array();
$elements = array();
$elements['field'] = '
  <input type="submit" class="btn btn-save rex-form-aligned" name="config[submit]" value="' . rex_i18n::msg('shop_config_save') . '" ' . rex::getAccesskey(rex_i18n::msg('shop_config_save'), 'save') . ' />
';
$formElements[] = $elements;

// parse submit element
$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$form .= $fragment->parse('core/form/submit.php');

// close form
$form .= '
    </fieldset>
  </form>
';

$fragment = new rex_fragment();
$fragment->setVar('class', 'edit');
$fragment->setVar('title', rex_i18n::msg('shop_config'));
$fragment->setVar('body', $form, false);
echo $fragment->parse('core/page/section.php');
