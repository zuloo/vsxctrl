<?php
require_once "PHPTelnet.php";

$telnet = new PHPTelnet();

// if the first argument to Connect is blank,
// PHPTelnet will connect to the local host via 127.0.0.1
$result = $telnet->Connect('pan.lan',null,null);

if ($result == 0) {
    /*
    $spString = "L__,R__,C__,SL_,SR_";
    $spArray = split(",",$spString);
    foreach($spArray as $sp){
        $telnet->DoCommand('?'.$sp.'CLV', $result);
        echo nl2br($result,true);
    }
    */
    setBalance($telnet,0,-0.5,-1,0);
// say Disconnect(0); to break the connection without explicitly logging out
$telnet->Disconnect();
}

function setBalance($telnet,$posX, $posY, $centerEmphasis = 0, $subwooferEmphasis = 0){
    $r = null;
    $shiftX = round($poxX * 12);
    $shiftY = round($posY * 12);
    $level = array();
    $level['L__'] = 50 + $shiftX + $shiftY;
    $level['R__'] = 50 - $shiftX + $shiftY;
    $level['SL_'] = 50 + $shiftX - $shiftY;
    $level['SR_'] = 50 - $shiftX - $shiftY;
    $level['C__'] = 50 + round($centerEmphasis * 12) + $shiftY;
    
    foreach($level as $key => $val){
        $telnet->DoCommand($key.$val.'CLV',$r);
        echo nl2br($r);
    }
    
}



?>
