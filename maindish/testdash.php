<?php
require_once('dbConnect.php');


function getRecent($year = null, $month =null,$countryid= null)
{
    global $db;
    $filter = null;
    if(!empty($year))
    {
        $filter = " where year={$year}";
    }
   
    if(!empty($month))
    {
        if(empty($filter))
        {
            $filter = " where month={$month}";
        }
        else
        {
            $filter = $filter." and  month={$month}";
        }
    }

    
    if(!empty($countryid))
    {
        if(empty($filter))
        {
            $filter = " where country_id={$countryid}";
        }
        else
        {
            $filter = $filter." and  country_id={$countryid}";
        }
    }
    

    //die("SELECT * FROM `testdata_view` {$filter} order by id desc limit 1");
    $data = $db->exec("SELECT * FROM `testdata_view` {$filter} order by id desc limit 1");
    if(count($data) > 0)
    {
        return $data[0];
    }

    return null;
}


function summary($year,$month,$countryid)
{
    global $db;
    $year = empty($year) ? '': " and year={$year}";
    $month = empty($month) ? '': " and month={$month}";
    $country = empty($countryid) ? '' : " and country_id={$countryid}";
    
     $data = $db->exec("select 
     ifnull(sum(no_samples_receive),0) as total,
     ifnull(sum(no_samples_reject),0) as rej,
     ifnull(sum(no_samples_analyzed),0) as tested,
     ifnull(sum(no_positive_samples),0) as pos, 
     (ifnull(sum(no_samples_analyzed),0) - ifnull(sum(no_positive_samples),0)) as neg,
     ifnull(sum(resultsready_ontime),0) as time, 
     ifnull(sum(no_invalid_series),0) as inv,
     ifnull(count( distinct facility_id),0) as labs
     FROM `testdata_view` where 1=1 {$month} {$year} {$country} ");
     
    if(count($data) > 0)
    {
        return $data[0];
    }

    return array();
}



function labResult($providerid,$roundid,$countryid,$type='nat_retest')
{
    global $db;
    $providerid = empty($providerid) ? 0: $providerid;
    $roundid = empty($roundid) ? 0 : $roundid;
    $countryfilter = empty($countryid) ? '' : " and country_id={$countryid}";

    $data = $db->exec("SELECT (select count(1) FROM `eqa_view` where result_submitted =1 and providers_id={$providerid} and round_id={$roundid} {$countryfilter} and type='{$type}') as submitted, (select count(1) FROM `eqa_view` where  result_submitted =0 and providers_id={$providerid} and round_id={$roundid} {$countryfilter} and type='{$type}') as notsubmitted");
    if(count($data) > 0)
    {
        return $data[0];
    }

    return array();
}

function eqa($providerid,$roundid,$countryid,$type='nat_retest')
{
    global $db;
    $providerid = empty($providerid) ? 0: $providerid;
    $roundid = empty($roundid) ? 0 : $roundid;
    $countryfilter = empty($countryid) ? '' : " and country_id={$countryid}";

    $data = $db->exec("SELECT (select count(1) FROM `eqa_view` where is_enrolled_pt =1 and providers_id={$providerid} and round_id={$roundid} {$countryfilter} and type='{$type}') as nat, (select count(1) FROM `eqa_view` where is_enrolled_Intpt = 1  and providers_id={$providerid} and round_id={$roundid} {$countryfilter} and type='{$type}') as inteqa");
    if(count($data) > 0)
    {
        return $data[0];
    }

    return array();
}

function supervised($providerid,$roundid,$countryid,$type='nat_retest')
{
    global $db;
    $providerid = empty($providerid) ? 0: $providerid;
    $roundid = empty($roundid) ? 0 : $roundid; 
    $countryfilter = empty($countryid) ? '' : " and country_id={$countryid}";

    $passmark = getPassMark($providerid,$roundid);
    $data = $db->exec("SELECT count(1) as sup FROM `eqa_view` where LENGTH(result) > 0 and result < $passmark and providers_id={$providerid} and round_id={$roundid} {$countryfilter} and type='{$type}'");
    
    if(count($data) > 0)
    {
        return $data[0]['sup'];
    }

    return 0;
}
function labs($providerid,$roundid,$countryid,$type='nat_retest')
{
    global $db;
    $providerid = empty($providerid) ? 0: $providerid;
    $roundid = empty($roundid) ? 0 : $roundid;
    $countryfilter = empty($countryid) ? '' : " and country_id={$countryid}";

    $data = $db->exec("SELECT count(1) as total FROM `eqa_view`  where providers_id={$providerid} and round_id={$roundid} {$countryfilter} and type='{$type}'");
    if(count($data) > 0)
    {
        return $data[0]['total'];
    }

    return 0;
}

function resultcompare($providerid,$roundid,$countryid,$type='nat_retest')
{
    global $db;
    $providerid = empty($providerid) ? 0: $providerid;
    $roundid = empty($roundid) ? 0 : $roundid;
    $countryfilter = empty($countryid) ? '' : " and country_id={$countryid}";

    $data = $db->exec("SELECT facilityname,result,country FROM `eqa_view`  where providers_id={$providerid} and round_id={$roundid} {$countryfilter} and type='{$type}'");
    if(count($data) > 0)
    {
        return $data;
    }

    return array();

}

function methodCompare($passmark,$providerid,$roundid,$countryid,$type='nat_retest')
{
    global $db;
    $providerid = empty($providerid) ? 0: $providerid;
    $roundid = empty($roundid) ? 0 : $roundid;
    $countryfilter = empty($countryid) ? '' : " and country_id={$countryid}";
    
    $data = $db->exec("SELECT count(1) as labs,method, avg(result) as avgresult,(select count(1) from eqa_view eq where eq.result >= {$passmark} and eq.method_id = eqa_view.method_id and eq.providers_id={$providerid} and eq.round_id={$roundid} {$countryfilter}  and type='{$type}') as passed   FROM `eqa_view`
    where providers_id={$providerid} and round_id={$roundid} {$countryfilter} and type='{$type}' 
    group by method,method_id order by count(1) desc");

    if(count($data) > 0)
    {
        return $data;
    }

    return array();


}

function regionCompare($providerid,$roundid,$countryid,$type='nat_retest')
{
    global $db;
    $providerid = empty($providerid) ? 0: $providerid;
    $roundid = empty($roundid) ? 0 : $roundid;
    $countryfilter = empty($countryid) ? '' : " and country_id={$countryid}";

    $data = $db->exec("SELECT avg(result) as regavg, region,country FROM `eqa_view`  where providers_id={$providerid} and round_id={$roundid} {$countryfilter} and type='{$type}' group by region,country order by avg(result) desc");
    if(count($data) > 0)
    {
        return $data;
    }

    return array();

}

function regiontotalCompare($providerid,$roundid,$countryid,$type='nat_retest')
{
    global $db;
    $providerid = empty($providerid) ? 0: $providerid;
    $roundid = empty($roundid) ? 0 : $roundid;
    $countryfilter = empty($countryid) ? '' : " and country_id={$countryid}";

    $sql= "SELECT count(1) as regtotal, region,country FROM `eqa_view`  where providers_id={$providerid} and round_id={$roundid} {$countryfilter} and type='{$type}' group by region,country order by count(1) desc";

    if(empty($countryid))
    {
        $sql= "SELECT count(1) as regtotal, country as region FROM `eqa_view`  where providers_id={$providerid} and round_id={$roundid} {$countryfilter} and type='{$type}' group by country order by count(1) desc";

    }
 

    $data = $db->exec($sql);
    if(count($data) > 0)
    {
        return $data;
    }

    return array();

}

function roundCompare($providerid,$countryid,$type='nat_retest')
{
    global $db;
    $providerid = empty($providerid) ? 0: $providerid;
    $countryfilter = empty($countryid) ? '' : " and country_id={$countryid}";

    $data = $db->exec("SELECT facility_id, facilityname,result,round,round_id,year FROM `eqa_view`  where providers_id={$providerid} {$countryfilter} and type='{$type}' order by year asc");
    if(count($data) > 0)
    {
        return $data;
    }

    return array();

}

function failureReasons($providerid,$roundid,$countryid,$type='nat_retest')
{
    global $db;
    $providerid = empty($providerid) ? 0: $providerid;
    $roundid = empty($roundid) ? 0 : $roundid;
    $countryfilter = empty($countryid) ? '' : " and country_id={$countryid}";

    $data = $db->exec("SELECT count(1) as cnt,`reason` FROM `eqa_view` WHERE reason_id > 0  and providers_id={$providerid} and round_id={$roundid} {$countryfilter} and type='{$type}' group by reason order by count(1) desc");
    if(count($data) > 0)
    {
        return $data;
    }

    return array();

}

function fetchLabsForMap($providerid,$roundid,$countryid,$dtype='nat_retest')
{
    global $db;
    $providerid = empty($providerid) ? 0: $providerid;
    $roundid = empty($roundid) ? 0 : $roundid;
    $countryfilter = empty($countryid) ? '' : " and country_id={$countryid}";
    $sql = "SELECT facilityname as name ,latitude as lat,longitude as lon FROM `eqa_view` where latitude is not null and  longitude is not null and  providers_id={$providerid} and round_id={$roundid} {$countryfilter} and type='{$dtype}' order by facilityname asc";

    $data = $db->exec($sql);
    return $data;
}


function fetchSumDetails($type,$providerid,$roundid,$countryid,$dtype='nat_retest')
{
    global $db;
    $providerid = empty($providerid) ? 0: $providerid;
    $roundid = empty($roundid) ? 0 : $roundid;
    $countryfilter = empty($countryid) ? '' : " and country_id={$countryid}";

   

    $sql ="";
    if($type == 'PASSED')
    {
        $passmark = getPassMark($providerid,$roundid);
        $sql = "SELECT facilityname,result,country FROM `eqa_view` where result >= $passmark and providers_id={$providerid} and round_id={$roundid} {$countryfilter} and type='{$dtype}' order by result desc";
    }

    if($type == 'FAILED')
    {
        $passmark = getPassMark($providerid,$roundid);
        $sql = "SELECT facilityname,result,country FROM `eqa_view` where  LENGTH(result) > 0 and result < $passmark and providers_id={$providerid} and round_id={$roundid} {$countryfilter} and type='{$dtype}' order by result desc";
    }

    if($type == 'SUBMITTED')
    {
        $sql = "SELECT facilityname,result,country FROM `eqa_view` where LENGTH(result) > 0 and result_submitted = 1 and providers_id={$providerid} and round_id={$roundid} {$countryfilter} and type='{$dtype}' order by result desc";
    }

    if($type == 'NOT SUBMITTED')
    {
        $sql = "SELECT facilityname,reason,country FROM `eqa_view` where  result_submitted = 0  and providers_id={$providerid} and round_id={$roundid} {$countryfilter} and type='{$dtype}' order by facilityname asc";
    }

    if($type == 'LOCAL EQA LABS')
    {
        $sql = "SELECT facilityname,country FROM `eqa_view` where is_enrolled_pt= 1 and providers_id={$providerid} and round_id={$roundid} {$countryfilter} and type='{$dtype}' order by facilityname asc";
    }
    if($type == 'INTERNATIONAL EQA LABS')
    {
        $sql = "SELECT facilityname,country FROM `eqa_view` where is_enrolled_Intpt= 1 and providers_id={$providerid} and round_id={$roundid} {$countryfilter} and type='{$dtype}' order by facilityname asc";
    }
    if($type == 'SUPERVISED LABS')
    {
        $passmark = getPassMark($providerid,$roundid);
        $sql = "SELECT facilityname,country,corrective_action FROM `eqa_view` where  LENGTH(result) > 0 and result < $passmark and providers_id={$providerid} and round_id={$roundid} {$countryfilter} and type='{$dtype}' order by facilityname asc";
    }

    if($type == 'TOTAL LABS')
    {
        $sql = "SELECT facilityname,country FROM `eqa_view` where providers_id={$providerid} and round_id={$roundid} {$countryfilter} and type='{$dtype}' order by facilityname asc";
    }
    $data = $db->exec($sql);
    if(count($data) > 0)
    {
        return $data;
    }

    return array();

}


function getColor($value)
{
    if($value <= 50)
    {
        return 'bg-red';
    }
    else if ($value > 50 && $value <= 75)
    {
        return 'bg-orange';
    }
    else
    {
        return 'bg-green';
    }
}


