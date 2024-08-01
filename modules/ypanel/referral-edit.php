<?php
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$title = Flux::message('CMSreferralEditTitle');
$referrals 	= Flux::config('FluxTables.CMSReferralsTable');

$id 	= $params->get('id');
$sql 	= "SELECT id, name, idtrans, amount, image, status, modified FROM {$server->loginDatabase}.$referrals WHERE id = ?";
$sth 	= $server->connection->getStatement($sql);
$sth->execute(array($id));
$referral 	= $sth->fetch();

$tinymce_key = Flux::config('TinyMCEKey'); 

if($referral) {
    $name   = $referral->name;
    $idtrans  = $referral->idtrans;
    $amount   = $referral->amount;
	$image	= $referral->image;
	$status	= $referral->status;
    
    if(count($_POST)) {
        $name = trim($params->get('referrals_name'));
        $idtrans = trim($params->get('referrals_idtrans'));
        $amount = trim($params->get('referrals_amount'));
        $image = trim($params->get('referrals_image'));
		$status 	= trim($params->get('referrals_status'));
        
        if($status === '') {
            $errorMessage = Flux::Message('CMSreferralsTitleError');
		}
        elseif($image === '') {
            $errorMessage = Flux::Message('CMSrefferalsImageError');    
        }                                                  
        else {  
            $sql  = "UPDATE {$server->loginDatabase}.$referrals SET name = ?, idtrans = ?, amount = ?, image = ?, status = ?, modified = NOW() WHERE id = ?";            
            $sth = $server->connection->getStatement($sql);
            $sth->execute(array($name, $idtrans, $amount, $image, $status, $id)); 
			
            $session->setMessageData(Flux::message('CMSreferralsUpdated'));
            if ($auth->actionAllowed('ypanel', 'referrals')) {
                $this->redirect($this->url('ypanel','referrals'));
            }else {
                $this->redirect();
            }
        }
    }
}
?>