<?php

class Core
{
	private
		$appPath,
		$license,
		$reConfig,
		$input,
		$error = array();

	public function init($config)
	{
		$this->appPath = $config['application'];
		$this->license = $config['license'];

		if (!is_dir($this->appPath)) $this->error[] = "Folder application salah";
		//if (!file_exists($this->dbFile)) $this->error[] = "File sql tidak ditemukan";

		return $this->error;
	}

	public function setInput($input)
	{
		$this->input = (object) $input;
	}

	public function reWrite()
	{
		$reWriteFile = array('config');


		$filePath = "$this->appPath/config/config.php";
		$file = file_get_contents($filePath);

		
		$replace = "\$config['license'] = '".$this->input->license."';";
		//echo $replace;
		//$array = explode(PHP_EOL, $file);
		$array = preg_split("/\\r\\n|\\r|\\n/",$file);
		$array[54] = $replace;
		

		//$file = str_replace($find, $replace, $file);
		$content = "";
		$id =0;
		foreach ($array as $value) {
			if ($id < count($array)-1) {
				$content .= $value.PHP_EOL;
			}else{
				$content .= $value;
			}
			
			$id++;
		}

		$reWrite = file_put_contents($filePath, $content);
	}


	public function getError()
	{
		return $this->error;
	}
}
