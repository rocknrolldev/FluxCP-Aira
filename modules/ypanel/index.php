<?php
if (!defined('FLUX_ROOT')) exit;

$title = Flux::message('YpanelTitle');
$info  = array(
		'accounts'   => 0,
		'characters' => 0,
		'guilds'     => 0,
		'parties'    => 0,
		'zeny'       => 0,
		'classes'    => array()
);

// Accounts.
$sql = "SELECT COUNT(account_id) AS total FROM {$server->loginDatabase}.login WHERE sex != 'S' ";
if (Flux::config('HideTempBannedStats')) {
	$sql .= "AND unban_time <= UNIX_TIMESTAMP() ";
}
if (Flux::config('HidePermBannedStats')) {
	if (Flux::config('HideTempBannedStats')) {
		$sql .= "AND state != 5 ";
	} else {
		$sql .= "AND state != 5 ";
	}
}
$sth = $server->connection->getStatement($sql);
$sth->execute();
$info['accounts'] += $sth->fetch()->total;

// Characters.
$sql = "SELECT COUNT(`char`.char_id) AS total FROM {$server->charMapDatabase}.`char` ";
if (Flux::config('HideTempBannedStats')) {
	$sql .= "LEFT JOIN {$server->loginDatabase}.login ON login.account_id = `char`.account_id ";
	$sql .= "WHERE login.unban_time <= UNIX_TIMESTAMP()";
}
if (Flux::config('HidePermBannedStats')) {
	if (Flux::config('HideTempBannedStats')) {
		$sql .= " AND login.state != 5";
	} else {
		$sql .= "LEFT JOIN {$server->loginDatabase}.login ON login.account_id = `char`.account_id ";
		$sql .= "WHERE login.state != 5";
	}
}
$sth = $server->connection->getStatement($sql);
$sth->execute();
$info['characters'] += $sth->fetch()->total;

// Guilds.
$sql = "SELECT COUNT(guild_id) AS total FROM {$server->charMapDatabase}.guild";
$sth = $server->connection->getStatement($sql);
$sth->execute();
$info['guilds'] += $sth->fetch()->total;

// Parties.
$sql = "SELECT COUNT(party_id) AS total FROM {$server->charMapDatabase}.party";
$sth = $server->connection->getStatement($sql);
$sth->execute();
$info['parties'] += $sth->fetch()->total;

// Zeny.
$bind = array();
$sql  = "SELECT SUM(`char`.zeny) AS total FROM {$server->charMapDatabase}.`char` ";
if ($hideGroupLevel=Flux::config('InfoHideZenyGroupLevel')) {
	$sql .= "LEFT JOIN {$server->loginDatabase}.login ON login.account_id = `char`.account_id ";
	
	$groups = AccountLevel::getGroupID($hideGroupLevel, '<');
	if(!empty($groups)) {
		$ids   = implode(', ', array_fill(0, count($groups), '?'));
		$sql  .= "WHERE login.group_id IN ($ids) ";
		$bind  = array_merge($bind, $groups);
	}
}
if (Flux::config('HideTempBannedStats')) {
	if ($hideGroupLevel) {
		$sql .= " AND unban_time <= UNIX_TIMESTAMP()";
	} else {
		$sql .= "LEFT JOIN {$server->loginDatabase}.login ON login.account_id = `char`.account_id ";
		$sql .= "WHERE unban_time <= UNIX_TIMESTAMP()";
	}
}
if (Flux::config('HidePermBannedStats')) {
	if ($hideGroupLevel || Flux::config('HideTempBannedStats')) {
		$sql .= " AND state != 5";
	} else {
		$sql .= "LEFT JOIN {$server->loginDatabase}.login ON login.account_id = `char`.account_id ";
		$sql .= "WHERE state != 5";
	}
}

$sth = $server->connection->getStatement($sql);
$sth->execute($hideGroupLevel ? $bind : array());
$info['zeny'] += $sth->fetch()->total;

