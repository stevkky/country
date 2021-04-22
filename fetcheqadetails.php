<?php
include_once('maindish/eqadash.php');
include_once('lang.php');
$type = $_REQUEST['type'];
$providerid = $_REQUEST['providerid'];
$roundid = $_REQUEST['roundid'];
$countryid = $_REQUEST['countryid'];
$dtype = $_REQUEST['dtype'];
$data = fetchSumDetails($type,$providerid,$roundid,$countryid,$dtype);

if(!$data || !is_array($data) || count($data) == 0)
{
    $noData = lang('general.NO_DATA');
    die("$noData");
}

$out = '<table class="table table-hover">';
$Tdown = '</tbody></table>';



switch($type)
{
    case 'PASSED':
    case 'FAILED':
    case 'SUBMITTED':
        $out = $out.'<thead>
        <tr>
            <th>#</th>
            <th>'.lang('general.COUNTRY').'</th>
            <th>'.lang('general.LAB').'</th>
            <th>'.lang('dataEntry.RESULT').'</th>
        </tr>
        </thead>
        <tbody>';
        for($i=0;$i<count($data);$i++)
        {
            $row = $data[$i];
            $out = $out.'<tr><td>'.($i+1).'</td>';
            $out = $out.'<td>'.$row['country'].'</td>';
            $out = $out.'<td>'.$row['facilityname'].'</td>';
            $out = $out.'<td>'.$row['result'].'</td></tr>';
        }
        $out= $out.$Tdown;
        break;
    case 'NOT SUBMITTED':
        $out = $out.'<thead>
        <tr>
            <th>#</th>
            <th>'.lang('general.COUNTRY').'</th>
            <th>'.lang('general.LAB').'</th>
            <th>'.lang('general.REASON').'</th>
        </tr>
        </thead>
        <tbody>';
        for($i=0;$i<count($data);$i++)
        {
            $row = $data[$i];
            $out = $out.'<tr><td>'.($i+1).'</td>';
            $out = $out.'<td>'.$row['country'].'</td>';
            $out = $out.'<td>'.$row['facilityname'].'</td>';
            $out = $out.'<td>'.$row['reason'].'</td></tr>';
        }
        $out= $out.$Tdown;
        break;
    case 'LOCAL EQA LABS':
    case 'INTERNATIONAL EQA LABS':
    case 'TOTAL LABS':
        $out = $out.'<thead>
        <tr>
            <th>#</th>
            <th>'.lang('general.COUNTRY').'</th>
            <th>'.lang('general.LAB').'</th>
        </tr>
        </thead>
        <tbody>';
        for($i=0;$i<count($data);$i++)
        {
            $row = $data[$i];
            $out = $out.'<tr><td>'.($i+1).'</td>';
            $out = $out.'<td>'.$row['country'].'</td>';
            $out = $out.'<td>'.$row['facilityname'].'</td>';
        }
        $out= $out.$Tdown;
    break;
    case 'SUPERVISED LABS':
        $out = $out.'<thead>
        <tr>
            <th>#</th>
            <th>'.lang('general.COUNTRY').'</th>
            <th>'.lang('general.LAB').'</th>
            <th>'.lang('general.DOC_CORRECTION').'</th>
        </tr>
        </thead>
        <tbody>';
        for($i=0;$i<count($data);$i++)
        {
            $row = $data[$i];
            $out = $out.'<tr><td>'.($i+1).'</td>';
            $out = $out.'<td>'.$row['country'].'</td>';
            $out = $out.'<td>'.$row['facilityname'].'</td>';
            $out = $out.'<td>'.lang('general.'.$row['corrective_action']).'</td>';
        }
        $out= $out.$Tdown;
    break;
    default;
        $notFound = lang('general.NOT_FOUND');
        die($type. "$notFound");
}

die($out);
