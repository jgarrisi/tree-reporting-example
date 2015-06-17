<?php namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class ServerReportingController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Home Controller
    |--------------------------------------------------------------------------
    |
    | This controller renders your application's "dashboard" for users that
    | are authenticated. Of course, you are free to change or remove the
    | controller as you wish. It is just here to get your app started!
    |
    */

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('ping');
    }

    /**
     * Show the application dashboard to the user.
     *
     * @return Response
     */
    public function index()
    {
        return view('maintainServerReporting');
    }

    /**
     * Show environments for the EPR ID
     *
     * @return Response
     */
    public function getEnvironments()
    {


        //try to get the EPRID from the text box
        $inputEPR = Request::input('textEPRID');

        if ($inputEPR) {
            $query = "SELECT DISTINCT SR.Environment FROM dbo.ServerReporting SR WHERE SR.EPRID=?";
            $queryResults = DB::select($query, array($inputEPR));
            $environments = [];
            foreach ($queryResults as $result) {
                $environments[] = $result->Environment;
            }

            return json_encode($environments);
        } else {
            $error = "no records found for EPR-ID" . $inputEPR;
            return -1;
        }
    }

    /**
     * Show servers for the EPR ID and Environment
     *
     * @return Response
     */
    public function getServers()
    {


        //try to get the EPRID from the text box
        $inputEPR = Request::input('textEPRID');
        $inputEnv = Request::input('selEnv');


        if ($inputEPR && $inputEnv) {
            $query = " SELECT DISTINCT SR.ServerName, SR.RIPPlanDate, SR.RIPActualDate, SR.RIPExcludeDate, SR.Status " .
                "  FROM dbo.ServerReporting SR WHERE SR.EPRID=? AND SR.Environment=?";
            $queryResults = DB::select($query, array($inputEPR, $inputEnv));

            return json_encode($queryResults);
        } else {

            return "bad input ==== EPR='" . $inputEPR . "' and ENV='" . $inputEnv . "'";
        }
    }

    /**
     * Show servers for the EPR ID and Environment
     *
     * @return Response
     */
    public function saveServerSet()
    {


        //try to get the EPRID from the text box
        $inputRows = Request::input('rowsToUpdate');
        $updateStatement = "UPDATE dbo.ServerReporting SET RIPPlanDate = NULLIF(?,''), RIPActualDate = NULLIF(?,''), RIPExcludeDate = NULLIF(?,''), Status=?  WHERE ServerName=? ";

        if ($inputRows) {
//            print_r($inputRows);
            $affectedRows=0;
            foreach($inputRows as $row) {
                //print_r($row);
//                ($row['RIPPlanDate']=='')? null : $row['RIPPlanDate'];
//                ($row['RIPActualDate']=='')? 'NULL' : $row['RIPActualDate'];
//                ($row['RIPExcludeDate']=='')? null : $row['RIPExcludeDate'];

                $updateResults = DB::update($updateStatement, array($row['RIPPlanDate'],$row['RIPActualDate'],$row['RIPExcludeDate'], $row['Status'], $row['ServerName']));
                if ($updateResults <= 0) {
                    return "update failed!";
                }
                $affectedRows += $updateResults;
            }

            return json_encode($affectedRows);
        } else {

            return "bad input ==== ".$inputRows;
        }
    }

    public function getCSV()
    {

         // output the column headings
        $output =  implode(',', array(
        'ServerName'
        ,'HostName'
        ,'ServerType'
        ,'ServerStatus'
        ,'OSName'
        ,'AppCIName'
        ,'DeviceFunction'
        ,'Site'
        ,'Cell'
        ,'Pod'
        ,'Environment'
        ,'Company'
        ,'ServerOrService'
        ,'RIPPlanDate'
        ,'Status'
        ,'EPRID'
        ,'RIPActualDate'
        ));
        $output .= "\n";

        // fetch the data
        $rows = DB::select('SELECT * FROM SERVERREPORTING');

        // loop over the rows, outputting them
        foreach($rows as $row){
            //print_r($row);
            $array = json_decode(json_encode($row), true);


            //print_r($array);
//            $output .= implode(",",
//                array(
//                    $row['ServerName'],
//                    $row['HostName'],
//                    $row['ServerType'],
//                    $row['ServerStatus'],
//                    $row['OSName'],
//                    $row['AppCIName'],
//                    $row['DeviceFunction'],
//                    $row['Site'],
//                    $row['Cell'],
//                    $row['Pod'],
//                    $row['Environment'],
//                    $row['Company'],
//                    $row['ServerOrService'],
//                    $row['RIPPlanDate'],
//                    $row['Status'],
//                    $row['EPRID'],
//                    $row['RIPActualDate']
//                  ));

            $output .= implode(",", $array);
            $output .= "\n";

        }

        $headers = array(
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=data.csv',
        );

        return Response::make(rtrim($output, "\n"), 200 , $headers);
    }

}
