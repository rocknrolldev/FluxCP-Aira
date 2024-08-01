
<?php
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$title = Flux::message('CMSquestshopEditTitle');
$questshops 	= Flux::config('FluxTables.CMSQuestShopsTable');

$id 	= $params->get('id');
$sql 	= "SELECT id, title, path, image, position, body, modified FROM {$server->loginDatabase}.$questshops WHERE id = ?";
$sth 	= $server->connection->getStatement($sql);
$sth->execute(array($id));
$questshop 	= $sth->fetch();

$tinymce_key = Flux::config('TinyMCEKey'); 

if($questshop) {
	$title	= $questshop->title;
	$path	= $questshop->path;
	$image	= $questshop->image;
	$position	= $questshop->position;
	$body	= $questshop->body;
    
    if(count($_POST)) {
        $title = trim($params->get('questshop_title'));
		$path 	= trim($params->get('questshop_path'));
		$position	= trim($params->get('questshop_position'));
        $image 	= trim($params->get('fileToUpload'));
        $body 	= trim($params->get('questshop_body'));
        
        if($title === '') {
            $errorMessage = Flux::Message('CMSquestshopTitleError');
		}
        elseif($path === '') {
            $errorMessage = Flux::Message('CMSquestshopPathError');
        }
        elseif($body === '') {
            $errorMessage = Flux::Message('CMSquestshopBodyError');    
        }  
        elseif($image === '') {
            $errorMessage = Flux::Message('CMSquestshopImageError');    
        }                                                  
        else {  
            $sql  = "UPDATE {$server->loginDatabase}.$questshops SET title = ?, path = ?, image = ?, position = ?, body = ?, modified = NOW() WHERE id = ?";            
            $sth = $server->connection->getStatement($sql);
            $sth->execute(array($title, $path, $image, $position, $body, $id)); 
			
            $session->setMessageData(Flux::message('CMSquestshopUpdated'));
            if ($auth->actionAllowed('ypanel', 'questshops')) {
                $this->redirect($this->url('ypanel','questshops'));
            }else {
                $this->redirect();
            }
        }
    }
}
?>