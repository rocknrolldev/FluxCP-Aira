<?php
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$title = Flux::message('CMSblogAddTitle');

$blogs	= Flux::config('FluxTables.CMSBlogsTable'); 
$title	= trim($params->get('blog_title'));
$path	= trim($params->get('blog_path'));
$image	= trim($params->get('fileToUpload'));
$category	= trim($params->get('blog_category'));
$body	= trim($params->get('blog_body'));

$tinymce_key = Flux::config('TinyMCEKey'); 
$target_dir = "ragfile/blog/";
$image = $target_dir . basename($_FILES["fileToUpload"]["tmp_name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($image,PATHINFO_EXTENSION));


// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
       if($title === '') {
            $errorMessage = Flux::Message('CMSblogTitleError');
		}
        elseif($path === '') {
            $errorMessage = Flux::Message('CMSblogPathError');
        }
        elseif($body === '') {
            $errorMessage = Flux::Message('CMSblogBodyError');    
        }  
        elseif($image === '') {
            $errorMessage = Flux::Message('CMSblogImageError');    
        }                 
    } else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $image)) {
                $sql = "INSERT INTO {$server->loginDatabase}.$blogs (title, path, image, category, body, modified)";
        $sql .= "VALUES (?, ?, ?, ?, ?, NOW())";
        $sth = $server->connection->getStatement($sql);
        $sth->execute(array($title, $path, $image, $category, $body)); 
		
        $session->setMessageData(Flux::message('CMSblogAdded'));
    if ($auth->actionAllowed('ypanel', 'blog')) {
            $this->redirect($this->url('ypanel','blog'));
  } else {
    echo "Sorry, there was an error uploading your file.";
    }
  }
}
?>