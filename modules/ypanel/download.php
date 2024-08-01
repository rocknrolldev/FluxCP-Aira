<?php
if (!defined('FLUX_ROOT')) exit;
$title	= Flux::message('CMSDownloadTitle');
$downloads	= Flux::config('FluxTables.CMSDownloadsTable');

$sql = "SELECT id, title, link FROM {$server->loginDatabase}.$downloads ORDER BY id DESC";
$sth = $server->connection->getStatement($sql);
$sth->execute();

$downloads = $sth->fetchAll();
?>
