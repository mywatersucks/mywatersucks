<?php

abstract class table {
	//var $primary_key='id';
	var $new=true;
	
	function load($values='',$lazyLoading=false) {
		$db=new DB_sql();
		$db->connect();
	
		// Generate the where statement
		$where='';
		if (!empty($values)) {
			foreach ($values as $key => $value) {
				$where.=" AND $key='$value'"; 
			}
			$where=substr($where,5);
			$where='WHERE '.$where;
		}
	
		$db->query("SELECT * FROM ".$this->table_name." $where");
		
		if ($db->next_record()) {
			foreach ($this->values as $key => $val) {
				$this->$val=$db->Record[$key];
			}
			if (!$lazyLoading && method_exists($this,'load_all')) $this->load_all();
			$this->new=false;
			return true;
		}
		
		return false;
	}
	
	function save() {
		$primary_key=$this->primary_key;
		
		if ($this->new) {
			// New Record
			$db=new DB_sql();
			$db->connect();
			
			$kstr="";
			$vstr="";
			foreach ($this->values as $key => $val) {
				if ($val=='') continue;			// If it is null, don't add it. 
				if ($val==$primary_key) continue;
				$kstr.=','.$key;
				$vstr.=',\''.addslashes($this->$val).'\'';
			}
			$kstr=substr($kstr,1);		// Remove the first comma
			$vstr=substr($vstr,1);		//   ""    ""   ""   ""   ;-) The ancient way of "Ctrl + C" and "Ctrl + V"

			$db->query("INSERT INTO ".$this->table_name."($kstr) VALUES($vstr)");
			
			//if ($this->$primary_key=='') {
				// It's probally an auto number.
				//$this->$primary_key=$db->get_last_insert_id();
			//}
			return $db->get_last_insert_id();
		} else {
			$db=new DB_sql();
			$db->connect();
			
			$str="";
			foreach ($this->values as $key => $val) {
				if ($val==$primary_key) continue;
				$str.=', '.$key.' = \''.addslashes($this->$val).'\'';
			}
			$str=substr($str,1);		// Remove the first comma
			
			$db->query("UPDATE ".$this->table_name." SET $str WHERE $primary_key='".$this->$primary_key."'");
		}
	}
	
	function remove() {
		$primary_key=$this->primary_key;

		if ($primary_key!="") {
			$db=new DB_sql();
			$db->connect();
			$db->query("DELETE FROM ".$this->table_name." WHERE $primary_key='".$this->$primary_key."'");
		}
	}
	
}

function class_list($table,$columns,$values,$class) {
	$result=array();

	$db=new DB_sql();
	$db->connect();

	$columns_text='';
	foreach ($columns as $col) {
		$columns_text.=",$col"; 
	}
	$columns_text=substr($columns_text,1);

	// Generate the where statement
	$where='';
	if (!empty($values)) {
		foreach ($values as $key => $value) {
			$where.=" AND $key='$value'"; 
		}
		$where=substr($where,5);
		$where='WHERE '.$where;
	}
	
	$db->query("SELECT 
					$columns_text
				FROM
					$table
				$where
				");

	while ($db->next_record()) {
		$c=new $class();
		
		$load_array = array();
		foreach ($columns as $key => $col) {
			$load_array[$key]=$db->Record[$col]; 
		}
		
		$c->load( $load_array );
		$result[]=$c;
	}
	
	return $result;
}


function table_add($table,$values) {
	$db=new DB_sql();
	$db->connect();


	$vals='';
	$columns_text='';
	foreach ($values as $key => $value) {
		$columns_text.=",$key"; 
		$vals.=",'".addslashes($value)."'"; 
	}
	$columns_text="(".substr($columns_text,1).")";
	$vals="(".substr($vals,1).")";
	
	$db->query("INSERT INTO $table
					$columns_text
				VALUES
					$vals
				");
}


?>