// Job classes.
$sql = "SELECT `char`.class, COUNT(`char`.class) AS total FROM {$server->charMapDatabase}.`char` ";
if (Flux::config('HideTempBannedStats')) {
	$sql .= "LEFT JOIN {$server->loginDatabase}.login ON login.account_id = `char`.account_id ";
	$sql .= "WHERE login.unban_time <= UNIX_TIMESTAMP() ";
}
if (Flux::config('HidePermBannedStats')) {
	if (Flux::config('HideTempBannedStats')) {
		$sql .= " AND login.state != 5 ";
	} else {
		$sql .= "LEFT JOIN {$server->loginDatabase}.login ON login.account_id = `char`.account_id ";
		$sql .= "WHERE login.state != 5 ";
	}
}
$sql .= "GROUP BY `char`.class";
$sth = $server->connection->getStatement($sql);
$sth->execute();

$classes = $sth->fetchAll();
if ($classes) {
	foreach ($classes as $class) {
		$classnum = (int)$class->class;
		$info['classes'][Flux::config("JobClasses.$classnum")] = $class->total;
	}
}

if (Flux::config('SortJobsByAmount')) {
	arsort($info['classes']);
}
?>
<?php
if (!defined('FLUX_ROOT')) exit;

$title = Flux::message('ServerStatusTitle');
$cache = FLUX_DATA_DIR.'/tmp/ServerStatus.cache';
$tbl = Flux::config('FluxTables.OnlinePeakTable'); 


if (file_exists($cache) && (time() - filemtime($cache)) < (Flux::config('ServerStatusCache') * 60)) {
	$serverStatus = unserialize(file_get_contents($cache));
}
else {
	$serverStatus = array();
	foreach (Flux::$loginAthenaGroupRegistry as $groupName => $loginAthenaGroup) {
		if (!array_key_exists($groupName, $serverStatus)) {
			$serverStatus[$groupName] = array();
		}

		$loginServerUp = $loginAthenaGroup->loginServer->isUp();

		foreach ($loginAthenaGroup->athenaServers as $athenaServer) {
			$serverName = $athenaServer->serverName;

			$sql = "SELECT COUNT(char_id) AS players_online FROM {$athenaServer->charMapDatabase}.char WHERE `online` > '0'";
			$sth = $loginAthenaGroup->connection->getStatement($sql);
			$sth->execute();
			$res = $sth->fetch();

			if(Flux::config('EnablePeakDisplay')){
				$sth = $server->connection->getStatement("SELECT `users` FROM {$server->charMapDatabase}.$tbl");
				$sth->execute();
				$peak = $sth->fetch();
			}
			$serverStatus[$groupName][$serverName] = array(
				'loginServerUp' => $loginServerUp,
				 'charServerUp' => $athenaServer->charServer->isUp(),
				  'mapServerUp' => $athenaServer->mapServer->isUp(),
				'playersOnline' => intval($res ? $res->players_online : 0),
                  'playersPeak' => intval($peak ? $peak->users : 0)
			);
		}
	}
	
	$fp = fopen($cache, 'w');
	if (is_resource($fp)) {
		fwrite($fp, serialize($serverStatus));
		fclose($fp);
	}
}


?>
<?php
if (!defined('FLUX_ROOT')) exit;

$this->loginRequired();

$title = 'List Characters';

$bind        = array();
$sqlpartial  = "LEFT OUTER JOIN {$server->charMapDatabase}.guild_member ON guild_member.char_id = ch.char_id ";
$sqlpartial .= "LEFT OUTER JOIN {$server->charMapDatabase}.guild ON guild.guild_id = guild_member.guild_id ";
$sqlpartial .= "LEFT OUTER JOIN {$server->loginDatabase}.login ON login.account_id = ch.account_id ";
$sqlpartial .= "LEFT OUTER JOIN {$server->charMapDatabase}.`char` AS partner ON partner.char_id = ch.partner_id ";
$sqlpartial .= "LEFT OUTER JOIN {$server->charMapDatabase}.`char` AS mother ON mother.char_id = ch.mother ";
$sqlpartial .= "LEFT OUTER JOIN {$server->charMapDatabase}.`char` AS father ON father.char_id = ch.father ";
$sqlpartial .= "LEFT OUTER JOIN {$server->charMapDatabase}.`char` AS child ON child.char_id = ch.child ";

