<?php
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$title = Flux::message('CMSFeaturesAddTitle');

$sfeature	= Flux::config('FluxTables.CMSFeaturesTable'); 
$title	= trim($params->get('feature_title'));
$body	= trim($params->get('feature_body'));
$ikon	= trim($params->get('feature_ikon'));

$tinymce_key = Flux::config('TinyMCEKey'); 

if(count($_POST))
{
    if($feature_title === '') {
        $errorMessage = Flux::Message('CMSfeatureTitleError');
    }
    elseif($feature_path === '') {
        $errorMessage = Flux::Message('CMSfeatureIkonError');
    }
    elseif($feature_body === '') {
        $errorMessage = Flux::Message('CMSfeatureBodyError');    
    }
    else {
        $sql = "INSERT INTO {$server->loginDatabase}.$sfeature (title, ikon, body, modified)";
        $sql .= "VALUES (?, ?, ?, NOW())";
        $sth = $server->connection->getStatement($sql);
        $sth->execute(array($title, $ikon, $body)); 
		
        $session->setMessageData(Flux::message('CMSFeaturesAddTitle'));
        if ($auth->actionAllowed('ypanel', 'feature')) {
            $this->redirect($this->url('ypanel','feature'));
        }
        else {
            $this->redirect();
        }
    }
}
?>
