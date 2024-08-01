<?php
if (!defined('FLUX_ROOT')) exit;  
$downloads 	  = Flux::config('FluxTables.CMSDownloadsTable');
$id 	  = $params->get('id');
$sql 	  = "SELECT title FROM {$server->loginDatabase}.$downloads WHERE id = ?";
$sth 	  = $server->connection->getStatement($sql);
$sth->execute(array($id));
$download	  = $sth->fetch();
$redirect = $auth->actionAllowed('ypanel', 'download') ? $this->url('ypanel', 'download') : null;

if ($download) {
    $sth = $server->connection->getStatement("DELETE FROM {$server->loginDatabase}.$downloads WHERE id = ?");
    $sth->execute(array($id));
	$session->setMessageData(sprintf(Flux::message('CMSConfirmDelete')));
}
else {
	$session->setMessageData(Flux::message('CMSblogNotFound'));
}
$this->redirect($redirect);
?>
