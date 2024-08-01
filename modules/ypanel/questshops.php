<?php
if (!defined('FLUX_ROOT')) exit;
$title	= Flux::message('CMSquestshopTitle');
$questshops	= Flux::config('FluxTables.CMSQuestShopsTable');

$sql = "SELECT id, title, position FROM {$server->loginDatabase}.$questshops ORDER BY id DESC";
$sth = $server->connection->getStatement($sql);
$sth->execute();

$questshops = $sth->fetchAll();
?>
