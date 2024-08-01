<?php
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$title = Flux::message('CMSfmenuEditTitle');

$fmenus	= Flux::config('FluxTables.CMSSettingsTable'); 
$id 	= $params->get('id');
$sql 	= "SELECT id, option_set, option_body, modified FROM {$server->loginDatabase}.$fmenus WHERE id = ?";
$sth 	= $server->connection->getStatement($sql);
$sth->execute(array($id));
$fmenu 	= $sth->fetch();

$tinymce_key = Flux::config('TinyMCEKey'); 

if($fmenu) {
	$option_set	= $fmenu->option_set;
	$option_body	= $fmenu->option_body;
    
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
            $sql  = "UPDATE {$server->loginDatabase}.$fmenus SET option_set = ?, option_body = ?, modified = NOW() WHERE id = ?";            
            $sth = $server->connection->getStatement($sql);
            $sth->execute(array($option_set, $option_body, $id)); 
			
            $session->setMessageData(Flux::message('CMSfmenuUpdated'));
            if ($auth->actionAllowed('ypanel', 'fmenu')) {
                $this->redirect($this->url('ypanel', 'fmenu'));
            }
            else {
                $this->redirect();
            }      
        }
    }
}
?>
