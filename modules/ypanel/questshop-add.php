<?php
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$title = Flux::message('CMSquestshopAddTitle');
$questshops 	= Flux::config('FluxTables.CMSQuestShopsTable');

$title = trim($params->get('questshop_title'));
$path 	= trim($params->get('questshop_path'));
$position	= trim($params->get('questshop_position'));
$image 	= trim($params->get('fileToUpload'));
$body 	= trim($params->get('questshop_body'));
$target_dir = "uploads/questshop/";

$tinymce_key = Flux::config('TinyMCEKey'); 
$target_dir = "ragfile/questshop/";
$image = $target_dir . basename($_FILES["fileToUpload"]["tmp_name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($image,PATHINFO_EXTENSION));


// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
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
    } else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $image)) {
                $sql = "INSERT INTO {$server->loginDatabase}.$questshops (title, path, image, position, body, modified)";
        $sql .= "VALUES (?, ?, ?, ?, ?, NOW())";
        $sth = $server->connection->getStatement($sql);
        $sth->execute(array($title, $path, $image, $position, $body)); 
		
        $session->setMessageData(Flux::message('CMSQuestShopsAdded'));
    if ($auth->actionAllowed('ypanel', 'questshops')) {
            $this->redirect($this->url('ypanel', 'questshops'));
  } else {
    echo "Sorry, there was an error uploading your file.";
    }
  }
}
?>

