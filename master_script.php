<?php

include "bootstrap.php";
header('Content-Type: text/plain');
?>
#!/bin/sh
rm /tmp/blocked_list.txt
wget -q -O /tmp/blocked_list.txt http://192.168.1.10/blocked.php
module_exists=`lsmod | grep ipt_mac`
if [ -z "$module_exists" ];then
    insmod ipt_mac
fi
#Deleting the old table
old_mac=`iptables -L | egrep "..:..:..:..:..:.."  | sed "s/.*\(..:..:..:..:..:..\).*/\1/"`
for mac in $old_mac;do
    iptables -D FORWARD -p tcp -m mac --mac-source $mac -j REJECT --reject-with tcp-reset
done
#Adding the table again
for mac in `cat /tmp/blocked_list.txt`;do
    iptables -I FORWARD -p tcp -m mac --mac-source $mac -j REJECT --reject-with tcp-reset
done
<?php
echo $kermit->access->script();
