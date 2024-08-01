<?php
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$title = Flux::message('CMSnpcEditTitle');
$npcs 	= Flux::config('FluxTables.CMSNpcsTable');
$id 	= $params->get('id');
$sql 	= "SELECT id, title, path, poster, category, body, modified FROM {$server->loginDatabase}.$npcs WHERE id = ?";
$sth 	= $server->connection->getStatement($sql);
$sth->execute(array($id));
$npc 	= $sth->fetch();

$tinymce_key = Flux::config('TinyMCEKey'); 

if($npc) {
	$title	= $npc->title;
	$path	= $npc->path;
	$category	= $npc->category;
	$poster	= $npc->poster;
	$body	= $npc->body;
    
    if(count($_POST)) {
        $title = trim($params->get('npc_title'));
		$path 	= trim($params->get('npc_path'));
		$category 	= trim($params->get('npc_category'));
        $poster 	= trim($params->get('fileToUpload'));
        $body 	= trim($params->get('npc_body'));

        
        if($title === '') {
            $errorMessage = Flux::Message('CMSnpcTitleError');
		}
        elseif($path === '') {
            $errorMessage = Flux::Message('CMSnpcPathError');
        }
        elseif($body === '') {
            $errorMessage = Flux::Message('CMSnpcBodyError');    
        }  
        elseif($category === '') {
            $errorMessage = Flux::Message('CMSnpccategoryError');    
        }  
        elseif($poster === '') {
            $errorMessage = Flux::Message('CMSnpcposterError');    
        }                                                  
        else {
            $sql  = "UPDATE {$server->loginDatabase}.$npcs SET title = ?, path = ?, poster = ?, category = ?, body = ?, modified = NOW() WHERE id = ?";            
            $sth = $server->connection->getStatement($sql);
            $sth->execute(array($title, $path, $poster, $category, $body, $id)); 
			
            $session->setMessageData(Flux::message('CMSnpcUpdated'));
            if ($auth->actionAllowed('ypanel', 'npc')) {
                $this->redirect($this->url('ypanel','npc'));
            } else {
                $this->redirect();
            }      
        }
    }
}
?>
