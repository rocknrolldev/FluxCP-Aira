<?php
if (!defined('FLUX_ROOT')) exit;

$title = Flux::message('CMSfmenuTitle');
$fmenus	= Flux::config('FluxTables.CMSSettingsTable');
$sql = "SELECT id, option_set, option_body, modified created FROM {$server->loginDatabase}.$fmenus WHERE id IN (16,17,18,19,20,21,22,23,24,25,26,27) ORDER BY id ASC";


$sth = $server->connection->getStatement($sql);
$sth->execute();

$fmenus = $sth->fetchAll();
?>