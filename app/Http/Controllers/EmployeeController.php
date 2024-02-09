<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Log;
use App\Models\Platform;
use App\Models\Project;
use App\Models\Revenue;
use App\Models\BidInvite;
use App\Models\CommissionReport;
use Illuminate\Support\Facades\Hash;


class EmployeeController extends Controller
{
    public function index(){
        
        if(Auth::user()->roles == 1){
            return redirect(url('admin/dashboard'));
        }

        $monthly_earning    = 0;
        $overall_earning    = 0;
        $bid_earning        = 0;
        $invite_earning     = 0;
        $bid_commission     = 0;
        $invite_commission  = 0;
        $commission         = 0;
        $overall_commission = 0;

        $completed_project  = 0;
        $pending_project    = 0;
        $loggedInUser    = Auth::user();
        

        $first_day_this_month = date('Y-m-01');
		$last_day_this_month = date("Y-m-t");

        $revenue            = Revenue::all();
        $commission_report  = CommissionReport::where('user_id', $loggedInUser->id)->where('status',1)->get();
        
        foreach($revenue as $rev){
            if($rev->owner_id == $loggedInUser->id){
                $overall_earning += $rev->earning;
            }else if($rev->shared_id == $loggedInUser->id ){
                $overall_earning += $shared->earning;
            }
            
            if($rev->completed_on >= $first_day_this_month && $rev->completed_on <= $last_day_this_month){
                if($rev->owner_id == $loggedInUser->id){
                    $monthly_earning += $rev->earning;
                    $completed_project++;
                }else if($rev->shared_id == $loggedInUser->id ){
                    $monthly_earning += $shared->earning;
                    $completed_project++;
                }

                
            }else{
                if($rev->owner_id == $loggedInUser->id){
                    $pending_project++;
                }else if($rev->shared_id == $loggedInUser->id ){
                    $pending_project++;
                }
                
            }
        }
        
        foreach($commission_report as $com_rep){
            $overall_commission += $com_rep->commission;
            if($com_rep->created_at >= $first_day_this_month && $com_rep->created_at <= $last_day_this_month){
                $commission += $com_rep->commission;
                if($com_rep->bidinvite_type == 1){
                    $bid_earning    += $com_rep->earning; 
                    $bid_commission += $com_rep->commission; 
                }else{
                    $invite_earning     += $com_rep->earning;
                    $invite_commission  += $com_rep->commission;
                }
                
            }
        }


        $monthly_earning   = round($monthly_earning,1);
        $overall_earning   = round($overall_earning,1);
        $bid_earning       = round($bid_earning,1);
        $invite_earning    = round($invite_earning,1);
        $bid_commission    = round($bid_commission,1);
        $invite_commission = round($invite_commission,1);
        $commission        = round($commission,1);
        $overall_commission= round($overall_commission,1);

        $title = "Dashboard";
        return view('employee.index',compact('title',
            'monthly_earning',   
            'overall_earning',   
            'bid_earning',       
            'invite_earning',    
            'bid_commission',    
            'invite_commission', 
            'completed_project', 
            'pending_project',   
            'commission',        
            'overall_commission',

        ));
        
    }

    //projects functions 
    public function user_add_project(){
        if(Auth::user()->roles == 1){
            return redirect(url('admin/dashboard'));
        }

        $platform   = Platform::where('status',1)->get();
        $bd         = user::where('roles',2)->where('status',1)->get();
        $title = "My Projects";
        return view('employee.project.index', compact('title','platform','bd'));
    }

