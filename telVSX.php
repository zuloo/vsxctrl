<?php
require_once "PHPTelnet.php";

$commands = array();
$md = 2.8;
if(isset($_POST['c']))
    $commands = explode("|",$_POST['c']);
elseif(isset($_GET['c']))
    $commands = explode("|",$_GET['c']);

$telnet = new PHPTelnet();

// if the first argument to Connect is blank,
// PHPTelnet will connect to the local host via 127.0.0.1
$result = $telnet->Connect('pan.lan',23,null,null);

if ($result == 0) {
    $doneSomething = false;
    if(sizeof($commands) > 0){
        echo '# entering Comand Mode |';
        foreach($commands as $c){
            echo '# executing '.$c.' |';
            $ca = explode('(',$c);
            $ca[1] = substr($ca[1],0,-1);
            switch($ca[0]){
                case 'setBalance':
                    setBalance($telnet, $ca[1]);
                    $doneSomething = true;
                    break;
                case 'setVolume':
                    setVolume($telnet, $ca[1]);
                    $doneSomething = true;
                    break;
                case 'switchMute':
                    setMute($telnet,null);
                    $doneSomething = true;
                    break;
                case 'muteOn':
                    setMute($telnet,0);
                    $doneSomething = true;
                    break;
                case 'muteOff':
                    setMute($telnet,1);
                    $doneSomething = true;
                    break;
                case 'getInfo':
                    getInfo($telnet);
                    $doneSomething = true;
                    break;
            }
        }
    }
    if(!$doneSomething){
        $spString = 'L__,R__,C__,SL_,SR_';
        $spArray = explode(",",$spString);
        foreach($spArray as $sp){
            $telnet->DoCommand('?'.$sp.'CLV', $result);
            echo str_replace(array("\n","\r"),array("",""),$result).'|';
        }
        $telnet->DoCommand('?V', $result);
        echo str_replace(array("\n","\r"),array("",""),$result).'|';
    } 
// say Disconnect(0); to break the connection without explicitly logging out
$telnet->Disconnect();
}

function getInfo($telnet){
    $r = null;
    $telnet->DoCommand('?FL',$r);
    echo decodeFL($r);
}

function decodeFL($fl){
    if(strpos($fl,"FL") !== 0)
        return '';
    
    $r = '= ';
    $ca = str_split($fl,2);
    foreach($ca as $c){
        if($c != "FL")
            $r.= chr(hexdec($c));
    }
    return $r.' |';
}

function setMute($telnet,$switch){
    if(is_null($switch)){
        $r = null;
        $telnet->DoCommand('?M',$r);
        if(strpos($r,'MUT0') !== false)
            $switch = 1;
        elseif(strpos($r,'MUT1') !== false)
            $switch = 0;
    }
    if($switch !== 0 && $switch !== 1)
        return;
    
    echo '* setting Mute : ';
    
    $r = null;
    $telnet->DoCommand(($switch == 1)?'MF':'MO',$r);
    echo str_replace(array("\n","\r"),array("",""),$r).' |';
}

function setVolume($telnet,$args){
    if(!is_numeric($args) || ($args < 0) || ( $args > 185))
        return;
    
    echo '* setting Volume : ';
    while(strlen($args) < 3)
        $args = "0".$args;
    
    $r = null;
    $telnet->DoCommand($args.'VL', $r);
    echo str_replace(array("\n","\r"),array("",""),$r).' |';
}

function setBalance($telnet,$args){
    $args = explode(',',$args);
    if(sizeof($args) != 4){
        return;
    }
    foreach($args as $arg){
        if(!is_numeric($arg) || ($arg < -1) || ($arg > 1)){
            return;
        }
    }
    $md = eDist(1,1,-1,-1);
    echo '* setting Balance : ';
    $r = null;
    $sX = $args[0];
    $sY = $args[1];
    $emphC = $args[2]*36;
    echo "# $md |";
    $level = array();
    $level['L__'] = 26 + round((eDist(-1,-1,$sX,$sY)/$md)*48);
    $level['R__'] = 26 + round((eDist(1,-1,$sX,$sY)/$md)*48);
    $level['SL_'] = 26 + round((eDist(-1,1,$sX,$sY)/$md)*48);
    $level['SR_'] = 26 + round((eDist(1,1,$sX,$sY)/$md)*48);
    $level['C__'] = round(($level['L__']+$level['R__'])/2);
    
    $emphLevel = $level['C__']+$emphC;
    $emph = (($emphLevel < 26) ? $emphLevel-26 : (($emphLevel > 74 ) ? $emphLevel -74: 0))*(-1);
    echo "# emph = $emph , emphC = $emphC";
    foreach($level as $ch => $l){
        $level[$ch] = min(max($l+(($ch == 'C__')?$emphC:$emph),26),74);
        echo $ch.":".$level[$ch];
    }
    
    
    $first = 0;
    foreach($level as $key => $val){
        $telnet->DoCommand($key.$val.'CLV',$r);
        echo (($first++ < 1)?'':', ').str_replace(array("\n","\r"),array("",""),$r);
    }
    echo ' |';
    
}

function eDist($x1,$y1,$x2,$y2){
    $dx = $x2-$x1;
    $dy = $y2-$y1;
    return sqrt($dx*$dx+$dy*$dy);
}

$md = eDist(-1,-1,1,1);

?>
