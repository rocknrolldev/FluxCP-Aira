<?php
if (!defined('FLUX_ROOT')) exit;

$title = Flux::message('CMSsmenuTitle');
$smenus	= Flux::config('FluxTables.CMSSettingsTable');
$sql = "SELECT id, option_set, option_body, modified created FROM {$server->loginDatabase}.$smenus WHERE id IN (28,29,30,31,32,33) ORDER BY id ASC";


$sth = $server->connection->getStatement($sql);
$sth->execute();

$smenus = $sth->fetchAll();
?>