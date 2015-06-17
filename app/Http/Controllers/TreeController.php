<?php namespace App\Http\Controllers;

use Request;
use DB;
use Input;

class TreeController extends Controller {

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
		// $this->middleware('auth');
	}

	/**
	 * Show the page to the user
	 *
	 * @return Response
	 */
	public function index()
	{

		return view('tree');
	}

	/**
	 * get the list of APPIDs to populate the dropdown on screen.
	 *
	 * @return Response
	 */
	public function getAPPIDsForDropDown(){
		//fetch the list of APPIDs to populate the drop down on screen
		$query = "SELECT DISTINCT SR.APPID, A.AppCommonName FROM ServerReporting SR, Application A WHERE SR.APPID = A.APPID AND A.ApplicationName NOT LIKE '%-ent' AND A.ApplicationName NOT LIKE '%-inc' ORDER BY A.APPID";
		$queryResults = DB::select($query);

		return json_encode($queryResults);
	}

	/**
	 * Show tree for the APP ID
	 *
	 * @return Response
	 */
	public function fetchTree()
	{
		$grey = '#5B9BD5';
		$black = '#000000';
		$green = '#00B050';
		$blue = '#767171';
		$colors = [$blue,$black,$green,$grey];
		$statusCounts = array(
			"Not Scheduled"=>0,
			"Scheduled"=>0,
			"Complete"=>0,
			"Out of Scope"=>0,
			"total"=>0
			);
		
		
		//try to get the APPID from the text box
		$inputAPP = Request::input('textAPPID');

		if($inputAPP){
			$query = "SELECT distinct A.ApplicationName, A.AppCommonName FROM ServerReporting SR, Application A WHERE SR.APPID = A.APPID AND SR.APPID=?";
			$queryResults = DB::select($query, array($inputAPP));



			$app = (object)['name'=>'', 'APPID'=>'', 'color'=>'', 'children'=>''];

			if(count($queryResults)>0){
				$app->name = $queryResults[0]->ApplicationName;
				$app->commonName = $queryResults[0]->AppCommonName;

				$query2 = "SELECT  DISTINCT SR.Company, SR.APPID FROM Application A, ServerReporting SR WHERE SR.APPID = A.APPID AND  A.ApplicationName like ? ORDER BY SR.Company";
				$queryResults2 = DB::select($query2, array($app->name.'%'));

			//print_r($app);
				

				//go get the environments of each instance
				foreach($queryResults2 as $dbInstance){
					//print_r($dbInstance);

					$instance = (object)['name'=>$dbInstance->Company, 'APPID'=>$dbInstance->APPID, 'color'=>'', 'children'=>''];
					// $query3 = "SELECT Distinct SR.Environment ,STUFF((SELECT ', ' + cast(T.InstanceID as varchar(max)) FROM dbo.ServerReporting T WHERE T.APPID = ? and T.Environment=SR.Environment ORDER BY InstanceID FOR XML PATH('')), 1, 1, '') AS InstanceString  from dbo.ServerReporting SR  where SR.APPID = ? GROUP BY SR.Environment ";
					$query3 = "SELECT DISTINCT SR.Environment from  ServerReporting SR WHERE SR.APPID=? ORDER BY SR.Environment";
					$queryResults3 = DB::select($query3, array($instance->APPID));
					foreach ($queryResults3 as $dbEnvironment) {
						$environment = (object)['name'=>$dbEnvironment->Environment, 'color'=>'', 'children'=>''];

						//get servers for this environment
						$query4 = "SELECT distinct SR.ServerName, SR.Status from ServerReporting SR WHERE SR.APPID=? AND SR.Environment = ? ORDER BY SR.ServerName";
						$queryResults4 = DB::select($query4, array($instance->APPID, $dbEnvironment->Environment));
						foreach($queryResults4 as $server){
							$serverColor = null;
							switch($server->Status){
								case "Not Scheduled":
								$serverColor = $blue;
								break;
								case "Out of Scope":
								$serverColor = $grey;
								break;
								case "Scheduled":
								$serverColor = $black;
								break;
								case "Complete":
								$serverColor =  $green;
								break;
								default:
								$serverColor = 'purple';
							}
							$statusCounts[$server->Status]++;
							$statusCounts['total']++;
                            //push this server into the environment
							$environment->children[] = (object)['name'=>$server->ServerName, 'color'=>$serverColor, 'children'=>''];
						}//end of server loop

                        //set environment color
						$environment->color = $this->getColor($environment,$colors);
						$environment->serverCount=count($environment->children);

                        //push this environment onto the instance
						$instance->children[] = $environment;

					}//end of environment loop

                    //set instance color
					$instance->color = $this->getColor($instance,$colors);

                    //push this instance onto the app
					$app->children[] = $instance;

				}//end of instance loop

                //set app color
				$app->color = $this->getColor($app,$colors);
				$app->statusCounts = $statusCounts;
				return json_encode($app);
			}else{
				$error = "no records found for APP-ID"+$inputAPP;
				return -1;
			}
		}else{
			return -2;
		}
	}

	private function getColor($parent, $colors){
		//$colors = [$blue,$black,$green,$grey];
        //find the lowest color number of the children
		$currentlowestcolor = 3;

		foreach($parent->children as $child){
			$childcolor = array_search($child->color, $colors);
			($currentlowestcolor>$childcolor)? $currentlowestcolor=$childcolor:$currentlowestcolor=$currentlowestcolor;
		}
        //translate the lowest number back to a color
		return $colors[$currentlowestcolor];
	}

}
