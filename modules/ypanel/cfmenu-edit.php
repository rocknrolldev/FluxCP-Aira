<?php
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$title = Flux::message('CMScfmenuEditTitle');

$cfmenus	= Flux::config('FluxTables.CMSSettingsTable'); 
$id 	= $params->get('id');
$sql 	= "SELECT id, option_set, option_body, modified FROM {$server->loginDatabase}.$cfmenus WHERE id = ?";
$sth 	= $server->connection->getStatement($sql);
$sth->execute(array($id));
$cfmenu 	= $sth->fetch();

$tinymce_key = Flux::config('TinyMCEKey'); 

if($cfmenu) {
	$option_set	= $cfmenu->option_set;
	$option_body	= $cfmenu->option_body;
    
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
            $sql  = "UPDATE {$server->loginDatabase}.$cfmenus SET option_set = ?, option_body = ?, modified = NOW() WHERE id = ?";            
            $sth = $server->connection->getStatement($sql);
            $sth->execute(array($option_set, $option_body, $id)); 
			
            $session->setMessageData(Flux::message('CMScfmenuUpdated'));
            if ($auth->actionAllowed('ypanel', 'cfmenu')) {
                $this->redirect($this->url('ypanel', 'cfmenu'));
            }
            else {
                $this->redirect();
            }      
        }
    }
}
?>
