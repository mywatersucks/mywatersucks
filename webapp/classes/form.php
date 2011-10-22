<?php
	class form {
		var $inputs;
		var $errors;
		var $values;

		function form() {

		}

		function add($name,$type="string",$required=false,$blank_message='',$invalid_message='') {
			$this->inputs[]=Array($name,$type,$required,$blank_message,$invalid_message);
		}
		
		function error_str($break='<br />') {
			$err="";
			foreach ($this->errors as $error) {
				$err.=$error.$break;
			}
			return $err;
		}

		function validate(){
			$this->errors=array();
			$valid=true;
			
			foreach ($this->inputs as $input) {
				$value=$_REQUEST[$input[0]];
				if ($input[2] && $value=="") {
					// Required but blank
					$this->errors[]=$input[3];
					$valid=false;
					continue;
				}
				if ($value=="") continue;
				switch ($input[1]) {
					case "Integer":
					case "integer":
					case "int":
						if (!is_numeric($value)) {
							$this->errors[]=$input[4];
							$valid=false;
						}
						break;
					case "CheckBox":
					case "checkbox":
					case "Checkbox":
						if ($value=='Y')
							$_REQUEST[$input[0]]=true;
						else 
							$_REQUEST[$input[0]]=false;
				}
			}
			return $valid;
		}

	}	

?>