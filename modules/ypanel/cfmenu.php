<?php
if (!defined('FLUX_ROOT')) exit;

$cfmenus	= Flux::config('FluxTables.CMSSettingsTable');
$sql = "SELECT id, option_set, option_body, modified created FROM {$server->loginDatabase}.$cfmenus WHERE id IN (34,35,36,37,38) ORDER BY id ASC";


$sth = $server->connection->getStatement($sql);
$sth->execute();

$cfmenus = $sth->fetchAll();
?>