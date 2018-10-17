<?php
/**
 * @package store
 * @author Joachim Doerr
 * @copyright (C) mail@doerr-softwaredevelopment.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//////////////////////////////
// dispatch install event
rex_extension::registerPoint(new rex_extension_point('STORE_PLUGIN_INSTALL', $this->getName(), array('plugin'=>$this, 'addon'=>$this->getAddon(), 'data_path' => 'resources')));

$sql = rex_sql::factory();
$sql->setQuery("
CREATE FUNCTION category_connect_by_parent_eq_prior_id_with_level(value INT, maxlevel INT) RETURNS INT
NOT DETERMINISTIC
READS SQL DATA
BEGIN
        DECLARE _id INT;
        DECLARE _parent INT;
        DECLARE _next INT;
        DECLARE _i INT;
        DECLARE CONTINUE HANDLER FOR NOT FOUND SET @id = NULL;

        SET _parent = @id;
        SET _id = -1;
        SET _i = 0;

        IF @id IS NULL THEN
                RETURN NULL;
        END IF;

        LOOP
                SELECT  MIN(id)
                INTO    @id
                FROM    rex_store_categories
                WHERE   parent = _parent
                        AND id > _id
                        AND COALESCE(@level < maxlevel, TRUE);
                IF @id IS NOT NULL OR _parent = @start_with THEN
                        SET @level = @level + 1;
                        RETURN @id;
                END IF;
                SET @level := @level - 1;
                SELECT  id, parent
                INTO    _id, _parent
                FROM    rex_store_categories
                WHERE   id = _parent;
                SET _i = _i + 1;
        END LOOP;
        RETURN NULL;
END;

CREATE FUNCTION category_sys_connect_by_path(delimiter TEXT, node INT) RETURNS TEXT
NOT DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE _path TEXT;
    DECLARE _cpath TEXT;
    DECLARE _id INT;
    DECLARE _prio INT;
    DECLARE EXIT HANDLER FOR NOT FOUND RETURN _path;
    SET _id = COALESCE(node, @id);
    SET _path = _id;
    LOOP
              SELECT  parent, prio
              INTO    _id, _prio
              FROM    rex_store_categories
              WHERE   id = _id
              AND COALESCE(id <> @start_with, TRUE);
              SET _path = CONCAT(_id, _prio, delimiter, _path);
  END LOOP;
END;
");