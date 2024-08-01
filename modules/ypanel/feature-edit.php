<?php
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$title = Flux::message('CMSDownlaodEditTitle');

$sfeature	= Flux::config('FluxTables.CMSDownloadsTable'); 
$id 	= $params->get('id');
$sql 	= "SELECT id, title, ikon, body, modified FROM {$server->loginDatabase}.$sfeature WHERE id = ?";
$sth 	= $server->connection->getStatement($sql);
$sth->execute(array($id));
$download 	= $sth->fetch();

$tinymce_key = Flux::config('TinyMCEKey'); 

if($download) {
	$title	= $download->title;
	$ikon	= $download->ikon;
	$body	= $download->body;
    
    if(count($_POST)) {
        $title = trim($params->get('download_title'));
		$ikon 	= trim($params->get('download_ikon'));
		$body 	= trim($params->get('download_body'));
        
        if($title === '') {
            $errorMessage = Flux::Message('CMSFeatureTitleError');
		}
        elseif($ikon === '') {
            $errorMessage = Flux::Message('CMSFeatureIkonError');
        }     
        elseif($ikon === '') {
            $errorMessage = Flux::Message('CMSFeatureBodyError');
        }                                           
        else {
            $sql  = "UPDATE {$server->loginDatabase}.$sfeature SET title = ?, ikon = ?, body = ?, modified = NOW() WHERE id = ?";            
            $sth = $server->connection->getStatement($sql);
            $sth->execute(array($title, $ikon, $id)); 
			
            $session->setMessageData(Flux::message('CMSFeatureUpdated'));
            if ($auth->actionAllowed('ypanel', 'feature')) {
                $this->redirect($this->url('ypanel', 'feature'));
            }
            else {
                $this->redirect();
            }      
        }
    }
}
?>
