<?php
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$title = Flux::message('CMSServerinfoEditTitle');

$serverinfos	= Flux::config('FluxTables.CMSSettingsTable'); 
$id 	= $params->get('id');
$sql 	= "SELECT id, option_set, option_body, modified FROM {$server->loginDatabase}.$serverinfos WHERE id = ?";
$sth 	= $server->connection->getStatement($sql);
$sth->execute(array($id));
$serverinfo 	= $sth->fetch();

$tinymce_key = Flux::config('TinyMCEKey'); 

if($serverinfo) {
	$option_set	= $serverinfo->option_set;
	$option_body	= $serverinfo->option_body;
    
    if(count($_POST)) {
        $option_set = trim($params->get('serverinfo_title'));
		$option_body 	= trim($params->get('serverinfo_body'));
        
        if($option_set === '') {
            $errorMessage = Flux::Message('CMSserverinfoTitleError');
		}
        elseif($link === '') {
            $errorMessage = Flux::Message('CMSserverinfoLinkError');
        }                                          
        else {
            $sql  = "UPDATE {$server->loginDatabase}.$serverinfos SET option_set = ?, option_body = ?, modified = NOW() WHERE id = ?";            
            $sth = $server->connection->getStatement($sql);
            $sth->execute(array($option_set, $option_body, $id)); 
			
            $session->setMessageData(Flux::message('CMSServerinfoUpdated'));
            if ($auth->actionAllowed('ypanel', 'server')) {
                $this->redirect($this->url('ypanel', 'server'));
            }
            else {
                $this->redirect();
            }      
        }
    }
}
?>
