<?php
if (!defined('FLUX_ROOT')) exit;  
$questshops 	  = Flux::config('FluxTables.CMSQuestShopsTable');
$id 	  = $params->get('id');
$sql 	  = "SELECT title FROM {$server->loginDatabase}.$questshops WHERE id = ?";
$sth 	  = $server->connection->getStatement($sql);
$sth->execute(array($id));
$questshop	  = $sth->fetch();
$redirect = $auth->actionAllowed('ypanel', 'questshops') ? $this->url('ypanel', 'questshops') : null;

if ($questshop) {
    $sth = $server->connection->getStatement("DELETE FROM {$server->loginDatabase}.$questshops WHERE id = ?");
    $sth->execute(array($id));
	$session->setMessageData(sprintf(Flux::message('CMSquestshopDeleted')));
}
else {
	$session->setMessageData(Flux::message('CMSquestshopNotFound'));
}
$this->redirect($redirect);
?>
