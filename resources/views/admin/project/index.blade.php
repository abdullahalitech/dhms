@include('layouts.admin_header')
<style>
.codex-editor.codex-editor--narrow::-webkit-scrollbar {
    display: none;
}
#month-select, #platform-select, #type-select, #status-select{
    height: 1.5rem !important;
    font-size:0.9rem !important;
    padding: 0px 4px
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
                                <div class="col-12 col-md-2">
                                    <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#AddModal">
                                        Add Project
                                    </a>
                                </div>
                                <div class="col-12 col-md-10">
                                    <div class="d-flex justify-content-end align-items-center">
                                        <div class="text-end mx-2">
                                            <select id="status-select" name="status-select" class="status-select form-select form-control">
                                                <option value='' selected="">Status</option>
                                                <option value='1'>In progress</option>
                                                <option value='3'>Completed</option>
                                            </select>
                                        </div>
                                        <div class="text-end mx-2">
                                            <select id="type-select" name="type-select" class="type-select form-select form-control">
                                                <option value='' selected="">Type</option>
                                                <option value='1'>Bid</option>
                                                <option value='2'>Invite</option>
                                            </select>
                                        </div>
                                        <div class="text-end mx-2">
                                            <select id="month-select" name="month-select" class="month-select form-select form-control">
                                                <option value='{{date("M Y")}}' selected="">{{date("M Y")}}</option>
                                                <option value="{{date('M Y',strtotime('-1 months'))}}" >{{date("M Y",strtotime('-1 months'))}}</option>
                                                <option value="{{date('M Y',strtotime('-2 months'))}}" >{{date("M Y",strtotime('-2 months'))}}</option>
                                                <option value="{{date('M Y',strtotime('-3 months'))}}" >{{date("M Y",strtotime('-3 months'))}}</option>
                                            </select>
                                        </div>
                                        <!-- <div class="text-end mx-2">
                                            <select id="platform-select" name="platform-select" class="platform-select form-select form-control">
                                                <option value="" selected="">Platform</option>
                                                <option value="1" >Fiver</option>
                                                <option value="2">Upwork</option>
                                                <option value="3">Direct</option>
                                                <option value="4">CV Marketing</option>
                                            </select>
                                        </div> -->
                                    </div>
                                </div>
                            </div>

                            
                            <div class="table-responsive">
                                <table class="table border-0" id="project-table" width="100%" cellspacing="0">
                                    <thead class="d-none">
                                        <tr>
                                            <th>Project</th>
                                        </tr>
                                    </thead>
                                    
                                    <tbody>
                                        
                                    </tbody>
                                </table>
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
    <div class="modal fade" id="AddModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Project</h5>
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
                            
                            <div class="col-12 col-md-12  mb-2">
                                <label for="title" class="fs-6 text-secondary">Title</label>
                                <input type="text" name="title"  class="title form-control" required="" data-parsley-group="block-1">
                            </div>
                            <div class="col-12 col-md-12  mb-2">
                                <label for="project_link" class="fs-6 text-secondary">Project Link</label>
                                <input type="url" name="project_link"  class="project_link form-control" required="" data-parsley-group="block-1" data-parsley-type="url">
                            </div>
                            <div class="col-12 col-md-4  mb-2">
                                <label for="project_type" class="fs-6 text-secondary">Project Type</label>
                                <select id="project_type" name="project_type" class="project_type form-select form-control" required="" data-parsley-group="block-1">
                                    <option value="1" selected="">Bid</option>
                                    <option value="2">Invite</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-4  mb-2">
                                <label for="client_name" class="fs-6 text-secondary">Client Name</label>
                                <input type="text" name="client_name"  class="client_name form-control" required="" data-parsley-group="block-1">
                            </div>
                            <div class="col-12 col-md-4  mb-2">
                                <label for="client_type" class="fs-6 text-secondary">Client Type</label>
                                <select id="client_type" name="client_type" class="client_type form-select form-control" required="" data-parsley-group="block-1">
                                    <option value="1" selected="">New</option>
                                    <option value="2">Old</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-4  mb-2">
                                <label for="amount" class="fs-6 text-secondary">Total Amount</label>
                                <input type="text" name="amount"  class="amount form-control" required="" data-parsley-group="block-1" data-parsley-type="number">
                            </div>
                            <div class="col-12 col-md-4 mb-2">
                                <label for="platform" class="fs-6 text-secondary">Source</label>
                                <select id="platform" name="platform" class="platform form-select form-control" required="" data-parsley-group="block-1">
                                    <option value="" selected="">Select</option>
                                    @foreach($platform as $p)
                                        <option data-com="{{$p->commission}}" value="{{$p->id}}" >{{$p->name}}
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

                            <div class="col-12 col-md-4 mb-2">
                                <label for="platform" class="fs-6 text-secondary">Assign</label>
                                <select id="bd" name="bd" class="bd form-select form-control" required="" data-parsley-group="block-1">
                                    <option value="" selected="">Select</option>
                                    @foreach($bd as $b)
                                        <option value="{{$b->id}}" >{{$b->name}}
                                        
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- <div class="col-12 col-md-12  mb-2">
                                <label for="client_name" class="fs-6 text-secondary">discription</label>
                                <div id="editorjs" class="border rounded"></div>
                            </div> -->
                            <div class="col-12 text-end">
                                <button type="button" id="submit-btn" class="btn btn-primary btn-sm px-4 mt-2 float-right">Create</button>
                            </div>
                        </div>
                    </form>
                    <form id="step2" class="step2 d-none" novalidate data-parsley-validate>
                        <div class="row my-4">
                            <div class="col-6 text-center border-bottom border-dark border-2">
                                <div class="d-flex justify-content-center align-items-center mb-3">
                                    <i class="fas fa-fw fa-file text-dark mx-1"></i>
                                    <h6 class="text-dark mb-0">Project</h6>
                                </div>
                            </div>
                            <div class="col-6 text-center border-bottom border-primary">
                                <div class="d-flex justify-content-center align-items-center mb-3">
                                    <i class="fas fa-fw fa-check text-primary mx-1"></i>
                                    <h6 class="text-primary mb-0">Confirmation</h6>
                                </div>
                            </div>
                           
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6  mb-2">
                                <label for="revenue" class="fs-6 text-secondary">Revenue</label>
                                <input type="text" name="revenue"  class="revenue form-control" required="" data-parsley-group="block-2" data-parsley-type="number" disabled>
                            </div>
                            <div class="col-12 col-md-6  mb-2">
                                <label for="commission" class="fs-6 text-secondary">Commission</label>
                                <input type="text" name="commission"  class="commission form-control" required="" data-parsley-group="block-2" data-parsley-type="number" disabled>
                            </div>
                            
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal-->
    <div class="modal fade" id="userEditModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Employee</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="edit-form-employee" class="edit-form-employee" novalidate data-parsley-validate>
                    <div class="modal-body"> 
                        <div class="row">
                            
                            <div class="col-12 col-md-12  mb-2">
                                <label for="eName" class="fs-6 text-secondary">Name</label>
                                <input type="text" name="eName"  class="editName form-control" required="" data-parsley-group="block-2">
                            </div>
                            <div class="col-12 col-md-12 mb-2">
                                <label for="editPassword" class="fs-6 text-secondary">Password</label>
                                <input type="editPassword" name="editPassword" class="editPassword form-control">
                            </div>
                            <div class="col-12 col-md-12 mb-2">
                                <label for="editPhone" class="fs-6 text-secondary">Phone</label>
                                <input type="tel" name="editPhone" class="editPhone form-control" required="" data-parsley-group="block-2">
                            </div>
                            <div class="col-12 col-md-12 mb-2">
                                <label for="editDepartment" class="fs-6 text-secondary">Department</label>
                                <select id="editDepartment" name="editDepartment" class="editDepartment form-select form-control" required="" data-parsley-group="block-2">
                                <option value="">Select</option>
                                <option value="2" >Business Developer</option>
                                <option value="3">Developer</option>
                                <option value="4">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="m">
                        <button class="btn btn-secondary btn-sm" type="button">Cancel</button>
                        <button type="button" class="btn btn-primary update-user btn-sm">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- suspend modal -->
    <div class="modal fade" id="suspendUserModal" tabindex="-1" role="dialog" aria-labelledby="suspendUserModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Suspend User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-danger">User access will be blocked from all features of this system are you sure to suspend this user?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="final-suspend-user">Yes</button>
                </div>
            </div>
        </div>
    </div> 

    @include('layouts.admin_footer')
    <script>
      
    var projectTbl = '';
    $(document).ready(function(){
        toastr.options.timeOut = 1500; // 1.5s
        projectTbl = $('#project-table').dataTable({
            ajax: {
                url: "{{ url('ajax/get-project') }}",
                type: "POST",
                data: {
                    _token: "{{ @csrf_token() }}",
                    id:"{{$id}}",
                    month:$('.month-select').val(),
                    completed:0,
                    progress:0,
                    bid:0,
                    invite:0
                },
            },
            processing: true,
            columns: [
                {data: 'project_data' }
            ],
            bLengthChange: false,
            searching: true,
            sorting:false,
            "iDisplayLength": 3,
            language: { search: "", searchPlaceholder: "Search Project" }
        });   
    })

    $(document).on('change','.platform-select',function(){
       search_db();
    })
    $(document).on('change','.month-select',function(){
       search_db();
    })
    $(document).on('change','.type-select',function(){
       search_db();
    })
    $(document).on('change','.status-select',function(){
       search_db();
    })


    function search_db(){

        var month       = $('.month-select').val();
        var platform    = $('.platform-select').val();
        var type        = $('.type-select').val();
        var status    = $('.status-select').val();

        $('#project-table').DataTable().destroy();
        projectTbl = $('#project-table').dataTable({
            ajax: {
                url: "{{ url('ajax/get-project') }}",
                type: "POST",
                data: {
                    _token: "{{ @csrf_token() }}",
                    id:"{{$id}}",
                    month:month,
                    type:type,
                    status:status
                    
                },
            },
            processing: true,
            columns: [
                {data: 'project_data' }
            ],
            bLengthChange: false,
            searching: true,
            bPaginate: false,
            sorting:false,
            "pageLength": 3,
            language: { search: "", searchPlaceholder: "Search Project" }
        });  
    }

    $(document).ready(function() {
        $('#form-employee').parsley();
        
    });
 
    $(document).on('click', '#submit-btn', function(){
        $(document).find('#step1').parsley().whenValidate({
            group: 'block-1'
        }).done(function(){
            $('#submit-btn').attr('disabled',true);
            $('#submit-btn').text('Processing');
            
            var title           = $('.title').val();
            var project_link     = $('.project_link').val();
            var project_type     = $('.project_type').val();
            var client_name     = $('.client_name').val();
            var client_type     = $('.client_type').val();
            var amount 		    = $('.amount').val();
            var platform 	    = $('.platform').val();
            var bd 	            = $('.bd').val();

            $.ajax({
                url: "{{ url('add-project') }}",
                method: "POST",
                data: { 
                    _token: "{{ @csrf_token() }}", 	
                    title 	 :title,
                    project_link 	 :project_link,
                    project_type 	 :project_type,
                    client_name    :client_name,
                    client_type    :client_type,
                    amount :amount,
                    platform:platform,
                    bd:bd
                },
                success: function(data) {
                    var d = $.parseJSON( data );

                    if( d.success == 1 ) {
                        projectTbl.api().ajax.reload();
                        $('#userEditModal').modal('hide');
                        toastr.success('Updated Successfuly!');
                        $('#submit-btn').attr('disabled',false);
                        $('#submit-btn').text('Create');
                        $('#AddModal').modal('hide');
                        empty_value();
                    } else {
                        
                        
                        
                    }
                }
            });
        });

    })


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
        $('#step1').removeClass('d-none');
    }

    
    </script>
