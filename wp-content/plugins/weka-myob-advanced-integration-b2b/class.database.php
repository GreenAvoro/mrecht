<?php

class Database
{ // Class : begin

	var $host;  		//Hostname, Server
	var $password; 	//Passwort MySQL
	var $user; 		//User MySQL
	var $database; 	//Datenbankname MySQL
	var $link;
	var $query;
	var $result;
	var $rows;
	
	function Database()
	{ // Method : begin
		$this->password = "WgCubb8aGsBK";           //          <<---------
		$this->user = "apowerte_main";                   //          <<---------
		$this->database = "apowerte_main";           //          <<---------
		//$this->host = 'localhost';
		$this->host = "localhost";                  //          <<---------

		$this->rows = 0;
	} // Method : end
	
	function OpenLink()
	{ // Method : begin
		$this->link = @mysql_connect($this->host,$this->user,$this->password) or die (print "Class Database: Error while connecting to DB (link)");
	} // Method : end
	
	function SelectDB()
	{ // Method : begin
		@mysql_select_db($this->database,$this->link) or die (print "Class Database: Error while selecting DB");
	} // Method : end
	
	function CloseDB()
	{ // Method : begin
		mysql_close();
	} // Method : end
	
	function Query($query)
	{ // Method : begin
		$this->OpenLink();
		$this->SelectDB();
		$this->query = $query;
		$this->result = mysql_query($query,$this->link) or die (print "Class Database: Error while executing Query " . $query);
		$this->CloseDB();
	} // Method : end	

} // Class : end

// class Curl
// {       

//     public $cookieJar = "";

//     public function __construct($cookieJarFile = 'cookies.txt') {
//         $this->cookieJar = $cookieJarFile;
// 		$settings = new dataobject('settings');
// 		$settings->selectObjectByField('s_username',$_SERVER['REDIRECT_REMOTE_USER'],'`s_username` ASC');

// 		$this->setBase = $settings->s_advanced_url;
// 		$this->setExtension = $settings->s_advanced_extension;
// 		$this->setVersion = $settings->s_advanced_version;
// 		$connection = array('name'=>$settings->s_advanced_username,'password'=>$settings->s_advanced_password,'company'=>$settings->s_client,'branch'=>$settings->s_advanced_branch);		

//     	$this->postForm($this->setBase.'/entity/auth/login',json_encode($connection));
// 	}

//     function setup()
//     {
		
//         $header = array();
//         $header[0] = "Content-Type: application/json";
//         $header[1] =  "Connection: keep-alive";
// 		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
//         curl_setopt($this->curl,CURLOPT_COOKIEJAR, $this->cookieJar); 
//         curl_setopt($this->curl,CURLOPT_COOKIEFILE, $this->cookieJar);
// 		curl_setopt($this->curl, CURLOPT_HTTPHEADER, $header);
// 	}


//     function get($url)
//     {		
// 		$this->curl = curl_init($this->setBase.'/entity/'.$this->setExtension.'/'.$this->setVersion.'/'.$url);
// 		$this->setup();
// 		return json_decode($this->request(),true);
//     }

//     function getAll($reg,$str)
//     {
//         preg_match_all($reg,$str,$matches);
//         return $matches[1];
//     }



//     function put($url, $fields, $referer='')
//     {
// 		$url = $this->setBase.'/entity/'.$this->setExtension.'/'.$this->setVersion.'/'.$url;
		
//         $this->curl = curl_init($url);
//         $header = array();
//         $header[0] = "Content-Type: application/json";
//         $header[1] =  "Connection: keep-alive";

//         curl_setopt($this->curl, CURLOPT_HTTPHEADER, $header);
// 		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
// 		curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "PUT");
//         curl_setopt($this->curl, CURLOPT_POSTFIELDS, $fields);
//         curl_setopt($this->curl, CURLOPT_URL, $url);
//         curl_setopt($this->curl,CURLOPT_COOKIEJAR, $this->cookieJar); 
//         curl_setopt($this->curl,CURLOPT_COOKIEFILE, $this->cookieJar);
		
//         return $this->request();
//     }
	
//    function postForm($url, $fields, $referer='')
//     {
//         $this->curl = curl_init($url);
//         $header = array();
//         $header[0] = "Content-Type: application/json";
//         $header[1] =  "Connection: keep-alive";

//         curl_setopt($this->curl, CURLOPT_HTTPHEADER, $header);
// 		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		
//         curl_setopt($this->curl, CURLOPT_URL, $url);
//         curl_setopt($this->curl, CURLOPT_POST, 1);
//         curl_setopt($this->curl, CURLOPT_REFERER, $referer);
//         curl_setopt($this->curl, CURLOPT_POSTFIELDS, $fields);
//         curl_setopt($this->curl,CURLOPT_COOKIEJAR, $this->cookieJar); 
//         curl_setopt($this->curl,CURLOPT_COOKIEFILE, $this->cookieJar);
		
//         return $this->request();
//     }

//     function getInfo($info)
//     {
//         $info = ($info == 'lasturl') ? curl_getinfo($this->curl, CURLINFO_EFFECTIVE_URL) : curl_getinfo($this->curl, $info);
//         return $info;
//     }

//     function request()
//     {
//         $response = curl_exec($this->curl);
// 		return $response;
//     }
// }

?>