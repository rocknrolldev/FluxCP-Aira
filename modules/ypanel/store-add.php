<?php
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$title = Flux::message('CMScashshopAddTitle');
$cashshops 	= Flux::config('FluxTables.CMSCashShopsTable');

$title = trim($params->get('cashshop_title'));
$path 	= trim($params->get('cashshop_path'));
$position	= trim($params->get('cashshop_position'));
$price	= trim($params->get('cashshop_price'));
$currency	= trim($params->get('cashshop_currency'));
$image 	= trim($params->get('fileToUpload'));
$body 	= trim($params->get('cashshop_body'));
$target_dir = "uploads/cashshop/";

$tinymce_key = Flux::config('TinyMCEKey'); 
$target_dir = "ragfile/cashshop/";
$image = $target_dir . basename($_FILES["fileToUpload"]["tmp_name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($image,PATHINFO_EXTENSION));


// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
        if($title === '') {
            $errorMessage = Flux::Message('CMScashshopTitleError');
		}
        elseif($path === '') {
            $errorMessage = Flux::Message('CMScashshopPathError');
        }
        elseif($body === '') {
            $errorMessage = Flux::Message('CMScashshopBodyError');    
        }  
        elseif($image === '') {
            $errorMessage = Flux::Message('CMScashshopImageError');    
        }                 
    } else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $image)) {
                $sql = "INSERT INTO {$server->loginDatabase}.$cashshops (title, path, image, position, price, currency, body, modified)";
        $sql .= "VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        $sth = $server->connection->getStatement($sql);
        $sth->execute(array($title, $path, $image, $position, $price, $currency, $body)); 
		
        $session->setMessageData(Flux::message('CMSCashShopsAdded'));
    if ($auth->actionAllowed('ypanel', 'store')) {
            $this->redirect($this->url('ypanel', 'store'));
  } else {
    echo "Sorry, there was an error uploading your file.";
    }
  }
}
?>

