<?php
if (!defined('FLUX_ROOT')) exit;  
$sfeature 	  = Flux::config('FluxTables.CMSFeaturesTable');
$id 	  = $params->get('id');
$sql 	  = "SELECT title FROM {$server->loginDatabase}.$sfeature WHERE id = ?";
$sth 	  = $server->connection->getStatement($sql);
$sth->execute(array($id));
$feature	  = $sth->fetch();
$redirect = $auth->actionAllowed('ypanel', 'feature') ? $this->url('ypanel', 'feature') : null;

if ($feature) {
    $sth = $server->connection->getStatement("DELETE FROM {$server->loginDatabase}.$sfeature WHERE id = ?");
    $sth->execute(array($id));
	$session->setMessageData(sprintf(Flux::message('CMSfeatureDeleted')));
}
else {
	$session->setMessageData(Flux::message('CMSfeatureNotFound'));
}
$this->redirect($redirect);
?>
