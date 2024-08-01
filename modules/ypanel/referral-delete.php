<?php
if (!defined('FLUX_ROOT')) exit;  
$referrals 	  = Flux::config('FluxTables.CMSReferralsTable');
$id 	  = $params->get('id');
$sql 	  = "SELECT title FROM {$server->loginDatabase}.$referrals WHERE id = ?";
$sth 	  = $server->connection->getStatement($sql);
$sth->execute(array($id));
$referral	  = $sth->fetch();
$redirect = $auth->actionAllowed('ypanel', 'referrals') ? $this->url('ypanel', 'referrals') : null;

if ($referral) {
    $sth = $server->connection->getStatement("DELETE FROM {$server->loginDatabase}.$referrals WHERE id = ?");
    $sth->execute(array($id));
	$session->setMessageData(sprintf(Flux::message('CMSreferralDeleted')));
}
else {
	$session->setMessageData(Flux::message('CMSreferralNotFound'));
}
$this->redirect($redirect);
?>
