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


//$sql = rex_sql::factory();
//$sql->execute(
//
//);