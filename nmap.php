<?


echo $out = shell_exec('uptime');
flush();
echo $out = shell_exec('met');
flush();

$out = shell_exec('nmap -sP 192.168.0-1.*');
echo $out;
#$out2 = str_replace("\n","", $out); 

?>