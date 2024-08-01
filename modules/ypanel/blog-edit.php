<?php
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$title = Flux::message('CMSblogEditTitle');
$blogs 	= Flux::config('FluxTables.CMSBlogsTable');

$id 	= $params->get('id');
$sql 	= "SELECT id, title, path, image, category, body, modified FROM {$server->loginDatabase}.$blogs WHERE id = ?";
$sth 	= $server->connection->getStatement($sql);
$sth->execute(array($id));
$blog 	= $sth->fetch();

$tinymce_key = Flux::config('TinyMCEKey'); 

if($blog) {
	$title	= $blog->title;
	$path	= $blog->path;
	$image	= $blog->image;
	$body	= $blog->body;
	$category	= $blog->category;
    
    if(count($_POST)) {
        $title = trim($params->get('blog_title'));
		$path 	= trim($params->get('blog_path'));
        $image 	= trim($params->get('fileToUpload'));
        $body 	= trim($params->get('blog_body'));
        $category 	= trim($params->get('blog_category'));
        
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
        else { 
            $sql  = "UPDATE {$server->loginDatabase}.$blogs SET title = ?, path = ?, image = ?, body = ?, category = ?, modified = NOW() WHERE id = ?";            
            $sth = $server->connection->getStatement($sql);
            $sth->execute(array($title, $path, $image, $body, $category, $id)); 
			
            $session->setMessageData(Flux::message('CMSblogUpdated'));
            if ($auth->actionAllowed('ypanel', 'blog')) {
                $this->redirect($this->url('ypanel','blog'));
            }
            else {
                $this->redirect();
            }
        }
    }
}
?>
