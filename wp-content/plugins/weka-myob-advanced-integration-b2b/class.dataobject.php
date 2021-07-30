<?php
// **********************
// CLASS DECLARATION
// **********************

class dataobject
{	
	public function __construct($table)
	{
		$this->database = new Database();
		$this->validate = new Validator();
		$this->table = $table;
		$this->remove = 3;
		$this->active = 2;
		
		$sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".$this->database->database."' AND TABLE_NAME = '".$this->table."';";
		$this->database->query($sql);
	
		while($row = mysql_fetch_object($this->database->result)) {	
			if($row->COLUMN_KEY == 'PRI') {
				$this->id = $row->COLUMN_NAME;
			} else if(preg_match('/status/',$row->COLUMN_NAME)) {
				$this->status = $row->COLUMN_NAME; 
			} else {
				$this->setField($row->ORDINAL_POSITION,$row->COLUMN_NAME);
			}
		}
	}
	
	//set the fields of the table
	protected function setField($position,$column) {
		$this->field[$position] = $column;
	}
	
	public function set($field,$value) {
		$this->$field = $this->validate->setdata($value);
		$this->values[array_search($field, $this->field)] = $this->validate->setdata($value);
		$this->display[array_search($field, $this->field)] = $this->validate->extractdata($value);
	}

	public function get($field) {
		return $this->validate->extractdata($this->$field);	
	}
	
	public function select($id,$any='') {
		if($any=='yes')
			$status = '';
		else
			$status = '';
		$sql = "SELECT * FROM `".$this->table."` WHERE ".$this->id." = '$id'$status";
		
		$this->database->query($sql);
	
		$row = mysql_fetch_object($this->database->result);
		
		foreach($row as $field => $value) {
			$this->set($field,$value);	
		}
	}
	
	public function getValues($table,$field,$value,$filters=array()) {
		$where = array();
		$result = array();
		$extra='';
		if(count($filters) > 0) {
			foreach($filters as $filter_key => $filter_value) {
				$where[] = "`$filter_key` = '$filter_value'";
			}
			$extra = ' AND ';
		}
				
		$sql = "SELECT $field FROM `$table` WHERE ".implode(' AND ',$where)."$extra`".$this->id."` IN (".trim($value,',').") AND `".$this->status."` = '".$this->active."'";
		$this->database->query($sql);
		while($row = mysql_fetch_object($this->database->result)) {
			if(is_object($row)) {
				$result[] = $row->$field;	
			}
		}
		
		return implode(',',$result);
	}
		
	public function getValue($table,$field,$value,$filters=array()) {
		$where = array();
		$extra='';
		if(count($filters) > 0) {
			foreach($filters as $filter_key => $filter_value) {
				$where[] = "`$filter_key` = '$filter_value'";
			}
			$extra = ' AND ';
		}
				
		$sql = "SELECT $field FROM `$table` WHERE ".implode(' AND ',$where)."$extra`".$this->id."` = '$value' AND `".$this->status."` = '".$this->active."'";
		$this->database->query($sql);
		$row = mysql_fetch_object($this->database->result);
		if(is_object($row)) {
			return $row->$field;	
		}
	}

	public function selectByFields($fields=array()) {
		$where = '';
		foreach($fields as $key => $value) {
			$where .= "`".$key."` = '$value' AND ";
		}
		$sql = "SELECT * FROM `".$this->table."` WHERE $where`".$this->status."` = '".$this->active."'";

		$this->database->query($sql);
	
		$row = mysql_fetch_object($this->database->result);
		if(is_object($row)) {
			foreach($row as $field => $value) {
				$this->set($field,$value);	
			}
			$id = $this->id;
			$this->objectID = $row->$id;
		}
	}

	public function selectObjectsByField($field,$value,$order,$replacements) {
		if($field != '')
			$sql = "SELECT * FROM `".$this->table."` WHERE ".$field." = '$value' AND `".$this->status."` = '".$this->active."' ORDER BY ".$order."";
		else
			$sql = "SELECT * FROM `".$this->table."` WHERE `".$this->status."` = '".$this->active."' ORDER BY ".$order."";
		
		$this->database->query($sql);
		$id = $this->id;
		$return = array();
		while($row = mysql_fetch_object($this->database->result)) {
			if(is_object($row)) {
				foreach($row as $field => $value) {
					if(!isset($return[$row->$id]))
						$return[$row->$id] = new \stdClass();
					foreach($replacements as $title => $replacement) {
						$value = str_replace('['.$title.']',$replacement,$value);	
					}
					$return[$row->$id]->$field = $value;	
				}
			}
		}
		return $return;
	}
	
