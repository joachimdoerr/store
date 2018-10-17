<?php
/**
 * @package store
 * @author Joachim Doerr
 * @copyright (C) mail@doerr-softwaredevelopment.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


use Basecondition\Database\DatabaseManager;

// copy definitions to addon data path
rex_dir::copy($this->getPath('data'),$this->getDataPath('resources/store/'));

// create database schema
DatabaseManager::provideSchema($this->getName(), $this->getDataPath('resources'));