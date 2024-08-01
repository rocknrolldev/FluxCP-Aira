<?php
if (!defined('FLUX_ROOT')) exit;  
$npcs 	  = Flux::config('FluxTables.CMSNpcsTable');
$id 	  = $params->get('id');
$sql 	  = "SELECT title FROM {$server->loginDatabase}.$npcs WHERE id = ?";
$sth 	  = $server->connection->getStatement($sql);
$sth->execute(array($id));
$npc	  = $sth->fetch();
$redirect = $auth->actionAllowed('ypanel', 'npc') ? $this->url('ypanel', 'npc') : null;

if ($npc) {
    $sth = $server->connection->getStatement("DELETE FROM {$server->loginDatabase}.$npcs WHERE id = ?");
    $sth->execute(array($id));
	$session->setMessageData(sprintf(Flux::message('CMSnpcDeleted')));
}
else {
	$session->setMessageData(Flux::message('CMSnpcNotFound'));
}
$this->redirect($redirect);
?>
