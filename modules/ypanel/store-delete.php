<?php
if (!defined('FLUX_ROOT')) exit;  
$cashshops 	  = Flux::config('FluxTables.CMSCashShopsTable');
$id 	  = $params->get('id');
$sql 	  = "SELECT title FROM {$server->loginDatabase}.$cashshops WHERE id = ?";
$sth 	  = $server->connection->getStatement($sql);
$sth->execute(array($id));
$cashshop	  = $sth->fetch();
$redirect = $auth->actionAllowed('ypanel', 'store') ? $this->url('ypanel', 'store') : null;

if ($cashshop) {
    $sth = $server->connection->getStatement("DELETE FROM {$server->loginDatabase}.$cashshops WHERE id = ?");
    $sth->execute(array($id));
	$session->setMessageData(sprintf(Flux::message('CMSConfirmDelete')));
}
else {
	$session->setMessageData(Flux::message('CMSstoreNotFound'));
}
$this->redirect($redirect);
?>
