<?php
class BackupForm extends Form
{
	static $path;
	function BackupForm()
	{
		Form::Form('BackupForm');
		$this->link_css('assets/default/css/cms.css');
	}
	function save_file($file,$content)
	{
		$hand = fopen(BackupForm::$path.'/'.strtolower($file).'.sql','w+');
		fwrite($hand,$content);
		fclose($hand);
	}
	function make_dir($name)
	{
		if(!is_dir($name))
		{
			@mkdir($name);
		}
		return $name;
	}
	function create_db($fields,$table)
	{
		$sql ='CREATE TABLE `'.$table.'` (';
		$first = true;
		foreach($fields as $key=>$value)
		{
			if($first)
			{
				$sql .= '`'.$value['Field'].'` '.$value['Type'].' NOT NULL';
				$first = false;
			}
			else
			{
				$sql .= ',`'.$value['Field'].'` '.$value['Type'].' NOT NULL';
			}
			if($value['Extra']=='auto_increment')
			{
				$sql .= ' AUTO_INCREMENT';
			}
			if($value['Default']!='')
			{
				$sql .= ' DEFAULT \''.$value['Default'].'\'';
			}
			if($value['Key'] == 'PRI')
			{
				$sql .= ',PRIMARY KEY (`'.$value['Field'].'`)';
			}
			elseif($value['Key'] == 'UNI')
			{
				$sql .= ',UNIQUE KEY `'.$value['Field'].'` (`'.$value['Field'].'`)';
			}
			elseif($value['Key'] == 'MUL')
			{
				$sql .= ',KEY `'.$value['Field'].'` (`'.$value['Field'].'`)';
			}
		}
		$sql .= ') ENGINE=MyISAM  DEFAULT CHARSET=latin1;';
		return $sql.'\n';
	}
	function backup_data($table)
	{
		$query = '';
		if($data = DB::fetch_all('select * from '.$table))
		{
			foreach($data as $key=>$value)
			{
				$query .= insert($table,$value).'\n';
			}
		}
		return $query;
	}
	function backup($tables)
	{
		foreach($tables as $key=>$value)
		{
			$sql = '';
			$fields = BackupDB::get_fields($value);
			$sql .= $this->create_db($fields,$value);
			$sql .= $this->backup_data($value);
			$this->save_file($value,$sql);
		}
	}
	function on_submit()
	{
		if(Url::get('cmd')=='backup' and isset($_REQUEST['selected_ids']) and count($_REQUEST['selected_ids'])>0)
		{
			set_time_limit(0);
			BackupForm::$path = $this->make_dir('backup/sql/'.strtoupper(substr(Session::get('user_id'),0,1)).substr(Session::get('user_id'),1).'_backup_'.date('h\hi.d-m-y',time()));
			$this->backup($_REQUEST['selected_ids']);
		}
		Url::redirect_current();
	}
	function draw()
	{
		$tables = BackupDB::get_all_tables();
		$i = 1;
		foreach($tables as $key=>$value)
		{
			$value['indexs'] = $i++;
			$tables[$key+1] = $value;
		}
		unset($tables[0]);
		$this->parse_layout('backup',array(
			'tables'=>$tables
			,'total'=>count($tables)
		));
	}
}
?>