	public function selectObjectByField($field,$value) {
		$sql = "SELECT * FROM `".$this->table."` WHERE ".$field." = '$value' AND `s_status` = '".$this->active."'";
		$this->database->query($sql);
	
		$row = mysql_fetch_object($this->database->result);
		if(is_object($row)) {
			foreach($row as $field => $value) {
				$this->$field = $value;	
			}
		}
	}
		
	public function selectByField($field,$value) {
		$sql = "SELECT * FROM `".$this->table."` WHERE ".$field." = '$value' AND `".$this->status."` = '".$this->active."'";
		$this->database->query($sql);
	
		$row = mysql_fetch_object($this->database->result);
		if(is_object($row)) {
			foreach($row as $field => $value) {
				$this->set($field,$value);	
			}
			$id = $this->id;
			$this->objectID = $row->$id;
		}
	}

	public function selectBox($field,$default,$filters=array()) {
		$array = $this->listSingle($field,$filters);
		
		foreach($array as $key => $value) {
			
			if(is_array($default)) {
				if(in_array($value,$default))
					$selected = ' selected="selected"';
				else
					$selected = '';
			} else {
				if($value == $default)
					$selected = ' selected="selected"';
				else
					$selected = '';				
			}
			
			$options[$key] = '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';
		}
		return implode("\n",$options);	
	}
	
	public function listSingle($field,$filters=array()) {
		$extra = '';
		$where = array();
		if(count($filters) > 0) {
			foreach($filters as $filter_key => $filter_value) {
				$where[] = "`$filter_key` = '$filter_value'";
			}
			$extra = implode(' AND ',$where).' AND ';
		}
				
		$sql = "SELECT * FROM `".$this->table."` WHERE $extra`".$this->status."` = '".$this->active."'";
		$this->database->query($sql);
		
		if(mysql_num_rows($this->database->result) > 0) {
			$return = array();
			while($row = mysql_fetch_object($this->database->result)) {
				$id=$this->id;
				$return[$row->$id] = $this->validate->extractdata($row->$field);
			}
			return $return;
		}
	}
		
	public function rows($args=array(),$group='',$order='',$order_format='',$limit='') {
		$where = array();
		foreach($args as $field=>$outcome) {
			$where[] = "`$field` = '$outcome'";
		}
		
		$sql = "SELECT * FROM `".$this->table."`";
		
		$sql .= "WHERE `".$this->status."` = '".$this->active."'";
		
		if(count($where) > 0)
			$sql .= implode(' AND ',$where);

		if($group != '')
			$sql .= "GROUP BY `$group`";

		if($order != '')
			$sql .= "ORDER BY `$order` $order_format";

		if($limit != '')
			$sql .= "LIMIT 0,$limit";
		
		$this->database->query($sql);
		if(mysql_num_rows($this->database->result) > 0) {
			$return = array();
			while($row = mysql_fetch_object($this->database->result)) {
				$id=$this->id;
				$return[$row->$id] = $row;		
			}
			return $return;
		}
	}
	
	public function insert() {
		$fields = array();
		foreach($this->values as $key => $value) {
			$fields[$key] = $this->field[$key];
		}
		
		$sql = "INSERT INTO `".$this->table."` (`".implode('`,`',$fields)."`) VALUES ('".implode("','",$this->values)."')";

		$this->database->query($sql);
		
		return mysql_insert_id($this->database->link);
	}
	
	public function update($id) {
		if(is_numeric($id)) {
			$sql = "UPDATE `".$this->table."`
			SET ".$this->updateValues()."
			WHERE ".$this->id." = '$id'";
			$this->database->query($sql);
		}
	}
	
	protected function updateValues() {
		foreach($this->field as $key => $field) {
			if(isset($this->values[$key]))
				$value = $this->values[$key];
			else
				$value = '';
				
			$return[] = "`$field` = '".$this->values[$key]."'";
		}
		
		return implode(',',$return);
	}

	public function remove($id) {
		$sql = "UPDATE `".$this->table."` SET `".$this->status."` = '".$this->remove."' WHERE ".$this->id." = '$id'";
		$this->database->query($sql);
	}
	
	public function getMap() {
		$map['table'] = $this->table;
		$map['key'] = $this->id;
		$map['fields'][$order] = array('type'=>"select",'field'=>"st_srid",'table'=>"staff_roles",'source'=>"sr_role");	
		
		return $map;
	}
	
