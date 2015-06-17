<?php namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class EPRStatusReportingController extends Controller
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
        return view('maintainEPRStatusReporting');
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
            $query = "SELECT DISTINCT E.Environment FROM dbo.EPRStatus E WHERE E.EPRID=?";
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
    public function getEPRStatuses()
    {


        //try to get the EPRID from the text box
        $inputEPR = Request::input('textEPRID');
        $inputEnv = Request::input('selEnv');


        if ($inputEPR && $inputEnv) {
            $query = " SELECT * " .
                "  FROM dbo.EPRStatus E WHERE E.EPRID=? AND E.Environment=?";
            $queryResults = DB::select($query, array($inputEPR, $inputEnv));

            return json_encode($queryResults);
        } else {

            return "bad input ==== EPR='" . $inputEPR . "' and ENV='" . $inputEnv . "'";
        }
    }

    /**
     * update EPRStatuses for the EPR ID and Environment
     *
     * @return Response
     */
    public function saveEPRStatusSet()
    {


        //try to get the EPRID from the text box
        $inputRows = Request::input('rowsToUpdate');
        $updateStatement = "UPDATE dbo.EPRStatus SET Status=?, TargetDate = NULLIF(?,''), ActualDate = NULLIF(?,''), PlanningComplete=?, DesignComplete=?  WHERE EPRID=? and Environment=? ";

        if ($inputRows) {
//            print_r($inputRows);
            $affectedRows=0;
            foreach($inputRows as $row) {
                //print_r($row);
//                ($row['RIPPlanDate']=='')? null : $row['RIPPlanDate'];
//                ($row['RIPActualDate']=='')? 'NULL' : $row['RIPActualDate'];
//                ($row['RIPExcludeDate']=='')? null : $row['RIPExcludeDate'];

                $tempPlanning = null;
                $tempDesign = null;

                if($row['PlanningComplete']=='true') {
                    $tempPlanning = 1;
                }else{
                    $tempPlanning = 0;
                }

                if($row['DesignComplete']=='true') {
                    $tempDesign = 1;
                }else{
                    $tempDesign = 0;
                }

                ($row['DesignComplete'])? 1 : 0;

                $updateResults = DB::update($updateStatement, array($row['Status'],$row['TargetDate'],$row['ActualDate'], $tempPlanning , $tempDesign , $row['EPRID'], $row['Environment']));
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
        'EPRID'
        ,'Company'
        ,'Environment'
        ,'Status'
        ,'TargetFate'
        ,'ActualDate'
        ,'PlanningComplete'
        ,'DesignComplete'
        ));
        $output .= "\n";

        // fetch the data
        $rows = DB::select('SELECT * FROM dbo.EprStatus');

        // loop over the rows, outputting them
        foreach($rows as $row){
            $array = json_decode(json_encode($row), true);
            $output .= implode(",", $array);
            $output .= "\n";

        }

        $headers = array(
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=eprStatus.csv',
        );

        return Response::make(rtrim($output, "\n"), 200 , $headers);
    }

}
