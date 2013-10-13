<?php


function getInnerStr($inpStr,$str1,$str2) {
  if ($str1==NULL) {
    $arr=explode($str2,$inpStr);
    return $arr[0];
  }
  $arr1=explode($str1,$inpStr); 
  if (count($arr1)>1) {    
    $arr=explode($str2,$arr1[1]);
    return $arr[0];
  }
  else {
    return NULL;
  }
}

function getResultsTeams($inpArr) {
  try {
      $inner_arr = explode("<title>",$inpArr);
      $inner_arr = $inner_arr[1];
      $inner_arr = explode("</title>",$inner_arr);
      $inner_arr = $inner_arr[0];
      $inner_arr = explode(" / ",$inner_arr);
      $inner_arr = $inner_arr[1];
      $inner_arr = explode("-",$inner_arr);
      $inner_arr = $inner_arr[0];
      $inner_arr = explode(" v ",$inner_arr);    
      return $inner_arr;
  }
  catch (Exception $e) {
    return FALSE;
  }
}

function getResultsMatchStr($inpArr) {
  try {
    $inner_arr = explode("<title>",$inpArr);
    $inner_arr = $inner_arr[1];
    $inner_arr = explode("</title>",$inner_arr);
    $inner_arr = $inner_arr[0];
    $inner_arr = explode("-",$inner_arr);
    return $inner_arr[0];
  }
  catch (Exception $e) {
    return FALSE;
  }
}

function getResultsSettledStr($inpArr) {
  try {
    $inner_arr = explode("<title>",$inpArr);
    $inner_arr = $inner_arr[1];
    $inner_arr = explode("</title>",$inner_arr);
    $inner_arr = $inner_arr[0];
    $inner_arr = explode("-",$inner_arr);
    $inner_arr = $inner_arr[1];
    $inner_arr = preg_split("/[0-9]{2}:[0-9]{2} /",$inner_arr);
    return $inner_arr[1];
  }
  catch (Exception $e) {
    return FALSE;
  }
}


function getResultsBfId($inpArr) {
    $inner_arr = explode("marketID=",$inpArr);
    $inner_arr = $inner_arr[1];
    $inner_arr = explode("</link>",$inner_arr);
    return $inner_arr[0];
}

function getResultsRes($inpArr) {
    $inner_arr = explode("Winner(s):",$inpArr);
    $inner_arr = $inner_arr[1];
    $inner_arr = explode("</description>",$inner_arr);

    return $inner_arr[0];
}


function getBetName($betMarketMapItem,$key,$currentName) {
  if (count($betMarketMapItem["selectionorder"])==0) {
    return $currentName;
  }
  else {
    return $betMarketMapItem["selectionorder"][$key-1];
  }
}

function getBetMarketMapItem($title,$inpMenuPath,$marketmap) {
  if (array_key_exists($title,$marketmap)) {
    return $marketmap[$title];
  }
  else {
  $myarr=explode("/",$inpMenuPath);
  $stopidx=-1;
  $matchid=FALSE;
  $tour = FALSE;
  foreach ($myarr as $key=>$menupart) {
    if (substr($menupart, 0, 7)=="Fixture") {
      $stopidx=$key;
    }
  }    
  if ($stopidx!=-1 && array_key_exists($stopidx+1,$myarr)) {
    $team1=explode(" v ",$myarr[$stopidx+1]);$team1=$team1[0];
    $team2=explode(" v ",$myarr[$stopidx+1]);$team2=$team2[1];
   foreach ($marketmap["team"] as $key=>$selection) {
    if (strstr($title, $key)==TRUE) {
     if (strstr($title, $team1)==TRUE) {$team="Team1";} else {$team="Team2";}
     $retArray=array();
     $retArray["betmarket"]=$team.$marketmap["team"][$key]["betmarket"];
     $retArray["selectionorder"]=$marketmap["team"][$key]["selectionorder"];
     return $retArray;
    }
   }
  }
}
    return FALSE;
}

