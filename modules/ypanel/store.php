<?php
if (!defined('FLUX_ROOT')) exit;
$title	= Flux::message('CMScashshopTitle');
$cashshops	= Flux::config('FluxTables.CMSCashShopsTable');

$sql = "SELECT id, title, position, price, currency FROM {$server->loginDatabase}.$cashshops ORDER BY id DESC";
$sth = $server->connection->getStatement($sql);
$sth->execute();

$cashshops = $sth->fetchAll();
?>
