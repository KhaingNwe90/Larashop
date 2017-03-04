<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateSettingRequest;
use App\Http\Requests\UpdateSettingRequest;
use App\Http\Requests\DeleteSettingRequest;

use App\Models\Setting;

class SettingController extends Controller
{
    public function index(){
    	$settings = Setting::paginate(3);

		return view('backend.settings.index')->with(array("settings"=>$settings));
    }

    public function create(){
   
		$parent_settings = Setting::all()->pluck("name", "id")->toArray();

		return view("backend.settings.create")->with(array("parent_settings"=>$parent_settings));

    }

    public function store(CreateSettingRequest $request){

    	$data = $request->all();
    	$setting = Setting::create($data);
		return redirect(route('admin.settings.index'));
    }
    public function show(){

	}

    public function edit($id){
    	$setting = Setting::find($id);
	
		if(!$setting)
			abort(404);
		$parent_settings = Setting::all()->pluck("name", "id")->toArray();

		return view("backend.settings.edit")->with(array("setting"=>$setting, "parent_settings"=>$parent_settings));



    }

    public function update($id, UpdateSettingRequest $request){
    	$data = $request->all();

		$setting = Setting::find($id);

		if(!$setting)
			abort(404);


		$setting->update($data);

		return redirect()->back()->with("flash_success", "Updated Successfully.");
    }

    public function destroy($id, DeleteSettingRequest $request){
    	$setting = Setting::find($id);

		if(!$setting){
			return redirect(route("admin.settings.index"))->with("flash_warning", "Setting not found!");
		}

		$setting->delete();
		return redirect(route("admin.settings.index"))->with("flash_success", "Deleted Successfully.");
    }
}
