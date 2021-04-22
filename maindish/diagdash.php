<?php
require_once('dbConnect.php');

function getRecent($year = null)
{
    global $db;
    $filter = '';
    if(!empty($year))
    {
        $filter = " where year={$year}";
    }

    $data = $db->exec("SELECT * FROM `testdata_view` {$filter} order by id desc limit 1");
    if(count($data) > 0)
    {
        return $data[0];
    }

    return null;
}
function sampleReceived($id)
{
    global $db;
    $id = empty($id) ? 0: $id;
    //$roundid = empty($roundid) ? 0 : $roundid;

    $data = $db->exec("SELECT no_samples_receive FROM `testdata_view`  where id={$id}");
    if(count($data) > 0)
    {
        return $data[0]['no_samples_receive'];
    }

    return 0;
}
function sampleReject($id)
{
    global $db;
    $id = empty($id) ? 0: $id;
    //$roundid = empty($roundid) ? 0 : $roundid;

    $data = $db->exec("SELECT no_samples_reject FROM `testdata_view`  where id={$id}");
    if(count($data) > 0)
    {
        return $data[0]['no_samples_reject'];
    }

    return 0;
}
function sampleAnalyzed($id)
{
    global $db;
    $id = empty($id) ? 0: $id;
    //$roundid = empty($roundid) ? 0 : $roundid;

    $data = $db->exec("SELECT no_samples_analyzed FROM `testdata_view`  where id={$id}");
    if(count($data) > 0)
    {
        return $data[0]['no_samples_analyzed'];
    }

    return 0;
}
function samplePositive($id)
{
    global $db;
    $id = empty($id) ? 0: $id;
    //$roundid = empty($roundid) ? 0 : $roundid;

    $data = $db->exec("SELECT no_positive_samples FROM `testdata_view`  where id={$id}");
    if(count($data) > 0)
    {
        return $data[0]['no_positive_samples'];
    }

    return 0;
}
function initialSeries($id)
{
    global $db;
    $id = empty($id) ? 0: $id;
    //$roundid = empty($roundid) ? 0 : $roundid;

    $data = $db->exec("SELECT no_invalid_series FROM `testdata_view`  where id={$id}");
    if(count($data) > 0)
    {
        return $data[0]['no_invalid_series'];
    }

    return 0;
}
function facilityResult()
{
    global $db;

    $data = $db->exec("SELECT facilityname,sum(no_samples_receive) as total FROM `testdata_view` group by facility_id ");
    if(count($data) > 0)
    {
        return $data;
    }

    return array();

}
function sampleRejectFacilty()
{
    global $db;

    $data = $db->exec("SELECT facilityname,sum(no_samples_reject) as total FROM `testdata_view` group by facility_id");
    if(count($data) > 0)
    {
        return $data;
    }

    return array();

}
?>