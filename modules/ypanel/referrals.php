<?php
if (!defined('FLUX_ROOT')) exit;

$title	= Flux::message('CMSreferralsTitle');
$referrals	= Flux::config('FluxTables.CMSReferralsTable');
$sql = "SELECT id, name, idtrans, amount, image, status FROM {$server->loginDatabase}.$referrals ORDER BY id ASC";


$sth = $server->connection->getStatement($sql);
$sth->execute();

$referrals = $sth->fetchAll();
?>
