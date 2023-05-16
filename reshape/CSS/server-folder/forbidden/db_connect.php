<?php
include __DIR__."/config.php";

	$Loc =  $_SERVER['DOCUMENT_ROOT'];
	$Loc = str_replace("\\" ,"/", $Loc);
	
	$_FOLDER = RELATIVE_FOLDER;
	
	define("SITE_FOLDER", "$Loc/$_FOLDER");

	define(
		'SITE_PATH',
		$_SERVER['REQUEST_SCHEME'].'://'.
		$_SERVER['HTTP_HOST']."/$_FOLDER"
	);


	define('DOWNLOAD_FOLDER' ,SITE_FOLDER.'/downloads');
	define('DOWNLOAD_PATH'   ,SITE_PATH.  '/downloads');	


	define('SITE_ROOT_FOLDER', SITE_FOLDER);
	define('DATABASE_FILE', SITE_FOLDER."/forbidden/".DATA_FILE);
//=========================================

//database connection
	/*** mysql hostname ***/
	$hostname = 'localhost';

	/***database     ******/
	$dbname   = "kwara_affair";
	$username  = 'root';


	/*** mysql password ***/
	$password  = '';

try {
		$PDO = new PDO(
			"sqlite:".DATABASE_FILE
		);
}catch(PDOException $e){
	echo "database connection error<br />";
	echo $e->getMessage();
	echo DATABASE_FILE;
}

function convertURL($stuff){

	if(strstr(SITE_FOLDER,"storage/sdcard0")){
		if(strstr($stuff,"?")){
			$stuff = str_replace('?', '&', $stuff);
			return "index.php?p=$stuff";
		}else{
			return "index.php?p=$stuff";
		}
	}else{
		return $stuff;
	}
}

//Debug functions

function pretty_r($stuff){
	echo "<pre>";
	print_r($stuff);
	echo "</pre>";
}

function showPrepedQuery ($query, $stuff){

	echo getPrepedQuery ($query, $stuff);
	echo " <br />\n";
}	

function getPrepedQuery ($query, $stuff){

	$q = explode("?", $query);

	$k =0;
	foreach ($stuff as $key => $value) {
		
		$stuff[$key] = escape_sql($value);
		
		$q[$k] .= '?';
		$q[$k] = str_replace('?', " '".$stuff[$key]."' ", $q[$k]);
		$k++;
	}

	return implode("", $q);
}	

function escape_sql($str) {

    $search = array("\\",  "\x00", "\n",  "\r",  "'", "\x1a");
    $replace = array("\\\\","\\0","\\n", "\\r", "\'", "\\Z");

    $ret = str_replace($search, $replace, $str);
    
    return $ret;
}

function file_log($stuff){
	$fil = fopen(DOWNLOAD_FOLDER."/log.log","at");
	fwrite($fil,$stuff."\n");
	fclose($fil);
}

	function capturePOST1($Filename){
		$src = file_get_contents($Filename);
		$stuff = ''.json_encode($_POST).'';
		
		$src =str_replace(
			'<?php capturePOST1(__FILE__); ?>', 
			"$stuff", 
			$src
		);
		
		file_put_contents($Filename, $src); die;
	}
	
	function capturePOST($Filename){
		$src = file_get_contents($Filename);
		$stuff = '/*'.print_r($_POST,true).json_encode($_POST).'*/';
		$src =str_replace("capturePOST", "$stuff\n//", $src);
		file_put_contents($Filename, $src); die;
	}
function verifyPOST($index){
		$Old = json_decode( file_get_contents(SITE_ROOT_FOLDER."/../debug/debug.json"));

		$Stuff = $Old[$index];
		foreach($Stuff as $Key=>$Value){
			if(!isset($_POST[$Key])){ return false; }
		}
		return true;
}
//------------------------------------------------
function substract($str, $frmStr){
 return str_replace("\\", "/", substr($frmStr,strlen($str)));
}


function table_view($Array2D){

	
	if(
		!is_array($Array2D) ||
		!is_array($Array2D[0]) 
	){
		return "";
	}

	$Len  = sizeof($Array2D[0]);
	$Len2 = sizeof($Array2D);


	$Output ='<table style="border-collapse" border="1" cellpadding="10" >'."\n".
		'	<tr style="background-color:#333; color:#eee" >';
	$Output .='<th>-</th>';

	foreach ($Array2D[0] as $key=> $value ) {
		$Output .='<th>'.$key.'</th>';
	}

	$Output.="\n".'	</tr>'."\n";

	for ($i = 1; $i< $Len2 ; $i++ ) {
		$Row = $Array2D[$i];
		
		if($i%2){
			$Output .= '	<tr style="background-color:#eee; color:#555" >';
		}else{
			$Output .= '	<tr style="background-color:#aaa; color:#555" >';
		}
		
		$Output .='<th>'.$i.'</th>';			
		foreach ( $Row as $key=> $value ) {
			$Output .='		<td>'.$value.'</td>'."\n";
		}

		$Output .= '</tr>';
	}

	$Output.='</table>';

	return $Output;
}

function exists_in_table($value,$table,$col){
	global $PDO; 
	
	$query = "SELECT $col
	FROM $table 
	WHERE username= ? ";
	$pds = $PDO->prepare($query); 
	
	$pds->execute(/****/ array($val) ); 

	$R = $pds->fetchAll(2); 
	return sizeof($R);	
}

function createInput($name,$class,$class2,$req,$prob_val,$id=""){
	if(isset($req[$name])){
		$VALUe= $req[$name];
	}else{
		$VALUe = "";
	}

	if(in_array($name, $prob_val)){
		$BClass = " $class2";
	}else{
		$BClass = "";
	}

	if(""!=trim($id)){
		$ID= " id=\"$id\" ";
	}else{
		$ID = "";
	}
	
	$Str = '<input class="'.$class.$BClass.'" name="'.$name.'"'.
	' type="text" value="'.$VALUe.'"'.$ID.' />';

	return $Str;
}

function checkEmptyFields($Req){
	$Out=[];
	foreach ($Req as $key => $value) {
		if("" ==  trim($value)){
			$Out[] = $key;
		}
	}
	return $Out;
}

//
function isValidMail($Str){
	$ret = preg_match('/[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+/', $Str);
	return $ret;
}

function getKeys($Array,$varname=""){
	$ret = [];
	if(is_array($Array)){
		$K =array_keys($Array);
		foreach ($K as $key => $value) {
			$ret[] = $varname.'["'.$key.'"];';
		}
		return implode("<br>\n", $ret);
	}else{
		return "";
	}
}

function MyFunctions(){

$Unix ='convertURL($stuff)
pretty_r($stuff)
showPrepedQuery ($query, $stuff)
getPrepedQuery ($query, $stuff)
escape_sql($str)
file_log($stuff)
capturePOST1($Filename)
capturePOST($Filename)
verifyPOST($index)
substract($str, $frmStr)
table_view($Array2D)
exists_in_table($value,$table,$col)
createInput($name,$class,$class2,$req,$prob_val,$id="")
checkEmptyFields($Req)
isValidMail($Str)
';
$Unix = str_replace("\n",'<br />',$Unix);
echo $Unix;
}