    public function user_store_project(Request $request){
        if(Auth::user()->roles == 1){
            return redirect(url('admin/dashboard'));
        }
        
        $ip              = $_SERVER['REMOTE_ADDR'];
        $loggedInUser    = Auth::user();
        $title           = $request->title;
        $project_link    = $request->project_link;
        $project_type    = $request->project_type;
        $client_name     = $request->client_name;
        $client_type     = $request->client_type;
        $amount          = $request->amount;
        $platform        = $request->platform;

        $after_fees = $amount;
        $pf = Platform::where('id',$platform)->first();

        if($pf){
            if($pf->type == 1){
                //fiver 20%
                $per = $amount*0.2;
                $after_fees = $amount - $per;
            }else if($pf->type == 2){
                //upwork 10%
                $per = $amount*0.1;
                $after_fees = $amount - $per;
            }else{
                $after_fees = $amount;
            }
        }
        
        $project 		        = new Project;
        
        $project->title 	            = $title;
        $project->project_link 	        = $project_link;
        $project->project_type 	        = $project_type;
        $project->client_name 	        = $client_name;
        $project->client_type 	        = $client_type;
        $project->platform_id           = $platform;
        $project->total_amount          = $amount;
        $project->after_fees            = $after_fees;
        $project->owner                 = $loggedInUser->id;
        $project->created_by            = $loggedInUser->id;
        
        $project->save();

        if($project->id > 0){

            $revenue 		        = new Revenue;
        
            $revenue->project_id 	        = $project->id;
            $revenue->amount 	            = $amount;
            $revenue->after_fees 	        = $after_fees;
            $revenue->project_type 	        = $project_type;
            $revenue->source_type 	        = $platform;
            $revenue->commission 	        = 0;
            $revenue->shared_commission     = 0;
            $revenue->owner_id              = $loggedInUser->id;
            $revenue->earning               = $after_fees;
            $revenue->source_p              = $pf->type;
            $revenue->source_username       = $pf->name;
            
            $revenue->save();


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

        $platform   = $request->platform;
        $month      = $request->month;
        $type      = $request->type;
        $status_f      = $request->status;
        
        if ($all_project) {
            $count = 0;
            foreach ($all_project as $project) {
                $go = false;
                if($month == date("M Y", strtotime($project->created_at))){
                    $go = true;
                    if($type != null && $status_f != null){
                        if($project->project_type == $type && $project->status == $status_f ){
                            $go = true;
                        }else{
                            $go = false;
                        }
                    }else if($type != null || $status_f != null){
                        
                        if($type != null){
                            
                            if($project->project_type == $type ){
                                $go = true;
                            }else{
                                $go = false;
                            }   
                        }

                        if($status_f != null){
                            if($project->status == $status_f ){
                                $go = true;
                            }else{
                                $go = false;
                            }
                        }
                    }
                    
                }else{
                    $go = false;
                }

                
                if(isset($request->search)){
                    // if($request->id != "all"){
                    //     if($project->owner != $request->id && $project->shared_user != $request->id){
                    //         continue;
                    //     }
                    // }
                    
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
                        $status.= '<span class="badge badge-primary">In Progress</span>';
                    } else if($project->status == 2) {
                        $action_btn .= '<button class="btn btn-sm btn-success resume-project" data-id="'.$project->id.'">Resume</button>';
                        $status.= '<span class="badge badge-primary">In Progress</span>';
                    } else{
                        $status.= '<span class="badge badge-success">Completed</span>';
                    }
                    
                    $project_type ="Invite";
                    if($project->project_type == 1){
                        $project_type ="Bid";
                    }
                    $element = '<div class="row">
                        <div class="col-12 col-md-2  bg-light d-flex align-items-center justify-content-center rounded">
                            <div>
                            '.$image.'
                        
                            </div>
                        </div>
                        <div class="col-12 col-md-7">
                            <h4 class="mb-1">'.$project->title.'</h4>
                            <p class="mb-1">'.$project->client_name.'</p>
                            
                            <div class="d-flex justify-content-start align-items-center mt-2">
                                <p class="mb-0">Created Date: <span class="text-dark">'.date("j M, Y", strtotime($project->created_at)).'</span></p>
                                <p class="mb-0 mx-4">Source: <span class="text-dark">'.$platform->name.'</span></p>
                                <p class="mb-0 mx-4">Owner: <span class="text-dark">'.$owner.'</span></p>
                                <p class="mb-0 mx-4">Type: <span class="text-dark">'.$project_type.'</span></p>
                            </div>
                            <div class=" d-flex justify-content-start align-items-end mt-3">
                                <a href="'.url('project').'/'.$project->id.'" class="btn btn-sm btn-light edit-project" >
                                <i class="fas fa-fw fa-cog"></i>
                                Settings</a> 
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="d-flex align-items-start justify-content-end mb-4">
                            '.$status.'
                            </div>
                            <div class="d-flex align-items-end justify-content-end">
                                <div class="border-right px-2">
                                    <p class="mb-0">Cost</p>
                                    <p class="mb-0 text-dark font-weight-bold">$'.$project->total_amount.'</p>
                                </div>
                                <div class="mx-2">
                                    <p class="mb-0">After Fees</p>
                                    <p class="mb-0 text-dark font-weight-bold">$'.$project->after_fees.'</p>
                                </div>
                            </div>
                            
                        </div>
                    </div>';
                    
                    $count++;

                    $all_project_data[] = array(
                        'project_data'  => $element
                    );
                }else{
                    if(!$go){
                        continue;
                    }
                    if($request->id != "all"){
                        
                        if($project->owner != $request->id && $project->shared_user != $request->id ){
                            
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
                        $status.= '<span class="badge badge-primary">In Progress</span>';
                    } else if($project->status == 2) {
                        $action_btn .= '<button class="btn btn-sm btn-success resume-project" data-id="'.$project->id.'">Resume</button>';
                        $status.= '<span class="badge badge-primary">In Progress</span>';
                    } else{
                        $status.= '<span class="badge badge-success">Completed</span>';
                    }
                    
                    $project_type ="Invite";
                    if($project->project_type == 1){
                        $project_type ="Bid";
                    }
                    $element = '<div class="row">
                        <div class="col-12 col-md-2  bg-light d-flex align-items-center justify-content-center rounded">
                            <div>
                            '.$image.'
                        
                            </div>
                        </div>
                        <div class="col-12 col-md-7">
                            <h4 class="mb-1">'.$project->title.'</h4>
                            <p class="mb-1">'.$project->client_name.'</p>
                            
                            <div class="d-flex justify-content-start align-items-center mt-2">
                                <p class="mb-0">Created Date: <span class="text-dark">'.date("j M, Y", strtotime($project->created_at)).'</span></p>
                                <p class="mb-0 mx-4">Source: <span class="text-dark">'.$platform->name.'</span></p>
                                <p class="mb-0 mx-4">Owner: <span class="text-dark">'.$owner.'</span></p>
                                <p class="mb-0 mx-4">Type: <span class="text-dark">'.$project_type.'</span></p>
                            </div>
                            <div class=" d-flex justify-content-start align-items-end mt-3">
                                <a href="'.url('user/project').'/'.$project->id.'" class="btn btn-sm btn-light edit-project" >
                                <i class="fas fa-fw fa-cog"></i>
                                Settings</a> 
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="d-flex align-items-start justify-content-end mb-4">
                            '.$status.'
                            </div>
                            <div class="d-flex align-items-end justify-content-end">
                                <div class="border-right px-2">
                                    <p class="mb-0">Cost</p>
                                    <p class="mb-0 text-dark font-weight-bold">$'.$project->total_amount.'</p>
                                </div>
                                <div class="mx-2">
                                    <p class="mb-0">After Fees</p>
                                    <p class="mb-0 text-dark font-weight-bold">$'.$project->after_fees.'</p>
                                </div>
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
                <a type="button" href="#" data-toggle="modal" data-target="#completeModal" class="btn-sm px-3 btn btn-success text-sm">Mark Complete</a>
            </div>';
            }
            
            $element = '<div class="row border-bottom pb-3 mb-4">
                <div class="col-12 col-md-4   d-flex align-items-center justify-content-start ">
                    <div class="rounded bg-light p-5">
                    '.$image.'
                
                    </div>
                    <div class="mx-3">
                        <p class="mb-1 h4">'.$project->title.'</p>
                        <p class="mb-1 h6">'.$project->client_name.'</p>
                    </div>
                </div>
                <div class="col-12 col-md-8">
                    <div class="d-flex align-items-start justify-content-end mb-4">
                    '.$status.'
                    </div>
                    <div class="d-flex align-items-end justify-content-end">
                        <div class="border-right px-2">
                            <p class="mb-0">Cost</p>
                            <p class="mb-0 text-dark font-weight-bold">$'.$project->total_amount.'</p>
                        </div>
                        <div class="mx-2">
                            <p class="mb-0">After Fees</p>
                            <p class="mb-0 text-dark font-weight-bold">$'.$project->after_fees.'</p>
                        </div>
                    </div>
                    '.$buttons.'
                   
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
                    <p class="font-weight-bold fs-14">Source: <span class="text-dark font-weight-normal">'.$platform->name.'</span></p>
                    <p class="font-weight-bold fs-14">Shared User: <span class="text-dark font-weight-normal">'.$s_user.'</span></p>
                    <p class="font-weight-bold fs-14">Created On: <span class="text-dark font-weight-normal">'.date("j M, Y", strtotime($project->created_at)).'</span></p>
                    <p class="font-weight-bold fs-14">Deadline: <span class="text-dark font-weight-normal">'.$deadline.'</span></p>
                    <p class="font-weight-bold fs-14">Completed On: <span class="text-dark font-weight-normal">'.$completed_date.'</span></p>
                    
                    
                </div>';

            $bd_users = User::where('roles',2)->where('status',1)->get();
            $s_users = User::where('roles',2)->where('status',1)->get();

            $title = $project->title;
           
            return view('employee.project.single', compact('title','element','project','all_platform','modal_ele','bd_users','s_users'));
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

        $ip                 = $_SERVER['REMOTE_ADDR'];
        $loggedInUser       = Auth::user();
        $title              = $request->title;
        $platform           = $request->platform;
        $assign_user        = $request->assign_user;
        $share_user         = $request->share_user;
        $share_commission   = $request->share_commission;
        $project_link       = $request->project_link;
        $project_type       = $request->project_type;
        $amount             = $request->amount;
        $client_name        = $request->client_name;

        $after_fees = $amount;
        $pf = Platform::where('id',$platform)->first();

        if($pf){
            if($pf->type == 1){
                //fiver 20%
                $per = $amount*0.2;
                $after_fees = $amount - $per;
            }else if($pf->type == 2){
                //upwork 10%
                $per = $amount*0.1;
                $after_fees = $amount - $per;
            }else{
                $after_fees = $amount;
            }
        }

        $owner_amount = 0;
        $share_amount = 0;
        
        if($share_user != 0){

            if($share_commission != 0){
                
                $share_amount = $after_fees*($share_commission/100);
                 
            }else{
                $share_commission = 50;
                $share_amount = $after_fees*($share_commission/100);
            }
            $owner_amount = $after_fees - $share_amount;
            
        }else{
            $owner_amount = $after_fees;
        }

        
        $id = intval($request->id);
        $project 		        = Project::find($id);
        
        $project->title 	            = $title;
        $project->client_name 	        = $client_name;
        $project->platform_id           = $platform;
        $project->total_amount          = $amount;
        $project->after_fees            = $after_fees;
        $project->owner 	            = $assign_user;
        if($share_user != 0){
            $project->shared_user 	    = $share_user;
        }

        $project->save();

        
        $revenue 		        = Revenue::where('project_id',$project->id)->first();
        
        $revenue->amount 	            = $amount;
        $revenue->after_fees 	        = $after_fees;
        $revenue->project_type 	        = $project_type;
        $revenue->source_type 	        = $platform;
        $revenue->earning 	            = $owner_amount;
        $revenue->commission 	        = 0;
        $revenue->shared_earning        = $share_amount;
        $revenue->shared_commission     = 0;
        $revenue->owner_id              = $assign_user;
        if($share_user != 0){
            $revenue->shared_id         = $share_user;
        }
        
        
        $revenue->save();


        

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

        $revenue 		        = Revenue::where('project_id',$project->id)->first();
        
        $owner_earning  = $revenue->earning;
        $shared_earning = 0;
        if($revenue->shared_earning != 0){
            $shared_earning = $revenue->shared_earning;
        }

        $comm = BidInvite::where('type', $revenue->project_type)->where('status',1)->get();

        $commission_exist = true;
        $owner_commission   = 0;
        $shared_commission  = 0;
		foreach($comm as $c){
			
			
			if($owner_earning >= $c->com_from &&  $owner_earning <= $c->com_to ){
				
				$owner_commission = $owner_earning * ($c->commission/100);
                break;
				
			} 
			
		}

        if($shared_earning != 0){
            foreach($comm as $c){
			
			
                if($shared_earning >= $c->com_from &&  $shared_earning <= $c->com_to ){
                    
                    $shared_commission = $shared_earning * ($c->commission/100);
                    break;
                    
                } 
                
            }
        }

        $revenue->commission 	        = $owner_commission;
        $revenue->shared_commission     = $shared_commission;
        $revenue->completed_on          = now();
        $revenue->save();

        $first_day_this_month = date('Y-m-01');
		$last_day_this_month = date("Y-m-t");
        $owner_commission_report = CommissionReport::where('user_id', $revenue->owner_id)->first();
        
        $create_new = false;
        if($owner_commission_report){
            if($owner_commission_report->created_at >= $first_day_this_month && $owner_commission_report->created_at <= $last_day_this_month) {
                
                if($owner_commission_report->bidinvite_type == $revenue->project_type){
                    $nearning = $owner_commission_report->earning + $revenue->earning;
                    foreach($comm as $c){
                        
                        
                        if($nearning >= $c->com_from &&  $nearning <= $c->com_to ){
                            
                            $owner_commission = $nearning * ($c->commission/100);
                            break;
                            
                        } 
                        
                    }
                    CommissionReport::where('id', $owner_commission_report->id)->update([
                        'earning' => $nearning,
                        'commission' => $owner_commission 
                    ]);
                    
                }else{
                    $create_new = true;
                }
            }else{
                $create_new = true;
            }
        }else{
            $create_new = true;
        }
        if($create_new){
            
            $cr 		        = new CommissionReport;
            $cr->user_id        = $revenue->owner_id;
            $cr->bidinvite_type = $revenue->project_type;
            $cr->earning        = $revenue->earning;
            $cr->commission     = $owner_commission;
            $cr->save();
            
        }
        if($shared_earning != 0){
            $shared_commission_report = CommissionReport::where('user_id', $revenue->shared_id)->first();
            $create_new_shared = false;
            if($shared_commission_report){
                if($shared_commission_report->created_at >= $first_day_this_month && $shared_commission_report->created_at <= $last_day_this_month) {
                    
                    if($shared_commission_report->bidinvite_type == $revenue->project_type){
                        $nearning = $shared_commission_report->earning + $revenue->shared_earning;
                        foreach($comm as $c){
                            
                            
                            if($nearning >= $c->com_from &&  $nearning <= $c->com_to ){
                                
                                $shyared_commission = $nearning * ($c->commission/100);
                                break;
                                
                            } 
                            
                        }
                        CommissionReport::where('id', $shared_commission_report->id)->update([
                            'earning' => $nearning,
                            'commission' => $shyared_commission 
                        ]);
                        
                    }else{
                        $create_new_shared = true;
                    }
                } else {
                    $create_new_shared = true;
                }
            }else{
                        
               $create_new_shared = true;
            }
            if($create_new_shared){
                $cr 		        = new CommissionReport;
                $cr->user_id        = $revenue->shared_id;
                $cr->bidinvite_type = $revenue->project_type;
                $cr->earning        = $revenue->shared_earning;
                $cr->commission     = $shared_commission;
                $cr->save();
            }
        }
        
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
        $bd         = user::where('roles',2)->where('status',1)->get();
        return view('employee.project.shared', compact('title','platform','bd'));
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

        $platform   = $request->platform;
        $month      = $request->month;
        $type      = $request->type;
        $status_f      = $request->status;
        
        if ($all_project) {
            $count = 0;
            foreach ($all_project as $project) {
                $go = false;
                if($month == date("M Y", strtotime($project->created_at))){
                    $go = true;
                    if($type != null && $status_f != null){
                        if($project->project_type == $type && $project->status == $status_f ){
                            $go = true;
                        }else{
                            $go = false;
                        }
                    }else if($type != null || $status_f != null){
                        
                        if($type != null){
                            
                            if($project->project_type == $type ){
                                $go = true;
                            }else{
                                $go = false;
                            }   
                        }

                        if($status_f != null){
                            if($project->status == $status_f ){
                                $go = true;
                            }else{
                                $go = false;
                            }
                        }
                    }
                    
                }else{
                    $go = false;
                }

                
                
                if(isset($request->search)){
                    // if($request->id != "all"){
                    //     if($project->owner != $request->id && $project->shared_user != $request->id){
                    //         continue;
                    //     }
                    // }
                    
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
                        $status.= '<span class="badge badge-primary">In Progress</span>';
                    } else if($project->status == 2) {
                        $action_btn .= '<button class="btn btn-sm btn-success resume-project" data-id="'.$project->id.'">Resume</button>';
                        $status.= '<span class="badge badge-primary">In Progress</span>';
                    } else{
                        $status.= '<span class="badge badge-success">Completed</span>';
                    }
                    
                    $project_type ="Invite";
                    if($project->project_type == 1){
                        $project_type ="Bid";
                    }
                    $element = '<div class="row">
                        <div class="col-12 col-md-2  bg-light d-flex align-items-center justify-content-center rounded">
                            <div>
                            '.$image.'
                        
                            </div>
                        </div>
                        <div class="col-12 col-md-7">
                            <h4 class="mb-1">'.$project->title.'</h4>
                            <p class="mb-1">'.$project->client_name.'</p>
                            
                            <div class="d-flex justify-content-start align-items-center mt-2">
                                <p class="mb-0">Created Date: <span class="text-dark">'.date("j M, Y", strtotime($project->created_at)).'</span></p>
                                <p class="mb-0 mx-4">Source: <span class="text-dark">'.$platform->name.'</span></p>
                                <p class="mb-0 mx-4">Owner: <span class="text-dark">'.$owner.'</span></p>
                                <p class="mb-0 mx-4">Type: <span class="text-dark">'.$project_type.'</span></p>
                            </div>
                            <div class=" d-flex justify-content-start align-items-end mt-3">
                                <a href="'.url('project').'/'.$project->id.'" class="btn btn-sm btn-light edit-project" >
                                <i class="fas fa-fw fa-cog"></i>
                                Settings</a> 
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="d-flex align-items-start justify-content-end mb-4">
                            '.$status.'
                            </div>
                            <div class="d-flex align-items-end justify-content-end">
                                <div class="border-right px-2">
                                    <p class="mb-0">Cost</p>
                                    <p class="mb-0 text-dark font-weight-bold">$'.$project->total_amount.'</p>
                                </div>
                                <div class="mx-2">
                                    <p class="mb-0">After Fees</p>
                                    <p class="mb-0 text-dark font-weight-bold">$'.$project->after_fees.'</p>
                                </div>
                            </div>
                            
                        </div>
                    </div>';
                    
                    $count++;

                    $all_project_data[] = array(
                        'project_data'  => $element
                    );
                }else{
                    
                    if(!$go){
                        continue;
                    }
                    
                        
                    if( $project->shared_user != $loggedInUser->id ){
                        continue;
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
                        $status.= '<span class="badge badge-primary">In Progress</span>';
                    } else if($project->status == 2) {
                        $action_btn .= '<button class="btn btn-sm btn-success resume-project" data-id="'.$project->id.'">Resume</button>';
                        $status.= '<span class="badge badge-primary">In Progress</span>';
                    } else{
                        $status.= '<span class="badge badge-success">Completed</span>';
                    }
                    
                    $project_type ="Invite";
                    if($project->project_type == 1){
                        $project_type ="Bid";
                    }
                    $element = '<div class="row">
                        <div class="col-12 col-md-2  bg-light d-flex align-items-center justify-content-center rounded">
                            <div>
                            '.$image.'
                        
                            </div>
                        </div>
                        <div class="col-12 col-md-7">
                            <h4 class="mb-1">'.$project->title.'</h4>
                            <p class="mb-1">'.$project->client_name.'</p>
                            
                            <div class="d-flex justify-content-start align-items-center mt-2">
                                <p class="mb-0">Created Date: <span class="text-dark">'.date("j M, Y", strtotime($project->created_at)).'</span></p>
                                <p class="mb-0 mx-4">Source: <span class="text-dark">'.$platform->name.'</span></p>
                                <p class="mb-0 mx-4">Owner: <span class="text-dark">'.$owner.'</span></p>
                                <p class="mb-0 mx-4">Type: <span class="text-dark">'.$project_type.'</span></p>
                            </div>
                            <div class=" d-flex justify-content-start align-items-end mt-3">
                                <a href="'.url('user/project').'/'.$project->id.'" class="btn btn-sm btn-light edit-project" >
                                <i class="fas fa-fw fa-cog"></i>
                                Settings</a> 
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="d-flex align-items-start justify-content-end mb-4">
                            '.$status.'
                            </div>
                            <div class="d-flex align-items-end justify-content-end">
                                <div class="border-right px-2">
                                    <p class="mb-0">Cost</p>
                                    <p class="mb-0 text-dark font-weight-bold">$'.$project->total_amount.'</p>
                                </div>
                                <div class="mx-2">
                                    <p class="mb-0">After Fees</p>
                                    <p class="mb-0 text-dark font-weight-bold">$'.$project->after_fees.'</p>
                                </div>
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