function getBfArr($inpArr) {
    $inner_arr = explode(">",$inpArr);
    $inner_arr = $inner_arr[0];
    $inner_arr = explode("\"",$inner_arr);
    return $inner_arr;
}

function acceptRecord($betMarketMapItem, $inpamtmatched,$inphourstoevent) {
if ($betMarketMapItem==FALSE) {
    return FALSE;
}
else if ($inphourstoevent <1) {
  if (($inpbetmarket=="FT") && ($inpamtmatched>500)) {
	return TRUE;
  }
  else if (($inpbetmarket!="FT") && ($inpamtmatched>100)) {
	return TRUE;
  }
  else {
	return FALSE;
  }
}
else if ($inphourstoevent <12) {
  if (($inpbetmarket=="FT") && ($inpamtmatched>2500)) {
	return TRUE;
  }
  else if (($inpbetmarket!="FT") && ($inpamtmatched>1000)) {
	return TRUE;
  }
  else {
	return FALSE;
  }
}
else if ($inphourstoevent <12) {
  if (($inpbetmarket=="FT") && ($inpamtmatched>2500)) {
	return TRUE;
  }
  else if (($inpbetmarket!="FT") && ($inpamtmatched>1000)) {
	return TRUE;
  }
  else {
	return FALSE;
  }
}
else if ($inphourstoevent <24) {
  if (($inpbetmarket=="FT") && ($inpamtmatched>2500)) {
	return TRUE;
  }
  else if (($inpbetmarket!="FT") && ($inpamtmatched>1000)) {
	return TRUE;
  }
  else {
	return FALSE;
  }
}
else {
if (($inpbetmarket=="FT") && ($inpamtmatched>1000)) {
	return TRUE;
}
else if (($inpbetmarket!="FT") && ($inpamtmatched>500)) {
	return TRUE;
}
else {
	return FALSE;
}
}
}
function bfDateToPhp($inpDate, $inpTime) {
    $datearr = explode("/",$inpDate);
    if (strstr($inpTime, ':')==FALSE) {
     $timearr=array();
     if ($inpTime==NULL) {
     $timearr[0]="00";
	}
     else {
     $timearr[0]=$inpTime;
	}

     $timearr[1]="00";
    }
    else {
    $timearr = explode(":",$inpTime);  
    }
    return mktime($timearr[0]+1,$timearr[1],0,$datearr[1],$datearr[0],$datearr[2]);
}

function bfDateToStr($inpDate) {
    $datearr = explode("/",$inpDate);
    return $datearr[2].$datearr[1].$datearr[0];
}

function getMatchId($inpMenuPath,$inpMatchTourMap) {
  $myarr=explode("/",$inpMenuPath);
  $stopidx=-1;
  $matchid=FALSE;
  $tour = FALSE;
  foreach ($myarr as $key=>$menupart) {
    if (substr($menupart, 0, 7)=="Fixture") {
      $stopidx=$key;
    }
  }    
  if ($stopidx!=-1 && array_key_exists($stopidx+1,$myarr)) {
    $team1=explode(" v ",$myarr[$stopidx+1]);$team1=$team1[0];
    $team2=explode(" v ",$myarr[$stopidx+1]);$team2=$team2[1];
    $keyattempt="";
    foreach ($myarr as $menupart) {
      if ($matchid==FALSE) { 
	if ($keyattempt=="") {
	$keyattempt = $menupart;
	}
	else {
	$keyattempt = $keyattempt."/".$menupart;
	}
	if (array_key_exists($keyattempt,$inpMatchTourMap)) {
	  $tour = $inpMatchTourMap[$keyattempt];
	  $matchid=$tour."/".$team1."/".$team2;
	}
      }
    }
    if ($tour ==FALSE) {
        $matchid="Other/".$team1."/".$team2;
    }
  }
  return $matchid;
}

?>