$sqlwhere    = "WHERE 1=1 ";
$sqlcount    = '';

$charID = $params->get('char_id');
if ($charID) {
	$sqlwhere   .= "AND ch.char_id = ? ";
	$bind[]      = $charID;
}
else {
	$opMapping   = array('eq' => '=', 'gt' => '>', 'lt' => '<');
	$opValues    = array_keys($opMapping);
	$account     = $params->get('account');
	$charName    = $params->get('char_name');
	$charClass   = $params->get('char_class');
	$baseLevelOp = $params->get('base_level_op');
	$baseLevel   = $params->get('base_level');
	$jobLevelOp  = $params->get('job_level_op');
	$jobLevel    = $params->get('job_level');
	$zenyOp      = $params->get('zeny_op');
	$zeny        = $params->get('zeny');
	$guild       = $params->get('guild');
	$partner     = $params->get('partner');
	$mother      = $params->get('mother');
	$father      = $params->get('father');
	$child       = $params->get('child');
	$online      = $params->get('online');
	$slotOp      = $params->get('slot_op');
	$slot        = $params->get('slot');
	
	if ($account) {
		$sqlcount .= "LEFT OUTER JOIN {$server->loginDatabase}.login ON login.account_id = ch.account_id ";
		if (preg_match('/^\d+$/', $account)) {
			$sqlwhere   .= "AND login.account_id = ? ";
			$bind[]      = $account;
		}
		else {
			$sqlwhere   .= "AND (login.userid LIKE ? OR login.userid = ?) ";
			$bind[]      = "%$account%";
			$bind[]      = $account;
		}
	}
	
	if ($charName) {
		$sqlwhere   .= "AND (ch.name LIKE ? OR ch.name = ?) ";
		$bind[]      = "%$charName%";
		$bind[]      = $charName;
	}
	
	if ($charClass) {
		$className = preg_quote($charClass, '/');
		$classIDs  = preg_grep("/.*?$className.*?/i", Flux::config('JobClasses')->toArray());
		
		if (count($classIDs)) {
			$classIDs    = array_keys($classIDs);
			$sqlwhere   .= "AND (";
			$partial     = '';
			
			foreach ($classIDs as $id) {
				$partial .= "ch.class = ? OR ";
				$bind[]   = $id;
			}
			
			$partial     = preg_replace('/\s*OR\s*$/', '', $partial);
			$sqlwhere   .= "$partial) ";
		}
		else {
			$sqlwhere .= 'AND ch.class IS NULL ';
		}
	}
	
	if (in_array($baseLevelOp, $opValues) && trim($baseLevel) != '') {
		$op          = $opMapping[$baseLevelOp];
		$sqlwhere   .= "AND ch.base_level $op ? ";
		$bind[]      = $baseLevel;
	}
	
	if (in_array($jobLevelOp, $opValues) && trim($jobLevel) != '') {
		$op          = $opMapping[$jobLevelOp];
		$sqlwhere   .= "AND ch.job_level $op ? ";
		$bind[]      = $jobLevel;
	}
	
	if (in_array($zenyOp, $opValues) && trim($zeny) != '') {
		$op          = $opMapping[$zenyOp];
		$sqlwhere   .= "AND ch.zeny $op ? ";
		$bind[]      = $zeny;
	}
	
	if ($guild) {
		$sqlcount   .= "LEFT OUTER JOIN {$server->charMapDatabase}.guild_member ON guild_member.char_id = ch.char_id ";
		$sqlcount   .= "LEFT OUTER JOIN {$server->charMapDatabase}.guild ON guild.guild_id = guild_member.guild_id ";
		$sqlwhere   .= "AND (guild.name LIKE ? OR guild.name = ?) ";
		$bind[]      = "%$guild%";
		$bind[]      = $guild;
	}
	
	if ($partner) {
		$sqlcount   .= "LEFT OUTER JOIN {$server->charMapDatabase}.`char` AS partner ON partner.char_id = ch.partner_id ";
		$sqlwhere   .= "AND (partner.name LIKE ? OR partner.name = ?) ";
		$bind[]      = "%$partner%";
		$bind[]      = $partner;
	}
	
	if ($mother) {
		$sqlcount   .= "LEFT OUTER JOIN {$server->charMapDatabase}.`char` AS mother ON mother.char_id = ch.mother ";
		$sqlwhere   .= "AND (mother.name LIKE ? OR mother.name = ?) ";
		$bind[]      = "%$mother%";
		$bind[]      = $mother;
	}
	
	if ($father) {
		$sqlcount   .= "LEFT OUTER JOIN {$server->charMapDatabase}.`char` AS father ON father.char_id = ch.father ";
		$sqlwhere   .= "AND (father.name LIKE ? OR father.name = ?) ";
		$bind[]      = "%$father%";
		$bind[]      = $father;
	}
	
	if ($child) {
		$sqlcount   .= "LEFT OUTER JOIN {$server->charMapDatabase}.`char` AS child ON child.char_id = ch.child ";
		$sqlwhere   .= "AND (child.name LIKE ? OR child.name = ?) ";
		$bind[]      = "%$child%";
		$bind[]      = $child;
	}
	
	if ($online == 'on' || $online == 'off') {
		if ($online == 'on') {
			$sqlwhere .= "AND ch.online > 0 ";
		}
		else {
			$sqlwhere .= "AND ch.online < 1 ";
		}
	}
	
	if (in_array($slotOp, $opValues) && trim($slot) != '') {
		$op          = $opMapping[$slotOp];
		$sqlwhere   .= "AND ch.char_num $op ? ";
		$bind[]      = $slot - 1;
	}
}