	public function selection($default) {
		$options = array('text','textarea','email','password','web','select','multiselect','checkbox');
		$result = '<option value="">Select An Option</option>';
		foreach($options as $option) {
			if($option == $default)
				$select = ' selected="selected"';
			else
				$select = '';
			
			$result .= '<option value="'.$option.'"'.$select.'>'.ucwords($option).'</option>';
		}
		
		return $result;
	}

	public function selectTable($table) {
		$result = '<option value="">Select An Option</option>';
		$sql = "SHOW TABLES FROM ".$this->database->database;
		$this->database->query($sql);

		while ($row = mysql_fetch_row($this->database->result)) {
			if($row[0] == $table)
				$select = ' selected="selected"';
			else
				$select = '';
			
			$result .= '<option value="'.$row[0].'"'.$select.'>'.ucwords($row[0]).'</option>';
		}		
		return $result;
	}

	public function selectField($table,$field) {
		$result = '<option value="">Select An Option</option>';
		$sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".$this->database->database."' AND TABLE_NAME = '".$table."';";
		$this->database->query($sql);

		while ($row = mysql_fetch_object($this->database->result)) {
			if($row->COLUMN_NAME == $field)
				$select = ' selected="selected"';
			else
				$select = '';
			
			$result .= '<option value="'.$row->COLUMN_NAME.'"'.$select.'>'.ucwords($row->COLUMN_NAME).'</option>';
		}			
		return $result;
	}
	
	public function editMap() {
		$map = '';
		$mapping = array();

		foreach($this->field as $key => $fieldValue) {
			$mapping[$fieldValue] = array();
			$mapping[$fieldValue]['label'] = '';
			$mapping[$fieldValue]['type'] = '';
			$mapping[$fieldValue]['ignore'] = 0;
			$mapping[$fieldValue]['order'] = '';

			$mapping[$fieldValue]['source'] = array();

			$mapping[$fieldValue]['source']['table'] = '';
			$mapping[$fieldValue]['source']['field'] = '';
		}

		
		$sql = "SELECT d_map FROM `datamap` WHERE d_table = '".$this->table."'";
		$this->database->query($sql);
		$row = mysql_fetch_object($this->database->result);
		
		if(isset($row->d_map))
			$mapping = json_decode(stripslashes($row->d_map),true);
		
		
		
		foreach($this->field as $key => $fieldValue) {
			
			if(isset($mapping[$fieldValue]['ignore'])) {
				if($mapping[$fieldValue]['ignore'] == 1)
					$ignoring = ' checked="checked"';			
				else
					$ignoring = '';
			} else
				$ignoring = '';
			
			if(!isset($mapping[$fieldValue]['source']['table']))
				$mapping[$fieldValue]['source']['table'] = '';

			if(!isset($mapping[$fieldValue]['source']['field']))
				$mapping[$fieldValue]['source']['field'] = '';
			
			$map .= '<div class="segment">';
			$map .= '<div class="maplink"><label>Field:</label><span>'.$fieldValue.'</span></div>';
			
			$map .= '<div class="maplink"><label>Ignore:</label><span><input type="checkbox" data-field="'.$fieldValue.'" name="'.$fieldValue.'-ignore"'.$ignoring.' value="1" /></span></div>';
			$map .= '<div class="maplink"><label>Label:</label><span><input type="text" data-field="'.$fieldValue.'" name="'.$fieldValue.'-label" value="'.$mapping[$fieldValue]['label'].'" /></span></div>';

			$map .= '<div class="maplink"><label>Type:</label><span><select class="type" data-field="'.$fieldValue.'" name="'.$fieldValue.'-type">'.$this->selection($mapping[$fieldValue]['type']).'</select></span></div>';

			$map .= '<div class="maplink"><label>Order:</label><span><input type="text" data-field="'.$fieldValue.'" name="'.$fieldValue.'-order" value="'.$mapping[$fieldValue]['order'].'" /></span></div>';
			
			if($mapping[$fieldValue]['type'] == 'select' || $mapping[$fieldValue]['type'] == 'multiselect') {
				$tableStyle = '';
			} else {
				$tableStyle = ' hidden';				
			}
			if($mapping[$fieldValue]['source']['table'] != '') {
				$fieldstyle = '';
			} else {
				$fieldstyle = ' hidden';				
			}
				
			$map .= '<div class="maplink sourcetable'.$tableStyle.'"><label>Source Table:</label><span><select class="datatable" data-field="'.$fieldValue.'" name="'.$fieldValue.'-sourcetable">'.$this->selectTable($mapping[$fieldValue]['source']['table']).'</select></span></div>';
			$map .= '<div class="maplink sourcefield'.$fieldstyle.'"><label>Source Field:</label><span><select class="thefield" data-field="'.$fieldValue.'" name="'.$fieldValue.'-sourcefield">'.$this->selectField($mapping[$fieldValue]['source']['table'],$mapping[$fieldValue]['source']['field']).'</select></span></div>';
			$map .= '</div>';
		}
		return $map;
	}

