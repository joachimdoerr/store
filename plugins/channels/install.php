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
StoreEvent::dispatch('store_plugin_install', new StorePluginActionEvent($this, $this->getAddon()));


$sql = rex_sql::factory();
$sql->execute(

    

);