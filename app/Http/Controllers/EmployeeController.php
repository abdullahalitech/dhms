<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Log;
use App\Models\Platform;
use App\Models\Project;
use Illuminate\Support\Facades\Hash;


class EmployeeController extends Controller
{
    public function index(){
        
        if(Auth::user()->roles == 1){
            return redirect(url('admin/dashboard'));
        }

        $monthly_earning = 0;
        $overall_earning = 0;
        $completed_project = 0;
        $pending_project = 0;

        $loggedInUser    = Auth::user();
        

        $first_day_this_month = date('Y-m-01');
		$last_day_this_month = date("Y-m-t");

        $projects = Project::where('owner',$loggedInUser->id)->get();

        foreach($projects as $project){    
            $overall_earning += $project->commission;    
            if($project->created_at >= $first_day_this_month && $project->created_at <= $last_day_this_month){
                $monthly_earning += $project->commission;
            }
            
            if($project->status == 3){
                $completed_project += 1;
            }elseif ($project->status == 1){
                $pending_project += 1;
            }else{
                
            }

        }

        $title = "Dashboard";
        return view('employee.index',compact('title','overall_earning','monthly_earning','completed_project','pending_project'));
        
    }

    //projects functions 
    public function user_add_project(){
        if(Auth::user()->roles == 1){
            return redirect(url('admin/dashboard'));
        }

        $platform = Platform::where('status',1)->get();
        $title = "My Projects";
        return view('employee.project.index', compact('title','platform'));
    }

    public function user_store_project(Request $request){
        if(Auth::user()->roles == 1){
            return redirect(url('admin/dashboard'));
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

    public function user_ajax_get_project(Request $request){
        if(Auth::user()->roles == 1){
            return redirect(url('admin/dashboard'));
        }

        $loggedInUser    = Auth::user();
        
        $all_project = array();
        if(isset($request->search)){
            $platform = Platform::where('type', $request->search)->get();
            foreach($platform as $p){
                $ind_project = Project::where('platform_id', $p->id)->where('owner',$loggedInUser->id)->get();
                foreach($ind_project as $i_p){
                    array_push($all_project, $i_p);
                }
                
                
            }
        }else{
            $all_project = Project::where('owner',$loggedInUser->id)->get();
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
                                <a href="'.url('user/project').'/'.$project->id.'" class="btn btn-sm btn-danger edit-project" >
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
                                <a href="'.url('user/project').'/'.$project->id.'" class="btn btn-sm btn-danger edit-project" >
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

    public function ajax_user_get_project(Request $request){
        $project_id = $request->project_id;

        $project = Project::where('id',$project_id)->first();

        if($project){
            echo json_encode($project);
            die();
        }

    }

    public function update_project(Request $request){
        if(Auth::user()->roles == 1){
            return redirect(url('admin/dashboard'));
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


    public function user_project_single($id){
        if(Auth::user()->roles == 1){
            return redirect(url('admin/dashboard'));
        }


        $loggedInUser   = Auth::user();
        $id             = intval($id);
        $project 	    = Project::where('id',$id)->first();
        
        $all_platform = Platform::where('status',1)->get();
        if($project){

            if($project->owner == $loggedInUser->id || $project->shared_user == $loggedInUser->id){

            }else{
                abort(403);
                exit();
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

            $buttons = "";

            if($project->status != 3){
                $d_btn = "";
                if($project->due_date == NULL){
                    $d_btn .= '<a type="button" href="#" data-toggle="modal" data-target="#deadlineModal" class="btn-sm mr-2 px-3 btn btn-danger text-sm">Deadline</a>';
                }
                $buttons =' <div class="text-wrap text-break d-flex gap-3 align-items-center justify-content-end my-3">
                '.$d_btn.'
                <a type="button" href="#" data-toggle="modal" data-target="#editModal" class="edit btn-sm mr-2 px-3 btn btn-warning text-sm">Edit</a>
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
            return view('employee.project.single', compact('title','element','project','all_platform','modal_ele','bd_users','s_users','desc_array'));
        }
        

        
    }

    public function user_set_deadline(Request $request){
        if(Auth::user()->roles == 1){
            return redirect(url('admin/dashboard'));
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
    
    public function user_set_share(Request $request){
        if(Auth::user()->roles == 1){
            return redirect(url('admin/dashboard'));
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

    public function user_edit_project(Request $request){
        if(Auth::user()->roles == 1){
            return redirect(url('admin/dashboard'));
        }

        $ip              = $_SERVER['REMOTE_ADDR'];; 
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
    

    public function ajax_user_mark_completed(Request $request){
        if(Auth::user()->roles == 1){
            return redirect(url('admin/dashboard'));
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

    //shared projects
    public function user_add_project_shared(){
        if(Auth::user()->roles == 1){
            return redirect(url('admin/dashboard'));
        }

        $platform = Platform::where('status',1)->get();
        $title = "Shared Projects";
        return view('employee.project.shared', compact('title','platform'));
    }

    public function user_ajax_get_project_shared(Request $request){
        if(Auth::user()->roles == 1){
            return redirect(url('admin/dashboard'));
        }

        $loggedInUser    = Auth::user();
        
        $all_project = array();
        if(isset($request->search)){
            $platform = Platform::where('type', $request->search)->get();
            foreach($platform as $p){
                $ind_project = Project::where('platform_id', $p->id)->where('shared_user',$loggedInUser->id)->get();
                foreach($ind_project as $i_p){
                    array_push($all_project, $i_p);
                }
                
                
            }
        }else{
            $all_project = Project::where('shared_user',$loggedInUser->id)->get();
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
                           
                            <div class=" d-flex justify-content-end align-items-end mt-5">
                                <a href="'.url('user/project/shared').'/'.$project->id.'" class="btn btn-sm btn-danger edit-project" >
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
                            
                            <div class=" d-flex justify-content-end align-items-end mt-5">
                                <a href="'.url('user/project/shared').'/'.$project->id.'" class="btn btn-sm btn-danger edit-project" >
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

    public function user_project_single_shared($id){
        if(Auth::user()->roles == 1){
            return redirect(url('admin/dashboard'));
        }


        $loggedInUser   = Auth::user();
        $id             = intval($id);
        $project 	    = Project::where('id',$id)->first();
        
        $all_platform = Platform::where('status',1)->get();
        if($project){

            if($project->shared_user == $loggedInUser->id){

            }else{
                abort(403);
                exit();
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

            $buttons = "";

            if($project->status != 3){
                $d_btn = "";
                if($project->due_date == NULL ){
                    $d_btn .= '<a type="button" href="#" data-toggle="modal" data-target="#deadlineModal" class="btn-sm mr-2 px-3 btn btn-danger text-sm">Deadline</a>';
                }
                if($project->shared_user == $loggedInUser->id){
                    $d_btn = "";
                }
                $buttons =' <div class="text-wrap text-break d-flex gap-3 align-items-center justify-content-end my-3">
                '.$d_btn.'
                <a type="button" href="#" data-toggle="modal" data-target="#editModal" class="edit btn-sm mr-2 px-3 btn btn-warning text-sm">Edit</a>
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
            return view('employee.project.single_shared', compact('title','element','project','all_platform','modal_ele','bd_users','s_users','desc_array'));
        }
        

        
    }
}
