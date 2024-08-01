
<?php
if (!defined('FLUX_ROOT')) exit;
$this->loginRequired();
$title = Flux::message('CMScashshopEditTitle');
$cashshops 	= Flux::config('FluxTables.CMSCashShopsTable');

$id 	= $params->get('id');
$sql 	= "SELECT id, title, path, image, position, price, currency, body, modified FROM {$server->loginDatabase}.$cashshops WHERE id = ?";
$sth 	= $server->connection->getStatement($sql);
$sth->execute(array($id));
$cashshop 	= $sth->fetch();

$tinymce_key = Flux::config('TinyMCEKey'); 

if($cashshop) {
	$title	= $cashshop->title;
	$path	= $cashshop->path;
	$image	= $cashshop->image;
	$position	= $cashshop->position;
	$price	= $cashshop->price;
	$currency	= $cashshop->currency;
	$body	= $cashshop->body;
    
    if(count($_POST)) {
        $title = trim($params->get('cashshop_title'));
		$path 	= trim($params->get('cashshop_path'));
		$position	= trim($params->get('cashshop_position'));
		$price	= trim($params->get('cashshop_price'));
		$currency	= trim($params->get('cashshop_currency'));
        $image 	= trim($params->get('fileToUpload'));
        $body 	= trim($params->get('cashshop_body'));
        
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
        else {  
            $sql  = "UPDATE {$server->loginDatabase}.$cashshops SET title = ?, path = ?, image = ?, position = ?, price = ?, currency = ?, body = ?, modified = NOW() WHERE id = ?";            
            $sth = $server->connection->getStatement($sql);
            $sth->execute(array($title, $path, $image, $position, $price, $currency, $body, $id)); 
			
            $session->setMessageData(Flux::message('CMScashshopUpdated'));
            if ($auth->actionAllowed('ypanel', 'store')) {
                $this->redirect($this->url('ypanel','store'));
            }else {
                $this->redirect();
            }
        }
    }
}
?>