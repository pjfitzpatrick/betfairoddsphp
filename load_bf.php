<?php
ini_set("memory_limit","256M");
date_default_timezone_set('UTC');

include_once "betfairfuncs.php";

$marketmap_str = file_get_contents("marketmap_adjusted.json");
$marketmap=json_decode($marketmap_str,true);

$depth_list=Array(1,2,3);
$bl_list=Array("b","l");
$os_list=Array("o","s");

$tmpdate = time();
$mysqldate_now = date("Y-m-d H:i:s",$tmpdate);
$ts_int = date("YmdHis",$tmpdate);

$ch = curl_init(INSERT URL YOU GET FROM BETFAIR HERE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,200);
curl_setopt($ch, CURLOPT_HEADER, 0);
$ret_map=array();

$data = curl_exec($ch);
curl_close($ch);

if ($data===false) {
  echo "error";
}
else {
  $event_arr=explode("<event", $data);
  unset($event_arr[0]);  
  foreach ($event_arr as $event) {
    $marketid_ft="-1";
    $inner_arr = getBfArr($event);  $menuPath = $inner_arr[1]; $subevent_arr=explode("<subevent",$event);       
    $fixture_position = strstr($menuPath, 'Fixture'); 
    $oddsMap=array();
    if ($fixture_position==FALSE) {
      $isMatch="N";
    }
    else {
      $isMatch="Y";
    }
    $oddsMap["info"]=array();
    $oddsMap["odds"]=array();
    unset($subevent_arr[0]);    
    if ($fixture_position==TRUE) {     
      $recordsAccepted=0;
      foreach ($subevent_arr as $subevent) {
        $subevent_inner_arr = getBfArr($subevent);
        $title=$subevent_inner_arr[1];
        $datestr=$subevent_inner_arr[3];
        $timestr=$subevent_inner_arr[5]; 
        $phpdate = bfDateToPhp($datestr, $timestr);
        $d1= time(); 
        $hourstoevent = floor(($phpdate-$d1)/3600);
        $mysqldate = date( 'Y-m-d H:i:s', $phpdate );
        $minutestoevent = (($phpdate-$d1)/60);
        $marketid=$subevent_inner_arr[7]; 
        $amtmatched=intval($subevent_inner_arr[9]); 
        $betMarketMapItem = getBetMarketMapItem($title,$menuPath,$marketmap);
        $acceptRecord=acceptRecord($betMarketMapItem, $amtmatched,$hourstoevent);
        if ($acceptRecord==TRUE) {
          $recordsAccepted++;
          $betmarket=$betMarketMapItem["betmarket"]; 
          if ($betmarket=="FT") {
            $marketid_ft=$marketid;  
          }
          $oddsMap["info"][$betmarket]=array();
          $oddsMap["info"][$betmarket]["amtmatched"]=$amtmatched;      
          $oddsMap["info"][$betmarket]["timestr"]=$timestr;      
          $oddsMap["info"][$betmarket]["datestr"]=$datestr;      
          $oddsMap["info"][$betmarket]["ids"]=array();
          $oddsMap["info"][$betmarket]["ids"]["marketid"]=$marketid;  
          $oddsMap["info"][$betmarket]["ids"]["selections"]=array();  
          $oddsMap["info"][$betmarket]["title"]=$title;
          $oddsMap["odds"][$betmarket]=array(); 
          $selection_arr=explode("<selection",$subevent);           
          unset($selection_arr[0]);
          foreach ($selection_arr as $key=>$selection) {
            $selection_inner_arr = getBfArr($selection);
            $selectionid = $selection_inner_arr[3];
            $betname=getBetName($betMarketMapItem,$key,$selection_inner_arr[1]); 
            $betkey = $betmarket."_".$betname;    
            $rowcount=3;
            $oddsMap["info"][$betmarket]["ids"]["selections"][$betkey]=$selectionid;
            foreach ($depth_list as $depth) { 
                foreach ($bl_list as $bl) {
                  foreach ($os_list as $os) {
                    $rowcount=$rowcount+2;$oddsname=$bl.$os.$depth;
                    if (array_key_exists("fullOdds",$betMarketMapItem) || ($depth==1 && $os=="o")) {
                      $$oddsname = $selection_inner_arr[$rowcount];
                      $oddsMap["odds"][$betmarket][$betkey][$oddsname]=$selection_inner_arr[$rowcount];
                    }
                  }
                }
              }
            }
        }
      }
      $datetime1=mktime();        
      if ($recordsAccepted>0) {
        $outoddsmap=json_encode($oddsMap["odds"]); 
        $outinfomap=json_encode($oddsMap["info"]); 
        $ret_row= array();
        $ret_row["inpts"]=$mysqldate_now;
        $ret_row["inptsint_gmt"]=$ts_int;
        $ret_row["marketid_ft"]=$marketid_ft;
        $ret_row["menupath"]=$menuPath;
        $ret_row["eventdate"]=$mysqldate;
        $ret_row["tour"]=$tour;      
        $ret_row["oddsmap"]=$oddsMap["odds"];
        $ret_row["infomap"]=$oddsMap["info"];
        $ret_map[]=$ret_row;
      }
     }   
  }
  $ret_map_str=json_encode($ret_map);   
  echo $ret_map_str;
  
  }
?>
