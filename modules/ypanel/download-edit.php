<?php
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$title = Flux::message('CMSDownlaodEditTitle');

$downloads	= Flux::config('FluxTables.CMSDownloadsTable'); 
$id 	= $params->get('id');
$sql 	= "SELECT id, title, link, modified FROM {$server->loginDatabase}.$downloads WHERE id = ?";
$sth 	= $server->connection->getStatement($sql);
$sth->execute(array($id));
$download 	= $sth->fetch();

$tinymce_key = Flux::config('TinyMCEKey'); 

if($download) {
	$title	= $download->title;
	$link	= $download->link;
    
    if(count($_POST)) {
        $title = trim($params->get('download_title'));
		$link 	= trim($params->get('download_link'));
        
        if($title === '') {
            $errorMessage = Flux::Message('CMSDownloadTitleError');
		}
        elseif($link === '') {
            $errorMessage = Flux::Message('CMSDownloadLinkError');
        }                                          
        else {
            $sql  = "UPDATE {$server->loginDatabase}.$downloads SET title = ?, link = ?, modified = NOW() WHERE id = ?";            
            $sth = $server->connection->getStatement($sql);
            $sth->execute(array($title, $link, $id)); 
			
            $session->setMessageData(Flux::message('CMSDownlaodUpdated'));
            if ($auth->actionAllowed('ypanel', 'download')) {
                $this->redirect($this->url('ypanel', 'download'));
            }
            else {
                $this->redirect();
            }      
        }
    }
}
?>
