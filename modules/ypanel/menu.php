<?php
if (!defined('FLUX_ROOT')) exit;

$title = Flux::message('CMSmenuTitle');
$menus	= Flux::config('FluxTables.CMSSettingsTable');
$sql = "SELECT id, option_set, option_body, modified created FROM {$server->loginDatabase}.$menus WHERE id IN (2,3,4,5,6,7,8,9,10,11,12,13,14,15) ORDER BY id ASC";


$sth = $server->connection->getStatement($sql);
$sth->execute();

$menus = $sth->fetchAll();
?>