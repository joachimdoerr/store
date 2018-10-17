<?php


class rex_form_categories_prio_element extends rex_form_select_element
{
    private $firstOptionMsg;
    private $optionMsg;

    public function __construct($tag = '', rex_form $table = null, array $attributes = [])
    {
        parent::__construct('', $table, $attributes);
        $this->firstOptionMsg = 'form_field_first_priority';
        $this->optionMsg = 'form_field_after_priority';
        $this->select->setSize(1);
        rex_extension::register('REX_FORM_SAVED', [$this, 'organizePriorities']);
    }

    /**
     * @return string
     * @throws rex_sql_exception
     * @author Joachim Doerr
     */
    public function formatElement()
    {
        $k = 't';
        $channelId = rex_request::get('channel');
        $currentId = rex_request::get('id');
        $currentLevel = null;
        $currentParent = null;
        $name = 'name_' . rex_clang::getCurrentId();

        $query = "
            SELECT  CONCAT(REPEAT('--', level - 1), ' ', $k.$name) AS name,
                    category_sys_connect_by_path('/', $k.id) AS path,
                    parent, level, $k.id
            FROM    (
                    SELECT  category_connect_by_parent_eq_prior_id_with_level(id, 10) AS id,
                            CAST(@level AS SIGNED) AS level
                    FROM    (
                            SELECT  @start_with := {$channelId},
                                    @id := @start_with,
                                    @level := 0
                            ) vars, ".rex::getTablePrefix() . StoreChannelsActions::CATEGORIES_TABLE."
                    WHERE   @id IS NOT NULL
                    ) ho
            JOIN    ".rex::getTablePrefix() . StoreChannelsActions::CATEGORIES_TABLE." $k
            ON      $k.id = ho.id
            ORDER BY path
        ";

        $value = 1;
        $this->select->addOption(rex_i18n::msg($this->firstOptionMsg), $value);

        $sql = rex_sql::factory();
        $result = $sql->getArray($query);


        foreach ($result as $item) {
            if ($item['id'] == $currentId) {
                $currentLevel = $item['level'];
                $currentParent = $item['parent'];
            }
        }

        foreach ($result as $item) {
            $value++;
            $disable = array();
//            if ($item['id'] == $currentId) {
//                continue;
//            }
            if ($currentParent !=  $item['id'] && $this->table->isEditMode()) {
                $disable = (
                    $item['level'] != $currentLevel or
                    $item['parent'] != $currentParent or
                    $currentId == $item['id']
                )
                    ? array('disabled' => 'disabled')
                    : array();
            }

            $this->select->addOption(rex_i18n::rawMsg($this->optionMsg, $item['name']), $value, $item['id'], 0, $disable);
        }

        return parent::formatElement();
    }

    public function organizePriorities(rex_extension_point $ep)
    {
        if ($this->table->equals($ep->getParam('form'))) {
            $name = $this->getFieldName();

            rex_sql_util::organizePriorities(
                $this->table->getTableName(),
                $name,
                '',
                $name . ', updatedate desc'
            );
        }
    }
}