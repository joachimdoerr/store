<?php

/**
 * User: joachimdoerr
 * Date: 26.01.17
 * Time: 17:38
 */
class StoreChannelFormHelper
{
    /**
     * @param rex_form $form
     * @param array $item
     * @param null $id
     * @author Joachim Doerr
     */
    public static function addCategoryElement(rex_form $form, array $item, $id = null)
    {
//        print_r($item);

        if (rex_request::get('func', 'string', '') == 'edit') {

            $sql = rex_sql::factory();
            $sql->setQuery("SELECT * FROM rex_store_channels AS sh WHERE id = $id");
            $channel = $sql->getRow();

            if (!empty($channel['sh.category'])) {
                // add delete button
                $formElements = array(
                    array(
                        'label' => AlfredHelper::getLabel($item),
                        'field' => '
                            <a class="btn btn-delete" href="' . $form->getUrl(array('sub_func' => 'delete_cat')) . '">
                                <i class="rex-icon fa-trash-o"></i> ' . rex_i18n::msg('store_category_removecat') . '
                            </a>
                            <a class="btn btn-info" href="' . $form->getUrl(array('sub_func' => 'setcat')) . '">
                                <i class="rex-icon fa-folder"></i> ' . rex_i18n::msg('store_category_setcat_goto') . '
                            </a>',
                        # 'note' => 'lalala'
                    )
                );
            } else {
                $formElements = array(
                    array(
                        'label' => AlfredHelper::getLabel($item),
                        'field' => '
                            <a class="btn btn-apply" href="' . $form->getUrl(array('sub_func' => 'add_cat')) . '">
                                <i class="rex-icon fa-folder-o"></i> ' . rex_i18n::msg('store_category_setcat') . '
                            </a>',
                        # 'note' => 'lalala'
                    )
                );

            }

            $fragment = new rex_fragment();
            $fragment->setVar('elements', $formElements, false);
            $form->addRawField($fragment->parse('core/form/form.php'));
        }
    }
}