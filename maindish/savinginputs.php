<?php 
require('dbConnect.php');
function login ($email,$password)
{
     global $db;
     $obj =new DB\SQL\Mapper($db,'user_login');
     $obj->load(array('user_email=? and user_password=?', $email,$password));
    
     if($obj->dry())
     {
          return false;
     }
     $_SESSION['user_email'] = $email;
     $_SESSION['user_type'] = $obj->user_type;
     $_SESSION['countryid'] = (empty($obj->countryid) || $obj->countryid == 0 )? null : $obj->countryid;
    
     return  true;
}

function getMonthName($number)
{
     $months = array (1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec');
     return $months[$number];
}
function saveSimpleSetup($table,$id=null)
{
    global $db;
    if(isset($_POST['submit'])){
        $name = $_POST['name'];
        $isactive = $_POST['isactive'];

       
        if(empty($id))
        {
            $obj =new DB\SQL\Mapper($db,$table);
        }
        else
        {
            $obj =  getSimpleSetup($table, $id);
        }

        $obj->name=$name;
        $obj->is_active =$isactive;

        if(!empty($_POST['regionid']))
        {
             $obj->region_id  =$_POST['regionid'];

        }

        if(!empty($_POST['yearid']))
        {
             $obj->year  =$_POST['yearid'];

        }

        if(isset($_POST['year']))
        {
             $obj->year  =$_POST['year'];

        }
        if(!empty($_POST['iso']))
        {
             $obj->iso  =$_POST['iso'];

        }

        if(!empty($_POST['month']))
        {
             $obj->month  =$_POST['month'];

        }

        if(!empty($_POST['districtid']))
        {
             $obj->district_id  =$_POST['districtid'];

        }

        if(isset($_POST['result']))
        {
             $obj->result  =$_POST['result'];

        }
         
        if(!empty($_POST['frmtype']))
        {
             $obj->reason_id  = empty($_POST['reasonid'])? null : $_POST['reasonid'];
        }
        else
        {
          if(!empty($_POST['reasonid']))
          {
               $obj->reason_id  = $_POST['reasonid'];
  
          }
        }

        if(!empty($_POST['facilityid']))
        {
             $obj->facility_id  =$_POST['facilityid'];

        }

        if(!empty($_POST['methodid']))
        {
             $obj->method_id  =$_POST['methodid'];

        }

        if(!empty($_POST['facilitytypeid']))
        {
             $obj->type_id  =$_POST['facilitytypeid'];

        }

        if(!empty($_POST['countryid']))
        {
             $obj->countryid  =$_POST['countryid'];

        }

        

        if(!empty($_POST['longitude']))
        {
             $obj->longitude  =$_POST['longitude'];

        }

        if(!empty($_POST['latitude']))
        {
             $obj->latitude  =$_POST['latitude'];

        }

        if(!empty($_POST['email']))
        {
               $obj->user_email  =$_POST['email'];

        }

        if(!empty($_POST['password']))
        {
               $password = $_POST['password'];
               $obj->user_password  =$password;

        }

        if(!empty($_POST['phone']))
        {
             $obj->user_phone  =$_POST['phone'];

        }

        if(!empty($_POST['usertype']))
        {
             $obj->user_type  =$_POST['usertype'];

        }
        
        if(!empty($_POST['roundid']))
        {
             $obj->round_id  =$_POST['roundid'];

        }

        if(!empty($_POST['countryid']))
        {
             $obj->country_id  =$_POST['countryid'];

        }

        if(!empty($_POST['providerid']))
        {
             $obj->providers_id  =$_POST['providerid'];
        }
        
        if(!empty($_POST['passmark']))
        {
             $obj->mark  =$_POST['passmark'];

        }

        if(isset($_POST['samplesreceived']))
        {
             $obj->no_samples_receive  =$_POST['samplesreceived'];

        }

        if(isset($_POST['rejectedsamples']))
        {
             $obj->no_samples_reject  =$_POST['rejectedsamples'];

        }

        if(isset($_POST['invalidseries']))
        {
             $obj->no_invalid_series  =$_POST['invalidseries'];

        }
        
        if(isset($_POST['analyzedsamples']))
        {
             $obj->no_samples_analyzed  =$_POST['analyzedsamples'];

        }

        if(isset($_POST['positivesamples']))
        {
             $obj->no_positive_samples =$_POST['positivesamples'];

        }

        if(!empty($_POST['monthid']))
        {
             $obj->month  =$_POST['monthid'];

        }

        if(isset($_POST['issupervised']))
        {
             $obj->is_supervised  =$_POST['issupervised'];
        }

        if(isset($_POST['isenrolledpt']))
        {
             $obj->is_enrolled_pt  =$_POST['isenrolledpt'];

        }

        if(isset($_POST['isenrolledintpt']))
        {
             $obj->is_enrolled_Intpt  =$_POST['isenrolledintpt'];

        }

        if(isset($_POST['isresultsubmitted']))
        {
             $obj->result_submitted  =$_POST['isresultsubmitted'];

        }

        if(!empty($_POST['resultreadyontime']))
        {
             $obj->resultsready_ontime  =$_POST['resultreadyontime'];
        }

        if(!empty($_POST['type']))
        {
             $obj->type  =$_POST['type'];
        }

        if(!empty($_SESSION['countryid']) && $_SESSION['countryid'] > 0)
        {
          $obj->countryid  = $_SESSION['countryid'];
        }

        if(!empty($_POST['corrective_action']))
        {
             $obj->corrective_action  =$_POST['corrective_action'];
        }
      
      

        $obj->Save();
    }
}

function getSimpleSetup($table, $id=null)
{
    global $db;
    $obj =new DB\SQL\Mapper($db,$table);
    if(empty($id))
    {
        $obj->load();
    }
    else
    {
        $obj->load(array('id=?', $id));
    }
    
    return $obj;
}

function getData($table , $filter = array())
{
     global $db;
    $obj =new DB\SQL\Mapper($db,$table);
    if(empty($filter))
    {
        $obj->load();
    }
    else
    {
        $obj->load($filter);
    }
    
    return $obj;
}
?>