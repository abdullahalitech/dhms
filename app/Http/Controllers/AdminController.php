<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Log;
use App\Models\Platform;
use App\Models\Project;
use App\Models\BidInvite;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index(){
        
        if(Auth::user()->roles == 2 || Auth::user()->roles == 3){
            return redirect(url('/'));
        }
        $monthly_earning = 0;
        $monthly_bd_commission = 0;
        $overall_earning = 0;
        $overall_bd_commission = 0;
        $completed_project = 0;
        $pending_project = 0;
        $employee = 0;
        $platform = 0;

        $first_day_this_month = date('Y-m-01');
		$last_day_this_month = date("Y-m-t");

        $projects = Project::all();

        $employee = User::where('roles','!=',1)->count(); 
        $platform = Platform::where('status',1)->count(); 
        foreach($projects as $project){
            $overall_earning += $project->net_profit;
            $overall_bd_commission += $project->commission;           
            if($project->created_at >= $first_day_this_month && $project->created_at <= $last_day_this_month){
                $monthly_earning += $project->net_profit;
                $monthly_bd_commission += $project->commission;
            }
            
            if($project->status == 3){
                $completed_project += 1;
            }elseif ($project->status == 1){
                $pending_project += 1;
            }else{
                
            }

        }

        $title = "Dashboard";
        return view('admin.index',compact('title','overall_earning','overall_bd_commission','monthly_earning','monthly_bd_commission','completed_project','pending_project','employee','platform'));
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

        $ip    = $_SERVER['REMOTE_ADDR'];
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
                $project_url = url('projects').'/'.$user->id;
                if ($user->status == 1) {
                    $action_btn .= ' <button class="btn btn-sm btn-warning edit-user" data-id="' . $user->id . '">Edit</button> <a class="btn btn-sm btn-primary view-user" href="'.$project_url.'" data-id="{{ $user->id }}">Projects</a> ';
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

        $ip             = $_SERVER['REMOTE_ADDR'];
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
                    $action_btn .= ' <button class="btn btn-sm btn-warning edit-platform mx-2" data-id="'.$platform->id.'">Edit</button>';
                    $action_btn .= '<button class="btn btn-sm btn-danger suspend-platform" data-id="'.$platform->id.'">Suspend</button>';
                    $status.= '<span class="badge badge-success">active</span>';
                } else {
                    $action_btn .= '<button class="btn btn-sm btn-success resume-platform mx-2" data-id="'.$platform->id.'">Resume</button>';
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

    public function add_project($id){


        if(Auth::user()->roles == 2 || Auth::user()->roles == 3){
            return redirect(url('/'));
        }

        $id  = $id;

        $platform = Platform::where('status',1)->get();
        $title = "Projects";
        return view('admin.project.index', compact('title','platform','id'));
    }

    public function store_project(Request $request){
        if(Auth::user()->roles == 2 || Auth::user()->roles == 3){
            return redirect(url('/'));
        }

        $ip              = $_SERVER['REMOTE_ADDR'];
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
                    if($request->id != "all"){
                        if($project->owner != $request->id && $project->shared_user != $request->id){
                            continue;
                        }
                    }
                    
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
                                <a href="'.url('project').'/'.$project->id.'" class="btn btn-sm btn-danger edit-project" >
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
                    if($request->id != "all"){
                        
                        if($project->owner != $request->id && $project->shared_user != $request->id){
                            
                            continue;
                        }
                    }
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
                                <a href="'.url('project').'/'.$project->id.'" class="btn btn-sm btn-danger edit-project" >
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
        $project 		= Project::where('id',$project_id)->update([
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
        $project 		= Project::where('id',$project_id)->update([
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

    public function project_single($id){
        if(Auth::user()->roles == 2 || Auth::user()->roles == 3){
            return redirect(url('/'));
        }
        $id             = intval($id);
        $project 	    = Project::where('id',$id)->first();
        
        $all_platform = Platform::where('status',1)->get();
        if($project){

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

            $buttons = "";

            if($project->status != 3){
                $buttons =' <div class="text-wrap text-break d-flex gap-3 align-items-center justify-content-end my-3">
                <a type="button" href="#" data-toggle="modal" data-target="#deadlineModal" class="btn-sm mr-2 px-3 btn btn-danger text-sm">Deadline</a>
                <a type="button" href="#" data-toggle="modal" data-target="#editModal" class="edit btn-sm mr-2 px-3 btn btn-warning text-sm">Edit</a>
                <a type="button" href="#" data-toggle="modal" data-target="#assignModal" class="btn-sm mr-2 px-3 btn btn-dark text-sm">Assign</a>
                <a type="button" href="#" data-toggle="modal" data-target="#shareModal" class="btn-sm px-3  mr-2 btn btn-primary text-sm">Share</a>
                <a type="button" href="#" data-toggle="modal" data-target="#completeModal" class="btn-sm px-3 btn btn-success text-sm">Mark Complete</a>
            </div>';
            }
            
            $element = '<div class="row border-bottom pb-3 mb-4">
                <div class="col-12 col-md-2  bg-light d-flex align-items-center justify-content-center rounded">
                    <div>
                    '.$image.'
                
                    </div>
                </div>
                <div class="col-12 col-md-10">
                    <div class="d-flex justify-content-between">
                        <p class="mb-0 font-weight-bold">'.$project->client_email.'</p>
                        <h4 class="mb-0 text-dark font-weight-bold">$'.$project->total_amount.'</h4>
                    </div>
                    <div class="d-flex align-items-start justify-content-start my-2">
                    '.$status.'
                    </div>
                    <p class="mb-1">'.$project->client_name.'</p>'.$buttons.'
                   
                </div>
            </div>';
            
            $deadline = "N/A";
            if($project->due_date != NULL){
                $deadline = date("j M, Y", strtotime($project->due_date));
            }

            $completed_date = "N/A";
            if($project->completed_date != NULL){
                $completed_date = date("j M, Y", strtotime($project->completed_date));
            }
            
            $shared_user = User::find($project->shared_user);
            $s_user = "N/A";
            if($shared_user){
                $s_user = $shared_user->name;
            }
            $modal_ele = '<div class="text-start mt-2">
                    <p class="font-weight-bold fs-14">Owner: <span class="text-dark font-weight-normal">'.$owner.'</span></p>
                    <p class="font-weight-bold fs-14">Account Holder: <span class="text-dark font-weight-normal">'.$platform->name.'</span></p>
                    <p class="font-weight-bold fs-14">Manage By: <span class="text-dark font-weight-normal">'.$s_user.'</span></p>
                    <p class="font-weight-bold fs-14">Created On: <span class="text-dark font-weight-normal">'.date("j M, Y", strtotime($project->created_at)).'</span></p>
                    <p class="font-weight-bold fs-14">Deadline: <span class="text-dark font-weight-normal">'.$deadline.'</span></p>
                    <p class="font-weight-bold fs-14">Completed On: <span class="text-dark font-weight-normal">'.$completed_date.'</span></p>
                    <p class="font-weight-bold fs-14">Net Revenue: <span class="text-dark font-weight-normal">$'.$project->net_profit.'</span></p>
                    <p class="font-weight-bold fs-14">BD Commission: <span class="text-dark font-weight-normal">$'.$project->commission.'</span></p>
                    
                    
                </div>';

            $bd_users = User::where('roles',2)->where('status',1)->get();
            $s_users = User::where('roles',3)->where('status',1)->get();

            $title = $project->title;
            $description = json_decode($project->description);
            $desc_array =array();
            foreach($description as $d){
                array_push($desc_array, $d);
            }
            $desc_array = $desc_array;
            return view('admin.project.single', compact('title','element','project','all_platform','modal_ele','bd_users','s_users','desc_array'));
        }
        

        
    }

    public function set_deadline(Request $request){
        if(Auth::user()->roles == 2 || Auth::user()->roles == 3){
            return redirect(url('/'));
        }

        $ip             = $_SERVER['REMOTE_ADDR']; 
        $loggedInUser   = Auth::user();
        $project_id    = $request->id;
        $deadline      = date('Y-m-d H:i:s', strtotime($request->deadline));
        
        $project 		        =  project::find($project_id);
        
        $project->due_date 	    = $deadline;
        
        $project->save();
        
        $addr_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        
        $log 		= new Log;
        
        $log->page 	    = 'project';
        $log->action 	    = 'Update info (deadline------------'.$request->deadline.'-----------)';
        $log->project_id   = $project_id;
        $log->done_by 	    = $loggedInUser->id;
        $log->done_by_ip   = $ip;
        $log->done_by_host = $addr_name;
        
        $log->save();
        
        echo json_encode(array('success' => 1));
        die();
    }
    
    public function set_assign(Request $request){
        if(Auth::user()->roles == 2 || Auth::user()->roles == 3){
            return redirect(url('/'));
        }

        $ip             = $_SERVER['REMOTE_ADDR']; 
        $loggedInUser   = Auth::user();
        $project_id    = $request->id;
        $assign_user   = $request->assign_user;
        
        $project 		        =  project::find($project_id);
        
        $project->owner 	    = $assign_user;
        
        $project->save();
        
        $addr_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        
        $log 		= new Log;
        
        $log->page 	    = 'project';
        $log->action 	   = 'Update info (assign------------'.$request->assign_user.'-----------)';
        $log->project_id   = $project_id;
        $log->done_by 	    = $loggedInUser->id;
        $log->done_by_ip   = $ip;
        $log->done_by_host = $addr_name;
        
        $log->save();
        
        echo json_encode(array('success' => 1));
        die();
    }
    public function set_share(Request $request){
        if(Auth::user()->roles == 2 || Auth::user()->roles == 3){
            return redirect(url('/'));
        }

        $ip             = $_SERVER['REMOTE_ADDR']; 
        $loggedInUser   = Auth::user();
        $project_id    = $request->id;
        $share_user      = $request->share_user;
        
        $project 		        =  project::find($project_id);
        
        $project->shared_user 	    = $share_user;
        
        $project->save();
        
        $addr_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        
        $log 		= new Log;
        
        $log->page 	    = 'project';
        $log->action 	    = 'Update info (share------------'.$request->share_user.'-----------)';
        $log->project_id   = $project_id;
        $log->done_by 	    = $loggedInUser->id;
        $log->done_by_ip   = $ip;
        $log->done_by_host = $addr_name;
        
        $log->save();
        
        echo json_encode(array('success' => 1));
        die();
    }

    public function edit_project(Request $request){
        if(Auth::user()->roles == 2 || Auth::user()->roles == 3){
            return redirect(url('/'));
        }

        $ip              = $_SERVER['REMOTE_ADDR'];
        $loggedInUser    = Auth::user();
        $title           = $request->title;
        $client_email    = $request->client_email;
        $client_name     = $request->client_name;
        $amount          = $request->amount;
        $platform        = $request->platform;
        $description     = json_encode($request->description);
        $description_html  = $request->description_html;
        $commission         = 0;
        $net_profit         = 0;
        $platform_row = Platform::where('id',$platform)->first();
        if($platform_row){
            $commission = $amount*($platform_row->commission/100);
            $net_profit  = $amount - $commission; 

        }
        
        $id = intval($request->id);
        $project 		        = Project::find($id);
        
        $project->title 	            = $title;
        $project->client_email 	        = $client_email;
        $project->client_name 	        = $client_name;
        $project->description           = $description;
        $project->description_html      = $description_html;
        $project->platform_id           = $platform;
        $project->total_amount          = $amount;
        $project->net_profit            = $net_profit;
        $project->commission            = $commission;
        
        $project->save();

        

        $addr_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);

        $log 		= new Log;
    
        $log->page 	            = 'Project';
        $log->action 	        = 'Update info(--------------'.$project.'------------)';
        $log->project_id        = $project->id;
        $log->done_by 	        = $loggedInUser->id;
        $log->done_by_ip        = $ip;
        $log->done_by_host      = $addr_name;
        
        $log->save();

        echo json_encode(array('success' => 1));
        die();
        
        
    }
    

    public function ajax_admin_mark_completed(Request $request){
        if(Auth::user()->roles == 2 || Auth::user()->roles == 3){
            return redirect(url('/'));
        }

        $ip             = $_SERVER['REMOTE_ADDR']; 
        $loggedInUser   = Auth::user();
        $project_id    = $request->id;
        
        $project 		        =  project::find($project_id);
        
        $project->status 	    = 3;
        $project->completed_date 	    = now();
        
        $project->save();
        
        $addr_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        
        $log 		= new Log;
        
        $log->page 	    = 'project';
        $log->action 	    = 'Update info (completed------------'.$request->share_user.'-----------)';
        $log->project_id   = $project_id;
        $log->done_by 	    = $loggedInUser->id;
        $log->done_by_ip   = $ip;
        $log->done_by_host = $addr_name;
        
        $log->save();
        
        echo json_encode(array('success' => 1));
        die();
    }

    //bid functions

    public function add_bid(){

        if(Auth::user()->roles == 2 || Auth::user()->roles == 3){
            return redirect(url('/'));
        }

        $title = "Bid Commissions";
        return view('admin.bid.index', compact('title'));
    }
    public function add_invite(){

        if(Auth::user()->roles == 2 || Auth::user()->roles == 3){
            return redirect(url('/'));
        }

        $title = "Invite Commissions";
        return view('admin.invite.index', compact('title'));
    }

    public function insert_commission(){

        $from = 0;
        $to   = 499;
        $com  = 0;
        $type = 2;
        $count = 0;
        for($i=0; $i<10000; $i++){
            if($from > 9500){
                break;
            }
            if($count == 0){
                $com = 0;
            }else if($count == 1){
                $from   = $to+1;
                $to     = $to+500;
                $com = 2;
            }else{
                $from   = $to+1;
                $to     = $to+500;
                $com    = $com+1;
            }
            $bidinvite 		        = new BidInvite;
        
            $bidinvite->com_from 	    = $from;
            $bidinvite->com_to 	        = $to;
            $bidinvite->commission      = $com;
            $bidinvite->type            = $type;
            $bidinvite->created_by      = 1;
            
            $bidinvite->save();
            $count++;
            
        }

    }

    public function store_bidinvite(Request $request){
        if(Auth::user()->roles == 2 || Auth::user()->roles == 3){
            return redirect(url('/'));
        }

        $ip             = $_SERVER['REMOTE_ADDR']; 
        $loggedInUser   = Auth::user();
        $from           = $request->from;
        $to           = $request->to;
        $commission    = $request->commission;
        $type           = $request->type;

        if($from > $to || $from == $to){
            echo json_encode(array('success' => 0, 'msg' => 'Invalid Range.'));
            die();
        }
        $all_ranges = BidInvite::where('type',$type)->get();
        $range_exist = false;
        foreach($all_ranges as $a_r){
            
            if(($from >= $a_r->com_from && $from <= $a_r->com_to) ||
                ($to <= $a_r->com_to && $to >= $a_r->com_from) ||
                ($from <= $a_r->com_from && $to >= $a_r->com_from)
            ){
                $range_exist = true;
                break;
            }
            
            
        }
			
			
			
	    if($range_exist){
            echo json_encode(array('success' => 0, 'msg' => 'Range Already Exist.'));
            die();
        }


        $bidinvite 		        = new BidInvite;
        
        $bidinvite->com_from 	        = $from;
        $bidinvite->com_to 	        = $to;
        $bidinvite->commission 	= $commission;
        $bidinvite->type         = $type;
        $bidinvite->created_by   = $loggedInUser->id;
        
        $bidinvite->save();

        if($bidinvite->id > 0){

            $addr_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);

            $log 		= new Log;
            
            $log->page 	            = 'bid-invite';
            $log->action 	        = 'Store';
            $log->bidinvite_id      = $bidinvite->id;
            $log->done_by 	        = $loggedInUser->id;
            $log->done_by_ip        = $ip;
            $log->done_by_host      = $addr_name;
            
            $log->save();

            echo json_encode(array('success' => 1));
            die();
        }
        
    }

    public function ajax_get_bidinvite(Request $request){
        
        $type = $request->type;
        $all_bidinvite = BidInvite::where('type',1)->orderBy('id','Asc')->get();
        if($type == 2){
            $all_bidinvite = BidInvite::where('type',2)->orderBy('id','Asc')->get();
        }
        
        
        $all_bidinvite_data = array();

        if ($all_bidinvite) {
            foreach ($all_bidinvite as $bidinvite) {
                
                $action_btn = '';
                $status = '';
                if ($bidinvite->status == 1) {
                    $action_btn .= ' <button class="btn btn-sm btn-warning edit-bidinvite mx-2" data-id="'.$bidinvite->id.'">Edit</button>';
                    $action_btn .= '<button class="btn btn-sm btn-danger suspend-bidinvite" data-id="'.$bidinvite->id.'">Suspend</button>';
                    $status.= '<span class="badge badge-success">active</span>';
                } else {
                    $action_btn .= '<button class="btn btn-sm btn-success resume-bidinvite mx-2" data-id="'.$bidinvite->id.'">Resume</button>';
                    $status.= '<span class="badge badge-danger">inactive</span>';
                }

                

                $type = "";

                if($bidinvite->type == 2) {
                    $type = "Upwork";
                } else if($bidinvite->type == 3) {
                    $type = "Direct";
                }else if($bidinvite->type == 4) {
                    $type = "CV Marketing";
                } else { 
                    $type = "Fiver";
                }

                $all_bidinvite_data[] = array(
                    'range'      => $bidinvite->com_from.' - '.$bidinvite->com_to ,
                    'commission' => $bidinvite->commission.'%',
                    'status'     => $status,
                    'created_at' => date("M j, Y", strtotime($bidinvite->created_at)),
                    'action'     => $action_btn
                );
            }
        }

        echo json_encode(array('data' => $all_bidinvite_data));
    }

    public function ajax_admin_get_bidinvite(Request $request){
        $bid_id = $request->bid_id;

        $bid = BidInvite::where('id',$bid_id)->first();

        if($bid){
            echo json_encode($bid);
            die();
        }

    }

    public function update_bidinvite(Request $request){
        if(Auth::user()->roles == 2 || Auth::user()->roles == 3){
            return redirect(url('/'));
        }

        $ip             = $_SERVER['REMOTE_ADDR']; 
        $loggedInUser   = Auth::user();
        $to             = $request->to;
        $from           = $request->from;
        $commission     = $request->commission;
        $type           = $request->type;
        $id             = $request->id;

        if($from > $to || $from == $to){
            echo json_encode(array('success' => 0, 'msg' => 'Invalid Range.'));
            die();
        }
        $all_ranges = BidInvite::where('type',$type)->where('id','!=',$id)->get();
        $range_exist = false;
        foreach($all_ranges as $a_r){
            
            if(($from >= $a_r->com_from && $from <= $a_r->com_to) ||
                ($to <= $a_r->com_to && $to >= $a_r->com_from) ||
                ($from <= $a_r->com_from && $to >= $a_r->com_from)
            ){
                $range_exist = true;
                break;
            }
            
            
        }
			
			
			
	    if($range_exist){
            echo json_encode(array('success' => 0, 'msg' => 'Range Already Exist.'));
            die();
        }

        $bidinvite 		        =  BidInvite::find($id);
        
        $bidinvite->com_from 	= $from;
        $bidinvite->com_to 	    = $to;
        $bidinvite->commission 	= $commission;
        
        $bidinvite->save();
        
        $addr_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        
        $log 		= new Log;
        
        $log->page 	    = 'bidinvite';
        $log->action 	    = 'Update info (------------'.$bidinvite.'-----------)';
        $log->bidinvite_id      = $bidinvite->id;
        $log->done_by 	    = $loggedInUser->id;
        $log->done_by_ip   = $ip;
        $log->done_by_host = $addr_name;
        
        $log->save();

        echo json_encode(array('success' => 1));
        die();
    
    }

    public function ajax_admin_suspend_bidinvite(Request $request){
        if(Auth::user()->roles == 2 || Auth::user()->roles == 3){
            return redirect(url('/'));
        }

        $ip             = $_SERVER['REMOTE_ADDR']; 
        $loggedInUser   = Auth::user();
        $bidinvite_id    = $request->bidinvite_id;
        $bidinvite 		= Bidinvite::where('id',$bidinvite_id)->update([
            'status' => 0
        ]);
        
        $addr_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        
        $log 		= new Log;
        
        $log->page 	        = 'bidinvite';
        $log->action 	    = 'Suspend';
        $log->bidinvite_id  = $bidinvite_id;
        $log->done_by 	    = $loggedInUser->id;
        $log->done_by_ip    = $ip;
        $log->done_by_host  = $addr_name;
        
        $log->save();
        
        echo json_encode(array('success' => 1));
        die();
        
    }

    public function ajax_admin_resume_bidinvite(Request $request){
        if(Auth::user()->roles == 2 || Auth::user()->roles == 3){
            return redirect(url('/'));
        }

        $ip             = $_SERVER['REMOTE_ADDR']; 
        $loggedInUser   = Auth::user();
        $bidinvite_id        = $request->bidinvite_id;
        $bidinvite 		= Bidinvite::where('id',$bidinvite_id)->update([
            'status' => 1
        ]);
        
        $addr_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        
        $log 		= new Log;
        
        $log->page 	    = 'bidinvite';
        $log->action 	    = 'Resume';
        $log->bidinvite_id      = $bidinvite_id;
        $log->done_by 	    = $loggedInUser->id;
        $log->done_by_ip   = $ip;
        $log->done_by_host = $addr_name;
        
        $log->save();
        
        echo json_encode(array('success' => 1));
        die();
    }
}