$sql  = "SELECT COUNT(ch.char_id) AS total FROM {$server->charMapDatabase}.`char` AS ch $sqlcount $sqlwhere";
$sth  = $server->connection->getStatement($sql);

$sth->execute($bind);
$paginator = $this->getPaginator($sth->fetch()->total);
$paginator->setSortableColumns(array(
	'ch.char_id' => 'desc', 'userid', 'char_name', 'ch.base_level', 'ch.job_level',
	'ch.zeny', 'guild_name', 'partner_name', 'mother_name', 'father_name', 'child_name',
	'ch.online', 'ch.char_num'
));

$col  = "ch.account_id, ch.char_id, ch.name AS char_name, ch.char_num, ";
$col .= "ch.online, ch.base_level, ch.job_level, ch.class, ch.zeny, ";
$col .= "guild.guild_id, guild.name AS guild_name, guild.emblem_id as emblem, ";
$col .= "login.userid, partner.name AS partner_name, partner.char_id AS partner_id, ";
$col .= "mother.name AS mother_name, mother.char_id AS mother_id, ";
$col .= "father.name AS father_name, father.char_id AS father_id, ";
$col .= "child.name AS child_name, child.char_id AS child_id";

$sql  = "SELECT $col FROM {$server->charMapDatabase}.`char` AS ch $sqlpartial $sqlwhere";
$sql  = $paginator->getSQL($sql);
$sth  = $server->connection->getStatement($sql);

$sth->execute($bind);

$characters = $sth->fetchAll();
$authorized = $auth->actionAllowed('character', 'view') && $auth->allowedToViewCharacter;

if ($characters && count($characters) === 1 && $authorized && Flux::config('SingleMatchRedirect')) {
	$this->redirect($this->url('character', 'view', array('id' => $characters[0]->char_id)));
}
?>
