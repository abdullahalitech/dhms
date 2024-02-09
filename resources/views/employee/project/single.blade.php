@include('layouts.employee_header')
<style>
.codex-editor.codex-editor--narrow::-webkit-scrollbar {
    display: none;
}
.datepicker {
    z-index: 1600 !important; /* has to be larger than 1050 */
}
</style>
        <!-- Begin Page Content -->
        <div class="container-fluid">
            <!-- Content Row -->
            <div class="row">

                <div class="col-12">
                    <div class="card shadow mb-4">
                        
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12 col-md-12">
                                    {!!$element!!}
                                </div>
                                <div class="col-12 col-md-9 px-4">
                                    <h4 class="mb-2 font-weight-bold text-dark border-bottom border-light pb-2">Project Details</h4>
                                    {!!$project->description_html!!}
                                </div>

                                <div class="col-12 col-md-3 mb-4">

                                    <!-- Illustrations -->
                                    <div class="card shadow mb-4">
                                        <div class="card-header py-3">
                                            <h6 class="m-0 font-weight-bold text-primary">Important Info</h6>
                                        </div>
                                        <div class="card-body">
                                            {!!$modal_ele!!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </div>
            <!-- End of Main Content -->

           
    <!-- Add Modal-->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Project</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="step1" class="step1" novalidate data-parsley-validate>
                        <div class="row">
                            <div class="col-12">
                                <p class="error text-danger text-sm"></p>
                            </div> 
                            
                            <div class="col-12 col-md-6  mb-2">
                                <label for="title" class="fs-6 text-secondary">Title</label>
                                <input type="text" name="title"  class="title form-control" value="{{$project->title}}" required="" data-parsley-group="block-1">
                            </div>
                            <div class="col-12 col-md-6  mb-2">
                                <label for="client_name" class="fs-6 text-secondary">Client Name</label>
                                <input type="text" name="client_name"  class="client_name form-control" value="{{$project->client_name}}" required="" data-parsley-group="block-1">
                            </div>
                            <div class="col-12 col-md-12  mb-2">
                                <label for="project_link" class="fs-6 text-secondary">Project Link</label>
                                <input type="url" name="project_link" value="{{$project->project_link}}" class="project_link form-control" required="" data-parsley-group="block-1" data-parsley-type="url">
                            </div>
                            <div class="col-12 col-md-6  mb-2">
                                <label for="project_type" class="fs-6 text-secondary">Project Type</label>
                                <select id="project_type" name="project_type" class="project_type form-select form-control" required="" data-parsley-group="block-1">
                                    <option value="1" <?php if($project->project_type == 1){ echo 'selected'; } ?>>Bid</option>
                                    <option value="2" <?php if($project->project_type == 2){ echo 'selected'; } ?>>Invite</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6  mb-2">
                                <label for="amount" class="fs-6 text-secondary">Total Amount</label>
                                <input type="text" name="amount"  class="amount form-control" required="" value="{{$project->total_amount}}" data-parsley-group="block-1" data-parsley-type="number">
                            </div>
                            <div class="col-12 col-md-6 mb-2">
                                <label for="platform" class="fs-6 text-secondary">Source</label>
                                <select id="platform" name="platform" class="platform form-select form-control" required="" data-parsley-group="block-1">
                                    <option value="" selected="">Select</option>
                                    @foreach($all_platform as $p)
                                        <option data-com="{{$p->commission}}" value="{{$p->id}}" <?php if($project->platform_id == $p->id){ echo 'selected'; } ?>>{{$p->name}}
                                        @if($p->type == 1)
                                            (Fiver)
                                        @elseif($p->type == 2)
                                            (Upwork)
                                        @elseif($p->type == 3)
                                            (Direct)
                                        @else
                                            (CV Marketing)
                                        @endif


                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-6 mb-2">
                                <label for="assign_user" class="fs-6 text-secondary">Owner</label>
                                <select id="assign_user" name="assign_user" class="assign_user form-select form-control" required="" data-parsley-group="block-3">
                                <option value="">Select</option>

                                @foreach($bd_users as $bd)
                                    <option value="{{$bd->id}}" <?php if($project->owner == $bd->id){ echo 'selected'; } ?>>{{$bd->name}}</option>
                                @endforeach
                                
                                </select>
                            </div>
                            <div class="col-12 col-md-6 mb-2">
                                <label for="shared_user" class="fs-6 text-secondary">Shared User</label>
                                <select id="shared_user" name="shared_user" class="shared_user form-select form-control" required="" data-parsley-group="block-3">
                                <option value="">Select</option>

                                @foreach($s_users as $s)
                                    <option value="{{$s->id}}" <?php if($project->shared_user == $s->id){ echo 'selected'; } ?>>{{$s->name}}</option>
                                @endforeach
                                
                                </select>
                            </div>

                            
                            <div class="col-12 col-md-6  mb-2 shared-com-div d-none">
                                <label for="shared_com" class="fs-6 text-secondary">Shared User Commission</label>
                                <input type="text" name="shared_com"  class="shared_com form-control" data-parsley-group="block-1" data-parsley-type="number">
                            </div>

                            
                            <div class="col-12 text-end">
                                <button type="button" id="submit-btn" class="btn btn-primary btn-sm px-4 mt-2 float-right">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

     <!-- Deadline Modal-->
     <div class="modal fade" id="deadlineModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Deadline</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label for="eName" class="fs-6 text-secondary">Set the deadline for meeting the client's requirement on time</label>
                    <div id="inline" data-date="01/05/2020"></div>
                    <input type="text" name="datepicker" id="deadline" class="form-control">
                </div>
                
                <div class="col-12 text-end mb-4">
                    <div class="d-flex justify-content-end align-items-center">
                        <button class="btn btn-secondary btn-sm" type="button">Cancel</button>
                        <button type="button" class="btn btn-primary update-deadline-btn btn-sm mx-2">Update</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    

     

    <!-- suspend modal -->
    <div class="modal fade" id="completeModal" tabindex="-1" role="dialog" aria-labelledby="completeModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Mark Complete</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-success">Great job on completing the task! Your hard work and dedication are truly appreciated.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="final-complete">Mark Complete</button>
                </div>
            </div>
        </div>
    </div> 
    @include('layouts.employee_footer')
    <script>
        
            $(document).ready(function(){
                const elem = document.querySelector('input[name="datepicker"]');
                const datepicker = new Datepicker(elem, {
                    // options here
                });
            })
    </script>
    <script>
   
    $(document).on('click','.update-deadline-btn',function(){
        var deadline = $('#deadline').val();
        if(deadline != ""){
            $(this).attr('disabled',true);
            $(this).text('Processing');
            $.ajax({
                url: "{{ url('user/set-deadline') }}",
                method: "POST",
                data: { 
                    _token: "{{ @csrf_token() }}", 	
                    id 	 :"{{$project->id}}", 	
                    deadline :deadline
                },
                success: function(data) {
                    var d = $.parseJSON( data );

                    if( d.success == 1 ) {

                        $('#deadlineModal').modal('hide');
                        $('.update-deadline-btn').attr('disabled', false);
                        $('.update-deadline-btn').text('Update');
                        toastr.success('Updated Successfuly!');
                        setTimeout(function() { 
                            window.location.reload();
                        }, 3000);
                        
                    } else {
                        
                        
                        
                    }
                }
            });
        }
        
    })
    
    $(document).on('click','.update-shared-btn',function(){
        var share_user = $('#shared_user').val();
        if(share_user != ""){
            $(this).attr('disabled',true);
            $(this).text('Processing');
            $.ajax({
                url: "{{ url('user/set-share') }}",
                method: "POST",
                data: { 
                    _token: "{{ @csrf_token() }}", 	
                    id 	 :"{{$project->id}}", 	
                    share_user :share_user
                },
                success: function(data) {
                    var d = $.parseJSON( data );

                    if( d.success == 1 ) {

                        $('#shareModal').modal('hide');
                        toastr.success('Updated Successfuly!');
                        setTimeout(function() { 
                            window.location.reload();
                        }, 3000);
                        
                    } else {
                        
                        
                        
                    }
                }
            });
        }
        
    })

    $(document).ready(function() {
        $('#form-employee').parsley();
        
    });
 
    

    $(document).on('change','#shared_user',function(){
        if($(this).val() != ""){
            $('.shared-com-div').removeClass('d-none');
        } else {
            $('.shared-com-div').addClass('d-none');
        }
    })

    $(document).on('click', '#submit-btn', function(){
        $(document).find('#step1').parsley().whenValidate({
            group: 'block-1'
        }).done(function(){
            $('#submit-btn').attr('disabled',true);
            $('#submit-btn').text('Processing');
            
            var title               = $('.title').val();
            var client_name         = $('.client_name').val();
            var project_type        = $('.project_type').val();
            var project_link        = $('.project_link').val();
            var platform 	        = $('.platform').val();
            var assign_user         = $('#assign_user').val();
            var share_user          = $('#shared_user').val();
            var share_commission    = $('.shared_com').val();
            var amount              = $('.amount').val();

            if(share_user == ""){
                share_commission = 0;
                share_user = 0;
            }

            $.ajax({
                url: "{{ url('user/edit-project') }}",
                method: "POST",
                data: { 
                    _token: "{{ @csrf_token() }}", 	
                    title 	 :title, 		
                    client_name    :client_name,
                    platform:platform,
                    assign_user:assign_user,
                    share_user:share_user,
                    share_commission:share_commission, 
                    project_type:project_type,
                    project_link:project_link,
                    amount:amount,
                    id:"{{$project->id}}"
                },
                success: function(data) {
                    var d = $.parseJSON( data );

                    if( d.success == 1 ) {
                        $('#editModal').modal('hide');
                        toastr.success('Updated Successfuly!');
                        setTimeout(function() { 
                            window.location.reload();
                        }, 2000);
                        
                    } else {
                        
                        
                        
                    }
                }
            });
        });
    })

    $(document).on('click', '#final-complete', function() {
        
        $.ajax({
            url: "{{ url('ajax/user/mark-completed') }}",
            type: "POST",
            data: { _token: "{{ @csrf_token() }}", id:"{{$project->id}}" 
        },
            success: function(data) {
                    $('#completeModal').modal('hide');
                    toastr.success('Updated Successfuly!');
                    setTimeout(function() { 
                        window.location.reload();
                    }, 2000);
            }
        });
    });

    function empty_value(){
        $('.title').val('');
        $('.client_email').val('');
        $('.client_name').val('');
        $('.amount').val('');
        $('.platform').val('');
        $('#platform').val('');
        $('.amount').val('');
        $('.revenue').attr('disabled',false);
        $('.commission').attr('disabled', false);
        $('.revenue').val('');
        $('.commission').val('');
        editor.blocks.clear();
        $('#step1').removeClass('d-none');
        $('#step2').addClass('d-none');
    }

    
    </script>