	public function display($fields,$filters=array(),$limit='',$order='',$sort='',$selectFilter=array(),$exclude=array()) {
		$id = $this->id;
		$result = '';
		$final = '';
		$extra = '';
		$first = '';
		
		$where = array();
		if(count($filters) > 0) {
			foreach($filters as $filter_key => $filter_value) {
				$where[] = "`$filter_key` = '$filter_value'";
			}
			$first = implode(' AND ',$where). " AND ";
		}

		if($order != '') {
			$extra .= " ORDER BY $order";
		}

		if($sort != '') {
			$extra .= " $sort";
		}
		
		if(is_numeric($limit)) {
			$extra .= " LIMIT 0,$limit";
		}
		
		$sql = "SELECT * FROM `".$this->table."` WHERE $first`".$this->status."` = ".$this->active."$extra";

		$this->database->query($sql);
		if(mysql_num_rows($this->database->result) > 0) {
			$return = array();
			while($row = mysql_fetch_object($this->database->result)) {
				$final .= '
				<div class="editBox" data-table="'.$this->table.'" data-key="'.$this->id.'" id="'. $row->$id.'" data-id="'. $row->$id.'">
				<i class="fa fa-times remove"></i>';
				$procedure = '';
				//select issue
				
				foreach($fields as $key => $field) {
					$use = 1;
					if(isset($field['ignore'])) {
						if($field['ignore'] == 1)	
							$use = 0;
					}
					if(in_array($key,$exclude)) {
						$use = 0;						
					}
					if($use == 1) {
						$filtered = array();
						switch($field['type']) {
							case 'multiselect':
								$dataobject = new dataobject($field['source']['table']);
								if(isset($selectFilter[$field['source']['table']]))
									$filtered = $selectFilter[$field['source']['table']];
								
								$display = $dataobject->getValues($field['source']['table'],$field['source']['field'],$row->$key,$filtered);
							break;
							case 'select':
								$dataobject = new dataobject($field['source']['table']);
								if(isset($selectFilter[$field['source']['table']]))
									$filtered = $selectFilter[$field['source']['table']];

								$display = $dataobject->getValue($field['source']['table'],$field['source']['field'],$row->$key,$filtered);
							break;
							case 'checkbox':
								switch($row->$key) {
									case 1:
										$display = 'Yes';
									break;
									case 0:
										$display = 'No';
									break;	
								}
							break;
							case 'password':
								$display = 'xxx-xxx-xxx';
							break;
							default:
								$display = $row->$key;
							break;					
						}
			
						$result[$field['order']] = '<div class="label">'.$field['label'].'</div>'."\n";
						$result[$field['order']] .= '<div class="editField" data-type="'.$field['type'].'" data-field="'.$key.'" data-table="'.$field['source']['table'].'" data-filter="'.urlencode(json_encode($filtered)).'" data-source="'.$field['source']['field'].'"><span class="output">'.$display.'</span></div>'."\n";
						if($key == 'cass_assstid' && $display == 'Website') {
							$procedure = 'website';
						} else if($key == 'cass_assstid' && $display == 'E Commerce') {
							$procedure = 'store';							
						}
					}
				}
				ksort($result);
				$outcome = '';
				foreach($result as $output) {
					$outcome .= $output;
				}
				$procedureLink = '';
				if($procedure == 'website')
					$procedureLink = '<a href="/websiteProcedure.php?service='.$row->$id.'&client='.$_REQUEST['client'].'" class="procedureLink" target="_blank">Website Procedure</a>';
				else if($procedure == 'store')
					$procedureLink = '<a href="/ecommerceProcedure.php?service='.$row->$id.'&client='.$_REQUEST['client'].'" class="procedureLink" target="_blank">E-Commerce Procedure</a>';
				$final .= $outcome.$procedureLink.'
				</div>';
			}
		} 
		return $final;
	}
}

?>