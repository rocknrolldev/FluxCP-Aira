
<?php
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$title = Flux::message('CMSstreamerEditTitle');
$streamers 	= Flux::config('FluxTables.CMSStreamersTable');

$id 	= $params->get('id');
$sql 	= "SELECT id, name, charname, picture, path, platform, embed, link, status, modified FROM {$server->loginDatabase}.$streamers WHERE id = ?";
$sth 	= $server->connection->getStatement($sql);
$sth->execute(array($id));
$streamer 	= $sth->fetch();

$tinymce_key = Flux::config('TinyMCEKey'); 

if($streamer) {
	$name	= $streamer->name;
	$charname	= $streamer->charname;
	$picture	= $streamer->picture;
	$path	= $streamer->path;
	$platform	= $streamer->platform;
	$embed	= $streamer->embed;
	$link	= $streamer->link;
	$status	= $streamer->status;
    
    if(count($_POST)) {
        $name = trim($params->get('streamer_name'));
		$charname 	= trim($params->get('streamer_charname'));
		$picture	= trim($params->get('streamer_picture'));
        $path 	= trim($params->get('streamer_path'));
        $platform 	= trim($params->get('streamer_platform'));
        $embed	= trim($params->get('streamer_embed'));
        $link	= trim($params->get('streamer_link'));
        $status 	= trim($params->get('streamer_status'));
        
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
        else {  
            $sql  = "UPDATE {$server->loginDatabase}.$streamers SET name = ?, charname = ?, picture = ?, path = ?, platform = ?, embed = ?, status = ?,  link = ?, modified = NOW() WHERE id = ?";            
            $sth = $server->connection->getStatement($sql);
            $sth->execute(array($name, $charname, $picture, $path, $platform, $embed, $status, $link, $id)); 
			
            $session->setMessageData(Flux::message('CMSstreamerUpdated'));
            if ($auth->actionAllowed('ypanel', 'streamers')) {
                $this->redirect($this->url('ypanel','streamers'));
            }else {
                $this->redirect();
            }
        }
    }
}
?>