<?php
if (!defined('FLUX_ROOT')) exit;
$title	= Flux::message('CMSnpcTitle');
$npcs	= Flux::config('FluxTables.CMSNpcsTable');

$sql = "SELECT id, title, path, modified, category FROM {$server->loginDatabase}.$npcs ORDER BY id DESC";
$sth = $server->connection->getStatement($sql);
$sth->execute();

$npcs = $sth->fetchAll();
?>
