<?php
if (!defined('FLUX_ROOT')) exit;
$title	= Flux::message('CMSblogHeader');
$blogs	= Flux::config('FluxTables.CMSBlogsTable');

$sql = "SELECT id, title, path, modified, category FROM {$server->loginDatabase}.$blogs ORDER BY id DESC";
$sth = $server->connection->getStatement($sql);
$sth->execute();

$blogs = $sth->fetchAll();
?>
