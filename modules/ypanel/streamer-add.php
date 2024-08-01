<?php
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$title = Flux::message('CMSstreamerAddTitle');
$streamers 	= Flux::config('FluxTables.CMSStreamersTable');


$name = trim($params->get('streamer_name'));
$charname 	= trim($params->get('streamer_charname'));
$picture	= trim($params->get('streamer_picture'));
$path 	= trim($params->get('streamer_path'));
$platform 	= trim($params->get('streamer_platform'));
$embed	= trim($params->get('streamer_embed'));
$link	= trim($params->get('streamer_link'));
$status	= trim($params->get('streamer_status'));

$tinymce_key = Flux::config('TinyMCEKey'); 
$target_dir = "ragfile/streamer/";
$picture = $target_dir . basename($_FILES["streamer_picture"]["tmp_name"]);
$uploadOk = 1;
$pictureFileType = strtolower(pathinfo($picture,PATHINFO_EXTENSION));


// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
        if($name === '') {
            $errorMessage = Flux::Message('CMSstreamernameError');
		}
        elseif($charname === '') {
            $errorMessage = Flux::Message('CMSstreamercharnameError');
        }
        elseif($picture === '') {
            $errorMessage = Flux::Message('CMSstreamerpictureError');    
        }  
        elseif($path === '') {
            $errorMessage = Flux::Message('CMSstreamerpathError');    
        }           
        elseif($platform === '') {
            $errorMessage = Flux::Message('CMSstreamerplatformError');    
        }                   
        elseif($embed === '') {
            $errorMessage = Flux::Message('CMSstreamerembedError');      
        }                 
    } else {
    if (move_uploaded_file($_FILES["streamer_picture"]["tmp_name"], $picture)) {
                $sql = "INSERT INTO {$server->loginDatabase}.$streamers (name, charname, picture, path, platform, embed, link, status, modified)";
        $sql .= "VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $sth = $server->connection->getStatement($sql);
        $sth->execute(array($name, $charname, $picture, $path, $platform, $embed, $link, $status)); 
		
        $session->setMessageData(Flux::message('CMSStreamerAdded'));
    if ($auth->actionAllowed('ypanel', 'streamers')) {
            $this->redirect($this->url('ypanel', 'streamers'));
  } else {
    echo "Sorry, there was an error uploading your file.";
    }
  }
}
?>

