<?php

// create database
$databaseManager = new AlfredDatabaseManager($this->getName());
$databaseManager->executeCustomTablesHandling();
