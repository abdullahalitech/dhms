<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Log;
use App\Models\Platform;
use App\Models\Project;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index(){
        
        if(Auth::user()->roles == 2 || Auth::user()->roles == 3){
            return redirect(url('/'));
        }
        $title = "Dashboard";
        return view('admin.index',compact('title'));
    }

    //employee functions

    public function add_employee(){

        if(Auth::user()->roles == 2 || Auth::user()->roles == 3){
            return redirect(url('/'));
        }

        $title = "Employees";
        return view('admin.employee.index', compact('title'));
    }

    public function store_employee(Request $request){
        if(Auth::user()->roles == 2 || Auth::user()->roles == 3){
            return redirect(url('/'));
        }

        $ip    = $_SERVER['REMOTE_ADDR'];; 
        $loggedInUser  = Auth::user();
        $name  = $request->name;
        $email   = $request->email;
        $password = $request->password;
        $phone = $request->phone;
        $department = $request->department;

        $password = Hash::make($password);

        $user 		= new User;
        
        $user->email 	  = $email;
        $user->name 	  = $name;
        $user->password   = $password;
        $user->phone 	  = $phone;
        $user->roles 	  = $department;
        $user->created_by = $loggedInUser->id;
        $user->user_ip 	  = $ip;
        
        $user->save();

        if($user->id > 0){

            $addr_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);

            $log 		= new Log;
        
            $log->page 	    = 'Employee';
            $log->action 	    = 'Store';
            $log->user_id      = $user->id;
            $log->done_by 	    = $loggedInUser->id;
            $log->done_by_ip   = $ip;
            $log->done_by_host = $addr_name;
            
            $log->save();

            echo json_encode(array('success' => 1));
            die();
        }
        
    }

    public function ajax_get_employee(Request $request){
        if(isset($request->search)){
            $all_user = User::where('roles', $request->search)->where('roles','!=', 1)->get();
        }else{
            $all_user = User::where('roles','!=', 1)->get();
        }
        
        
        $all_user_data = array();

        if ($all_user) {
            foreach ($all_user as $user) {
                
                $action_btn = '';
                $status = '';
                if ($user->status == 1) {
                    $action_btn .= ' <button class="btn btn-sm btn-warning edit-user" data-id="' . $user->id . '">Edit</button> <button class="btn btn-sm btn-primary view-user" data-id="{{ $user->id }}">View</button> ';
                    $action_btn .= '<button class="btn btn-sm btn-danger suspend-user" data-id="' . $user->id . '">Suspend</button>';
                    $status.= '<span class="badge badge-success">active</span>';
                } else {
                    $action_btn .= '<button class="btn btn-sm btn-success resume-user" data-id="' . $user->id . '">Resume</button>';
                    $status.= '<span class="badge badge-danger">inactive</span>';
                }

                

                $department = "";

                if($user->roles == 2) {
                    $department = "Business Developer";
                } else if($user->roles == 3) {
                    $department = "Developer";
                } else { 
                    $department = "Other";
                }

                $all_user_data[] = array(
                    'email'      => $user->email,
                    'name'       => $user->name,
                    'department' => $department,
                    'status'     => $status,
                    'created_at' => date("M j, Y", strtotime($user->created_at)),
                    'action'     => $action_btn
                );
            }
        }

        echo json_encode(array('data' => $all_user_data));
    }

    public function ajax_admin_get_employee(Request $request){
        $user_id = $request->user_id;

        $user = User::where('id',$user_id)->first();

        if($user){
            echo json_encode($user);
            die();
        }

    }

    public function update_employee(Request $request){
        if(Auth::user()->roles == 2 || Auth::user()->roles == 3){
            return redirect(url('/'));
        }

        $ip    = $_SERVER['REMOTE_ADDR']; 
        $loggedInUser  = Auth::user();
        $name  = $request->name;
        $password = $request->password;
        $phone = $request->phone;
        $department = $request->department;
        $id         = $request->id; 

        

        $user 		= User::find($id);
        
        $user->name 	  = $name;
        if($password != "" && isset($password)){
            $password = Hash::make($password);
            $user->password   = $password;
        }
        
        $user->phone 	  = $phone;
        $user->roles 	  = $department;
        
        $user->save();
        
        $addr_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        
        $log 		= new Log;
        
        $log->page 	    = 'Employee';
        $log->action 	    = 'Update info (------------'.$user.'-----------)';
        $log->user_id      = $id;
        $log->done_by 	    = $loggedInUser->id;
        $log->done_by_ip   = $ip;
        $log->done_by_host = $addr_name;
        
        $log->save();

        echo json_encode(array('success' => 1));
        die();
    
    }

    public function ajax_admin_suspend_user(Request $request){
        if(Auth::user()->roles == 2 || Auth::user()->roles == 3){
            return redirect(url('/'));
        }

        $ip             = $_SERVER['REMOTE_ADDR']; 
        $loggedInUser   = Auth::user();
        $user_id        = $request->user_id;
        $user 		= User::where('id',$user_id)->update([
            'status' => 0
        ]);
        
        $addr_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        
        $log 		= new Log;
        
        $log->page 	    = 'Employee';
        $log->action 	    = 'Suspend';
        $log->user_id      = $user_id;
        $log->done_by 	    = $loggedInUser->id;
        $log->done_by_ip   = $ip;
        $log->done_by_host = $addr_name;
        
        $log->save();
        
        echo json_encode(array('success' => 1));
        die();
        
    }

    public function ajax_admin_resume_user(Request $request){
        if(Auth::user()->roles == 2 || Auth::user()->roles == 3){
            return redirect(url('/'));
        }

        $ip             = $_SERVER['REMOTE_ADDR']; 
        $loggedInUser   = Auth::user();
        $user_id        = $request->user_id;
        $user 		= User::where('id',$user_id)->update([
            'status' => 1
        ]);
        
        $addr_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        
        $log 		= new Log;
        
        $log->page 	    = 'Employee';
        $log->action 	    = 'Resume';
        $log->user_id      = $user_id;
        $log->done_by 	    = $loggedInUser->id;
        $log->done_by_ip   = $ip;
        $log->done_by_host = $addr_name;
        
        $log->save();
        
        echo json_encode(array('success' => 1));
        die();
    }

    //platform functions

    public function add_platform(){

        if(Auth::user()->roles == 2 || Auth::user()->roles == 3){
            return redirect(url('/'));
        }

        $title = "Platforms";
        return view('admin.platform.index', compact('title'));
    }

    public function store_platform(Request $request){
        if(Auth::user()->roles == 2 || Auth::user()->roles == 3){
            return redirect(url('/'));
        }

        $ip             = $_SERVER['REMOTE_ADDR'];; 
        $loggedInUser   = Auth::user();
        $name           = $request->name;
        $commission    = $request->commission;
        $type           = $request->type;

        $platform 		        = new Platform;
        
        $platform->commission 	= $commission;
        $platform->name 	    = $name;
        $platform->type         = $type;
        $platform->created_by   = $loggedInUser->id;
        
        $platform->save();

        if($platform->id > 0){

            $addr_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);

            $log 		= new Log;
        
            $log->page 	            = 'Platform';
            $log->action 	        = 'Store';
            $log->platform_id       = $platform->id;
            $log->done_by 	        = $loggedInUser->id;
            $log->done_by_ip        = $ip;
            $log->done_by_host      = $addr_name;
            
            $log->save();

            echo json_encode(array('success' => 1));
            die();
        }
        
    }

    public function ajax_get_platform(Request $request){
        if(isset($request->search)){
            $all_platform = Platform::where('type', $request->search)->get();
        }else{
            $all_platform = Platform::all();
        }
        
        
        $all_platform_data = array();

        if ($all_platform) {
            foreach ($all_platform as $platform) {
                
                $action_btn = '';
                $status = '';
                if ($platform->status == 1) {
                    $action_btn .= ' <button class="btn btn-sm btn-warning edit-platform" data-id="'.$platform->id.'">Edit</button> <button class="btn btn-sm btn-primary view-platform" data-id="{{ $platform->id }}">View</button> ';
                    $action_btn .= '<button class="btn btn-sm btn-danger suspend-platform" data-id="'.$platform->id.'">Suspend</button>';
                    $status.= '<span class="badge badge-success">active</span>';
                } else {
                    $action_btn .= '<button class="btn btn-sm btn-success resume-platform" data-id="'.$platform->id.'">Resume</button>';
                    $status.= '<span class="badge badge-danger">inactive</span>';
                }

                

                $type = "";

                if($platform->type == 2) {
                    $type = "Upwork";
                } else if($platform->type == 3) {
                    $type = "Direct";
                }else if($platform->type == 4) {
                    $type = "CV Marketing";
                } else { 
                    $type = "Fiver";
                }

                $all_platform_data[] = array(
                    'name'       => $platform->name,
                    'type'       => $type,
                    'commission' => $platform->commission.'%',
                    'status'     => $status,
                    'created_at' => date("M j, Y", strtotime($platform->created_at)),
                    'action'     => $action_btn
                );
            }
        }

        echo json_encode(array('data' => $all_platform_data));
    }

    public function ajax_admin_get_platform(Request $request){
        $platform_id = $request->platform_id;

        $platform = Platform::where('id',$platform_id)->first();

        if($platform){
            echo json_encode($platform);
            die();
        }

    }

    public function update_platform(Request $request){
        if(Auth::user()->roles == 2 || Auth::user()->roles == 3){
            return redirect(url('/'));
        }

        $ip             = $_SERVER['REMOTE_ADDR']; 
        $loggedInUser   = Auth::user();
        $name           = $request->name;
        $commission     = $request->commission;
        $type           = $request->type;
        $id             = $request->id;

        $platform 		        =  Platform::find($id);
        
        $platform->commission 	= $commission;
        $platform->name 	    = $name;
        $platform->type         = $type;
        
        $platform->save();
        
        $addr_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        
        $log 		= new Log;
        
        $log->page 	    = 'platform';
        $log->action 	    = 'Update info (------------'.$platform.'-----------)';
        $log->platform_id      = $platform->id;
        $log->done_by 	    = $loggedInUser->id;
        $log->done_by_ip   = $ip;
        $log->done_by_host = $addr_name;
        
        $log->save();

        echo json_encode(array('success' => 1));
        die();
    
    }

    public function ajax_admin_suspend_platform(Request $request){
        if(Auth::user()->roles == 2 || Auth::user()->roles == 3){
            return redirect(url('/'));
        }

        $ip             = $_SERVER['REMOTE_ADDR']; 
        $loggedInUser   = Auth::user();
        $platform_id    = $request->platform_id;
        $platform 		= Platform::where('id',$platform_id)->update([
            'status' => 0
        ]);
        
        $addr_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        
        $log 		= new Log;
        
        $log->page 	    = 'Platform';
        $log->action 	    = 'Suspend';
        $log->platform_id   = $platform_id;
        $log->done_by 	    = $loggedInUser->id;
        $log->done_by_ip   = $ip;
        $log->done_by_host = $addr_name;
        
        $log->save();
        
        echo json_encode(array('success' => 1));
        die();
        
    }

    public function ajax_admin_resume_platform(Request $request){
        if(Auth::user()->roles == 2 || Auth::user()->roles == 3){
            return redirect(url('/'));
        }

        $ip             = $_SERVER['REMOTE_ADDR']; 
        $loggedInUser   = Auth::user();
        $platform_id        = $request->platform_id;
        $platform 		= Platform::where('id',$platform_id)->update([
            'status' => 1
        ]);
        
        $addr_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        
        $log 		= new Log;
        
        $log->page 	    = 'Platform';
        $log->action 	    = 'Resume';
        $log->platform_id      = $platform_id;
        $log->done_by 	    = $loggedInUser->id;
        $log->done_by_ip   = $ip;
        $log->done_by_host = $addr_name;
        
        $log->save();
        
        echo json_encode(array('success' => 1));
        die();
    }


    //project functions

    public function add_project(){

        if(Auth::user()->roles == 2 || Auth::user()->roles == 3){
            return redirect(url('/'));
        }

        $platform = Platform::where('status',1)->get();
        $title = "Projects";
        return view('admin.project.index', compact('title','platform'));
    }

    public function store_project(Request $request){
        if(Auth::user()->roles == 2 || Auth::user()->roles == 3){
            return redirect(url('/'));
        }

        $ip              = $_SERVER['REMOTE_ADDR'];; 
        $loggedInUser    = Auth::user();
        $title           = $request->title;
        $client_email    = $request->client_email;
        $client_name     = $request->client_name;
        $amount          = $request->amount;
        $platform        = $request->platform;
        $description     = json_encode($request->description);
        $description_html     = $request->description_html;
        $commission      = 0;
        $net_profit         = 0;
        $platform_row = Platform::where('id',$platform)->first();
        if($platform_row){
            $commission = $amount*($platform_row->commission/100);
            $net_profit  = $amount - $commission; 

        }
        
        $project 		        = new Project;
        
        $project->title 	            = $title;
        $project->client_email 	        = $client_email;
        $project->client_name 	        = $client_name;
        $project->description           = $description;
        $project->description_html      = $description_html;
        $project->platform_id           = $platform;
        $project->total_amount          = $amount;
        $project->net_profit            = $net_profit;
        $project->commission            = $commission;
        $project->owner                 = $loggedInUser->id;
        $project->created_by            = $loggedInUser->id;
        
        $project->save();

        if($project->id > 0){

            $addr_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);

            $log 		= new Log;
        
            $log->page 	            = 'Project';
            $log->action 	        = 'Store';
            $log->project_id        = $project->id;
            $log->done_by 	        = $loggedInUser->id;
            $log->done_by_ip        = $ip;
            $log->done_by_host      = $addr_name;
            
            $log->save();

            echo json_encode(array('success' => 1));
            die();
        }
        
    }

    public function ajax_get_project(Request $request){
        $all_project = array();
        if(isset($request->search)){
            $platform = Platform::where('type', $request->search)->get();
            foreach($platform as $p){
                $ind_project = Project::where('platform_id', $p->id)->get();
                foreach($ind_project as $i_p){
                    array_push($all_project, $i_p);
                }
                
                
            }
        }else{
            $all_project = Project::all();
        }
        
    
        $all_project_data = array();
        if ($all_project) {
            $count = 0;
            foreach ($all_project as $project) {
                
                if(isset($request->search)){
                    $platform = Platform::where('id', $project->platform_id)->first();
                    $owner_user = User::find($project->owner);
                    if($owner_user){
                        $owner = $owner_user->name;
                    }
                    $image = "";
                    if($platform){
                        if($platform->type == 1){
                            //fiver
                            $image = '<img src="'.url('images/fiver-logo.svg').'"  class="w-150"/>';
                        }elseif($platform->type == 2){
                            //upwork
                            $image = '<img src="'.url('images/upwork-logo.svg').'"  class="w-150"/>';
                        }elseif($platform->type == 3){
                            //direct
                            $image = '<img src="'.url('images/direct-logo.png').'"  class="w-150"/>';
                        }else{
                            //cv
                            $image = '<img src="'.url('images/cv-logo.png').'"  class="w-150"/>';
                        }
                    }
                    $action_btn = '';
                    $status = '';
                    if ($project->status == 1) {
                        $action_btn .= ' <button class="btn btn-sm btn-warning edit-project" data-id="'.$project->id.'">Edit</button> <button class="btn btn-sm btn-primary view-project" data-id="{{ $project->id }}">View</button> ';
                        $action_btn .= '<button class="btn btn-sm btn-danger suspend-project" data-id="'.$project->id.'">Suspend</button>';
                        $status.= '<span class="badge badge-warning">Pending</span>';
                    } else if($project->status == 2) {
                        $action_btn .= '<button class="btn btn-sm btn-success resume-project" data-id="'.$project->id.'">Resume</button>';
                        $status.= '<span class="badge badge-primary">In Progress</span>';
                    } else{
                        $status.= '<span class="badge badge-success">Completed</span>';
                    }
                    
                    
                    $element = '<div class="row">
                        <div class="col-12 col-md-2  bg-light d-flex align-items-center justify-content-center rounded">
                            <div>
                            '.$image.'
                        
                            </div>
                        </div>
                        <div class="col-12 col-md-7">
                            <h4 class="mb-1">'.$project->title.'</h4>
                            <p class="mb-0 font-weight-bold">'.$project->client_email.'</p>
                            <p class="mb-1">'.$project->client_name.'</p>
                            <div class="d-flex align-items-start justify-content-start">
                            '.$status.'
                            </div>
                            <div class="d-flex justify-content-start align-items-center mt-2">
                                <p class="mb-0">Created Date: <span class="text-dark">'.date("j M, Y", strtotime($project->created_at)).'</span></p>
                                <p class="mb-0 mx-4">Account Holder: <span class="text-dark">'.$platform->name.'</span></p>
                                <p class="mb-0">Project Owner: <span class="text-dark">'.$owner.'</span></p>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="d-flex align-items-end justify-content-end">
                                <h4 class="mb-0 text-dark font-weight-bold">$'.$project->total_amount.'</h4>
                            </div>
                            <div class=" d-flex justify-content-end align-items-end mt-5">
                                <a class="btn btn-sm btn-danger edit-project" data-id="'.$project->id.'">
                                <i class="fas fa-fw fa-cog"></i>
                                Settings</a> 
                            </div>
                        </div>
                    </div>';

                    $count++;

                    $all_project_data[] = array(
                        'project_data'  => $element
                    );
                }else{
                    $platform = Platform::where('id', $project->platform_id)->first();
                    $image = "";
                    $owner = "N/A";

                    $owner_user = User::find($project->owner);
                    if($owner_user){
                        $owner = $owner_user->name;
                    }
                    if($platform){
                        if($platform->type == 1){
                            //fiver
                            $image = '<img src="'.url('images/fiver-logo.svg').'"  class="w-150"/>';
                        }elseif($platform->type == 2){
                            //upwork
                            $image = '<img src="'.url('images/upwork-logo.svg').'"  class="w-150"/>';
                        }elseif($platform->type == 3){
                            //direct
                            $image = '<img src="'.url('images/direct-logo.png').'"  class="w-150"/>';
                        }else{
                            //cv
                            $image = '<img src="'.url('images/cv-logo.png').'"  class="w-150"/>';
                        }
                    }

                    $action_btn = '';
                    $status = '';
                    if ($project->status == 1) {
                        $action_btn .= ' <button class="btn btn-sm btn-warning edit-project" data-id="'.$project->id.'">Edit</button> <button class="btn btn-sm btn-primary view-project" data-id="{{ $project->id }}">View</button> ';
                        $action_btn .= '<button class="btn btn-sm btn-danger suspend-project" data-id="'.$project->id.'">Suspend</button>';
                        $status.= '<span class="badge badge-warning">Pending</span>';
                    } else if($project->status == 2) {
                        $action_btn .= '<button class="btn btn-sm btn-success resume-project" data-id="'.$project->id.'">Resume</button>';
                        $status.= '<span class="badge badge-primary">In Progress</span>';
                    } else{
                        $status.= '<span class="badge badge-success">Completed</span>';
                    }
                    
                    $element = '<div class="row">
                        <div class="col-12 col-md-2  bg-light d-flex align-items-center justify-content-center rounded">
                            <div>
                            '.$image.'
                        
                            </div>
                        </div>
                        <div class="col-12 col-md-7">
                            <h4 class="mb-1">'.$project->title.'</h4>
                            <p class="mb-0 font-weight-bold">'.$project->client_email.'</p>
                            <p class="mb-1">'.$project->client_name.'</p>
                            <div class="d-flex align-items-start justify-content-start">
                            '.$status.'
                            </div>
                            <div class="d-flex justify-content-start align-items-center mt-2">
                                <p class="mb-0">Created Date: <span class="text-dark">'.date("j M, Y", strtotime($project->created_at)).'</span></p>
                                <p class="mb-0 mx-4">Account Holder: <span class="text-dark">'.$platform->name.'</span></p>
                                <p class="mb-0">Project Owner: <span class="text-dark">'.$owner.'</span></p>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="d-flex align-items-end justify-content-end">
                                <h4 class="mb-0 text-dark font-weight-bold">$'.$project->total_amount.'</h4>
                            </div>
                            <div class=" d-flex justify-content-end align-items-end mt-5">
                                <a class="btn btn-sm btn-danger edit-project" data-id="'.$project->id.'">
                                <i class="fas fa-fw fa-cog"></i>
                                Settings</a> 
                            </div>
                        </div>
                    </div>';
                    

                    $all_project_data[] = array(
                        'project_data'  => $element
                    );
                }
                
            }
        }

        echo json_encode(array('data' => $all_project_data));
    }

    public function ajax_admin_get_project(Request $request){
        $project_id = $request->project_id;

        $project = Project::where('id',$project_id)->first();

        if($project){
            echo json_encode($project);
            die();
        }

    }

    public function update_project(Request $request){
        if(Auth::user()->roles == 2 || Auth::user()->roles == 3){
            return redirect(url('/'));
        }

        $ip             = $_SERVER['REMOTE_ADDR']; 
        $loggedInUser   = Auth::user();
        $name           = $request->name;
        $commission     = $request->commission;
        $type           = $request->type;
        $id             = $request->id;

        $project 		        =  project::find($id);
        
        $project->commission 	= $commission;
        $project->name 	    = $name;
        $project->type         = $type;
        
        $project->save();
        
        $addr_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        
        $log 		= new Log;
        
        $log->page 	    = 'project';
        $log->action 	    = 'Update info (------------'.$project.'-----------)';
        $log->project_id      = $project->id;
        $log->done_by 	    = $loggedInUser->id;
        $log->done_by_ip   = $ip;
        $log->done_by_host = $addr_name;
        
        $log->save();

        echo json_encode(array('success' => 1));
        die();
    
    }

    public function ajax_admin_suspend_project(Request $request){
        if(Auth::user()->roles == 2 || Auth::user()->roles == 3){
            return redirect(url('/'));
        }

        $ip             = $_SERVER['REMOTE_ADDR']; 
        $loggedInUser   = Auth::user();
        $project_id    = $request->project_id;
        $project 		= project::where('id',$project_id)->update([
            'status' => 0
        ]);
        
        $addr_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        
        $log 		= new Log;
        
        $log->page 	    = 'project';
        $log->action 	    = 'Suspend';
        $log->project_id   = $project_id;
        $log->done_by 	    = $loggedInUser->id;
        $log->done_by_ip   = $ip;
        $log->done_by_host = $addr_name;
        
        $log->save();
        
        echo json_encode(array('success' => 1));
        die();
        
    }

    public function ajax_admin_resume_project(Request $request){
        if(Auth::user()->roles == 2 || Auth::user()->roles == 3){
            return redirect(url('/'));
        }

        $ip             = $_SERVER['REMOTE_ADDR']; 
        $loggedInUser   = Auth::user();
        $project_id        = $request->project_id;
        $project 		= project::where('id',$project_id)->update([
            'status' => 1
        ]);
        
        $addr_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        
        $log 		= new Log;
        
        $log->page 	    = 'project';
        $log->action 	    = 'Resume';
        $log->project_id      = $project_id;
        $log->done_by 	    = $loggedInUser->id;
        $log->done_by_ip   = $ip;
        $log->done_by_host = $addr_name;
        
        $log->save();
        
        echo json_encode(array('success' => 1));
        die();
    }

    
}
