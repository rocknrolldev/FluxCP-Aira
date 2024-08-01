<?php
if (!defined('FLUX_ROOT')) exit;  
$streamers 	  = Flux::config('FluxTables.CMSStreamersTable');
$id 	  = $params->get('id');
$sql 	  = "SELECT name FROM {$server->loginDatabase}.$streamers WHERE id = ?";
$sth 	  = $server->connection->getStatement($sql);
$sth->execute(array($id));
$streamer	  = $sth->fetch();
$redirect = $auth->actionAllowed('ypanel', 'streamers') ? $this->url('ypanel', 'streamers') : null;

if ($streamer) {
    $sth = $server->connection->getStatement("DELETE FROM {$server->loginDatabase}.$streamers WHERE id = ?");
    $sth->execute(array($id));
	$session->setMessageData(sprintf(Flux::message('CMSConfirmDelete')));
}
else {
	$session->setMessageData(Flux::message('CMSstreamerNotFound'));
}
$this->redirect($redirect);
?>
