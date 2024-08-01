<?php
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$title = Flux::message('CMSnpcAddTitle');

$npcs	= Flux::config('FluxTables.CMSNpcsTable'); 
$title	= trim($params->get('npc_title'));
$path	= trim($params->get('npc_path'));
$poster	= trim($params->get('fileToUpload'));
$category	= trim($params->get('npc_category'));
$body	= trim($params->get('npc_body'));

$tinymce_key = Flux::config('TinyMCEKey'); 
$target_dir = "ragfile/npc/";
$poster = $target_dir . basename($_FILES["fileToUpload"]["tmp_name"]);
$uploadOk = 1;
$posterFileType = strtolower(pathinfo($poster,PATHINFO_EXTENSION));


// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
       if($title === '') {
            $errorMessage = Flux::Message('CMSnpcTitleError');
		}
        elseif($path === '') {
            $errorMessage = Flux::Message('CMSnpcPathError');
        }
        elseif($body === '') {
            $errorMessage = Flux::Message('CMSnpcBodyError');    
        }  
        elseif($poster === '') {
            $errorMessage = Flux::Message('CMSnpcImageError');    
        }                 
    } else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $poster)) {
                $sql = "INSERT INTO {$server->loginDatabase}.$npcs (title, path, poster, category, body, modified)";
        $sql .= "VALUES (?, ?, ?, ?, ?, NOW())";
        $sth = $server->connection->getStatement($sql);
        $sth->execute(array($title, $path, $poster, $category, $body)); 
		
        $session->setMessageData(Flux::message('CMSnpcAdded'));
    if ($auth->actionAllowed('ypanel', 'npc')) {
            $this->redirect($this->url('ypanel','npc'));
  } else {
    echo "Sorry, there was an error uploading your file.";
    }
  }
}
?>