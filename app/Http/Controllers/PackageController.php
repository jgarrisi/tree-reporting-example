<?php namespace App\Http\Controllers;

use DB;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\RIPSvc;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class PackageController extends Controller {

	/**
	* Create a new controller instance.
	*
	* @return void
	*/
	public function __construct()
	{
		//
	}

	/**
	* Display a listing of the resource.
	*
	* @return Response
	*/
	public function index()
	{
		$packages = DB::table('Package')->get();
		return view("Package.index", ['packages' => $packages]);
	}

	/**
	* Show the form for creating a new resource.
	*
	* @return Response
	*/
	public function create(Request $request)
	{
		$this->validate($request, [ 'eprid' => 'integer|digits:6' ]);

		$data = [];
		if($request->input('eprid')) {
			$data['appInfo'] = json_decode(RIPSvc::getAppInfo($request->input('eprid')));
		}
		return view("Package.create", $data);
	}

	/**
	* Store a newly created resource in storage.
	*
	* @return Response
	*/
	public function store(Request $request)
	{
		//TODO(mark): validate and store request input as new Package
		return new RedirectResponse(url('package/1'));
	}

	/**
	* Display the specified resource.
	*
	* @param  int  $id
	* @return Response
	*/
	public function show($id)
	{
		try {
			$package = PackageController::getPackageByID($id);
			if ($package) {
				return view("Package.show")->withPackage($package);
			} else {
				return view("Package.show")->withErrors("Could not find RIP Package: " . $id);
			}
		} catch (\Exception $e) {
			return view("Package.show")->withErrors("Could not find RIP Package: " . $id);
		}
	}

	/**
	* Show the form for editing the specified resource.
	*
	* @param  int  $id
	* @return Response
	*/
	public function edit($id)
	{
		//
	}

	/**
	* Update the specified resource in storage.
	*
	* @param  int  $id
	* @return Response
	*/
	public function update($id)
	{
		//
	}

	/**
	* Remove the specified resource from storage.
	*
	* @param  int  $id
	* @return Response
	*/
	public function destroy($id)
	{
		//
	}

	private static function getPackageByID($id) {
		return DB::table('Package')->where('PackageID', $id)->first();
	}

}
