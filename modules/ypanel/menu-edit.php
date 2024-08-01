<?php
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$title = Flux::message('CMSmenuEditTitle');

$mmenus	= Flux::config('FluxTables.CMSSettingsTable'); 
$id 	= $params->get('id');
$sql 	= "SELECT id, option_set, option_body, modified FROM {$server->loginDatabase}.$mmenus WHERE id = ?";
$sth 	= $server->connection->getStatement($sql);
$sth->execute(array($id));
$mmenu 	= $sth->fetch();

$tinymce_key = Flux::config('TinyMCEKey'); 

if($mmenu) {
	$option_set	= $mmenu->option_set;
	$option_body	= $mmenu->option_body;
    
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
            $sql  = "UPDATE {$server->loginDatabase}.$mmenus SET option_set = ?, option_body = ?, modified = NOW() WHERE id = ?";            
            $sth = $server->connection->getStatement($sql);
            $sth->execute(array($option_set, $option_body, $id)); 
			
            $session->setMessageData(Flux::message('CMSmenuUpdated'));
            if ($auth->actionAllowed('ypanel', 'menu')) {
                $this->redirect($this->url('ypanel', 'menu'));
            }
            else {
                $this->redirect();
            }      
        }
    }
}
?>
