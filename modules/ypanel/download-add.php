<?php
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$title = Flux::message('CMSDownloadAddTitle');

$downloads	= Flux::config('FluxTables.CMSDownloadsTable'); 
$title	= trim($params->get('download_title'));
$link	= trim($params->get('download_link'));

$tinymce_key = Flux::config('TinyMCEKey'); 

if(count($_POST))
{
       if($title === '') {
            $errorMessage = Flux::Message('CMSDownloadTitleError');
		}
        elseif($link === '') {
            $errorMessage = Flux::Message('CMSDownloadLinkError');
        }
    else {
        $sql = "INSERT INTO {$server->loginDatabase}.$downloads (title, link, modified)";
        $sql .= "VALUES (?, ?, NOW())";
        $sth = $server->connection->getStatement($sql);
        $sth->execute(array($title, $link)); 
		
        $session->setMessageData(Flux::message('CMSDownloadAddTitle'));
        if ($auth->actionAllowed('ypanel','download')) {
            $this->redirect($this->url('ypanel','download'));
        }
        else {
            $this->redirect();
        }
    }
}
?>
