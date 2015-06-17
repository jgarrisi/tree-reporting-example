<?php namespace App\Http\Controllers;

use Input;
use App\View;
use DateTime;
use DateInterval;
use Request;
use DB;
use App\Package;
use App\Bench;
use Calendar;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return view('Schedule.schedule');
    }

    public function getEventsByDateRange()
    {
        $query = ' SELECT TOP 1000 BA.PackageID '.
            " ,STUFF((SELECT  ', ' + Location FROM BENCH B, BenchAssign BA WHERE  B.BenchID=BA.BenchID AND BA.PackageID = ? ORDER BY Location FOR XML PATH('')), 1, 1, '') AS locationStr ".
            " ,STUFF((SELECT  ', ' + Platform FROM BENCH B, BenchAssign BA WHERE  B.BenchID=BA.BenchID AND BA.PackageID = ? ORDER BY Platform FOR XML PATH('')), 1, 1, '') AS platformStr".
            " ,STUFF((SELECT  ', ' + Shift FROM BENCH B, BenchAssign BA WHERE  B.BenchID=BA.BenchID AND BA.PackageID = ? ORDER BY shift FOR XML PATH('')), 1, 1, '') AS shiftStr".
            ' FROM BenchAssign BA, Bench B WHERE BA.PackageID= ? AND BA.BenchID=B.BenchID GROUP BY BA.PackageID';
        $events = []; //array to send back events to view
        $viewStartEnd = [];

        //try to get the viewRange
        $viewType = Request::input('viewType', 'no view type');
        $viewRange = Request::input('viewRange', 'no view range');

            //parse view range based on viewType
            switch ($viewType) {

            case 'month':
                // $pieces = explode(" ", $viewRange);
             //    $viewStartEnd[0] = date('Y-m-d', mktime(0, 0, 0, $pieces[1], 1, $pieces[0]));
             //    $viewStartEnd[1] = date('Y-m-t', mktime(23, 59, 59, $pieces[1], 1, $pieces[0]));
                $endOfDay   = strtotime($viewRange) - 1;
                $viewStartEnd[0] = date('Y-m-d', strtotime($viewRange));
                $viewStartEnd[1] = date('Y-m-t', strtotime($viewRange));
                break;

            case 'agendaDay': // never reached because "a" is already matched with 0
                $pieces = explode(' ', $viewRange);
                $viewStartEnd[0] = new DateTime(date('Y-m-d', strtotime($viewRange)));
                $viewStartEnd[1] = new DateTime(date('Y-m-d', strtotime($viewRange)));
                $viewStartEnd[1]->add(new DateInterval('P1D'));
                break;

            default: //agendaWeek or all others
                if ($viewRange == 'no view range') {
                    //view is this week

                    $viewStartEnd[0] = new DateTime('last sunday');
                    $viewStartEnd[1] = clone($viewStartEnd[0]);
                    $viewStartEnd[1]->modify('+6 days');
                } else {
                    $pieces = explode(' ', $viewRange);
                    if (count($pieces) == 5) {
                        $m = $pieces[0];
                        $d1 = $pieces[1];
                        $d2 = $pieces[3];
                        $y = $pieces[4];
                        $startDateString = $y.'-'.$m.'-'.sprintf('%02d', $d1);
                        $endDateString = $y.'-'.$m.'-'.sprintf('%02d', rtrim($d2, ','));
                    } elseif (count($pieces) == 6) {
                        $m1 = $pieces[0];
                        $m2 = $pieces[3];
                        $d1 = $pieces[1];
                        $d2 = $pieces[4];
                        $y = $pieces[5];
                        $startDateString = $y.'-'.$m1.'-'.sprintf('%02d', $d1);
                        $endDateString = $y.'-'.$m2.'-'.sprintf('%02d', rtrim($d2, ','));
                    } elseif (count($pieces) == 7) {
                        $m1 = $pieces[0];
                        $m2 = $pieces[4];
                        $d1 = $pieces[1];
                        $d2 = $pieces[5];
                        $y1 = $pieces[2];
                        $y2 = $pieces[6];
                        $startDateString = $y1.'-'.$m1.'-'.sprintf('%02d', rtrim($d1, ','));
                        $endDateString = $y2.'-'.$m2.'-'.sprintf('%02d', rtrim($d2, ','));
                    }

                    $viewStartEnd[0] = new DateTime($startDateString);
                    $viewStartEnd[1] = new DateTime($endDateString);
                }

            }

        $packages = Package::whereBetween('targetStartDate', $viewStartEnd)
                                ->orWhereBetween('targetEndDate', $viewStartEnd)
                                ->orWhereBetween('actualStartDate', $viewStartEnd)
                                ->orWhereBetween('actualEndDate', $viewStartEnd)
                                ->get();

        foreach ($packages as $package) {
            $queryResults = DB::select($query, array($package->PackageID, $package->PackageID, $package->PackageID, $package->PackageID));

            //print_r($queryResults);
            //get results onto events
            $shiftStr = $queryResults[0]->shiftStr;
            $locationStr = $queryResults[0]->locationStr;
            $platformStr = $queryResults[0]->platformStr;
            //print_r($shiftStr);


            $target_event = array(
                'title' => $package->PackageName.'-Target',
                'start' => $package->TargetStartDate,
                'end' => $package->TargetEndDate,
                'shift' => $shiftStr,
                'location' => $locationStr,
                'bench' => $platformStr,
                'color' => 'grey',
            );
            $events[] = $target_event;
            $target_event = null;

            $actual_event = array(
                'title' => $package->PackageName.'-Actual',
                'start' => $package->ActualStartDate,
                'end' => $package->ActualEndDate,
                'shift' => $shiftStr,
                'location' => $locationStr,
                'bench' => $platformStr,
                'color' => '', //default blue color
            );
            $events[] = $actual_event;
            $actual_event = null;
        }

        $results = ['events' => $events,
                'goToDate' => $viewStartEnd[0],
                'viewType' => $viewType, ];

        return json_encode($results);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Return the calendar object with a filtered list of events to the view.
     *
     * @return Response
     */
    public function filter(Request $request)
    {
        //get the location and bench values from the request
        //validate the values
        //build query to get the filtered list from the database


        //rebuild the calendar object
        $calendar = \Calendar::addEvents($events) //add an array with addEvents
            ->setOptions([//set fullcalendar options
                'defaultView' => 'agendaWeek',
                'weekNumbers' => true,
            ])->setCallbacks([//set fullcalendar callback options (will not be JSON encoded)
                'viewRender' => 'function() {
		        	//alert("Callbacks!");
		        }',
            ]);

        return view('Schedule.schedule', compact('calendar'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
