@include('layouts.admin_header')

        <!-- Begin Page Content -->
        <div class="container-fluid">
            <!-- Content Row -->
            <div class="row">

                <div class="col-12">
                    <div class="card shadow mb-4">
                        
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12 col-md-10">
                                    <a href="login.html" class="btn btn-primary" data-toggle="modal" data-target="#AddModal">
                                        Add Employee
                                    </a>
                                </div>
                                <div class="col-12 col-md-2 mb-2">
                                    <div class="text-end">
                                        <select id="dep" name="dep" class="dep form-select form-control">
                                            <option value="" selected="">Select Department</option>
                                            <option value="2" >Business Development</option>
                                            <option value="3">Developer</option>
                                            <option value="4">Other</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table border-0" id="employee-table" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Email</th>
                                            <th>Name</th>
                                            <th>Department</th>
                                            <th>Status</th>
                                            <th>Created Date</th>
                                            <th style="width:20% !important;">Actions</th>
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
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Employee</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="form-employee" class="form-employee" novalidate data-parsley-validate>
                    <div class="modal-body"> 
                        <div class="row">
                            <div class="col-12">
                                <p class="error text-danger text-sm"></p>
                            </div> 
                            
                            <div class="col-12 col-md-12  mb-2">
                                <label for="first_name" class="fs-6 text-secondary">Name</label>
                                <input type="text" name="first_name"  class="name form-control" required="" data-parsley-group="block-1">
                            </div>
                            <div class="col-12 col-md-12 mb-2">
                                <label for="email" class="fs-6 text-secondary">Email Address</label>
                                <input type="email" name="email" class="email form-control" required="" data-parsley-group="block-1">
                            </div>
                            <div class="col-12 col-md-12 mb-2">
                                <label for="password" class="fs-6 text-secondary">Password</label>
                                <input type="password" name="password" class="password form-control" required="" data-parsley-group="block-1">
                            </div>
                            <div class="col-12 col-md-12 mb-2">
                                <label for="phone" class="fs-6 text-secondary">Phone</label>
                                <input type="tel" name="phone" class="phone form-control" required="" data-parsley-group="block-1">
                            </div>
                            <div class="col-12 col-md-12 mb-2">
                                <label for="department" class="fs-6 text-secondary">Department</label>
                                <select id="department" name="department" class="department form-select form-control" required="" data-parsley-group="block-1">
                                <option value="" selected="">Select</option>
                                <option value="2" >Business Developer</option>
                                <option value="3">Developer</option>
                                <option value="4">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary add-employee btn-sm">Add</button>
                    </div>
                </form>
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
                    <div class="modal-footer">
                        <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
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
    <script >

   
    var employeeTbl = '';
    $(document).ready(function(){
        toastr.options.timeOut = 1500; // 1.5s
        employeeTbl = $('#employee-table').dataTable({
            ajax: {
                url: "{{ url('ajax/get-employee') }}",
                type: "POST",
                data: {
                    _token: "{{ @csrf_token() }}"
                },
            },
            processing: true,
            columns: [
                {data: 'email' },
                {data: 'name' },
                {data: 'department' },
                {data: 'status' },
                {data: 'created_at' },
                {data: 'action'}
            ],
            bLengthChange: false,
            searching: true,
            bPaginate: false,
            language: { search: "", searchPlaceholder: "Search Employee" }
        });

        
    })

    $(document).on('change','.dep',function(){
        var search = $(this).val();
        $('#employee-table').DataTable().destroy();
        employeeTbl = $('#employee-table').dataTable({
            ajax: {
                url: "{{ url('ajax/get-employee') }}",
                type: "POST",
                data: {
                    _token: "{{ @csrf_token() }}",
                    search:search
                },
            },
            processing: true,
            columns: [
                {data: 'email' },
                {data: 'name' },
                {data: 'department' },
                {data: 'status' },
                {data: 'created_at' },
                {data: 'action'}
            ],
            bLengthChange: false,
            searching: true,
            bPaginate: false,
            language: { search: "", searchPlaceholder: "Search Employee" }
        });
    })

    $(document).ready(function() {
        $('#form-employee').parsley();
        
    });
 
    $(document).on('click', '.add-employee', function(){
        $(document).find('#form-employee').parsley().whenValidate({
            group: 'block-1'
        }).done(function(){
            			
			var email 		= $('.email').val();
			var name 		= $('.name').val();
			var password    = $('.password').val();
			var phone 		= $('.phone').val();
			var department 		= $('.department').val();
			
			$.ajax({
                url: "{{ url('add-employee') }}",
                method: "POST",
                data: { 
                    _token: "{{ @csrf_token() }}", 
					email 	 :email, 	
					name 	 :name, 	
					password :password, 	
					phone    :phone,
					department :department
                },
                success: function(data) {
                    var d = $.parseJSON( data );

                    if( d.success == 1 ) {
						
                        employeeTbl.api().ajax.reload();
                        $('#AddModal').modal('hide');
                        toastr.success('Added Successfuly!');
						
                    } else {
                       
					 
					   
                    }
                }
            });
        })
    })


    $(document).on('click', '.edit-user', function() {

        var user_id = $(this).attr('data-id');
        $('.update-user').attr('data-id', user_id);

        $.ajax({
            url: "{{ url('ajax/admin/get-employee-for-edit') }}",
            type: "POST",
            data: { _token: "{{ @csrf_token() }}", user_id: user_id },
            success: function(data) {
                var d = $.parseJSON( data );
                console.log( data );
                $('.editName').val( d.name );
                $('.editPhone').val(d.phone);
                $('.editPassword').val('');
                $('.editDepartment').val(d.roles).change();
                $('#userEditModal').modal('show');
            }   
        });

    });

    $(document).on('click', '.update-user', function(){
        $(document).find('#edit-form-employee').parsley().whenValidate({
            group: 'block-2'
        }).done(function(){
            var id          = $('.update-user').attr('data-id');
			var name 		= $('.editName').val();
			var password    = $('.editPassword').val();
			var phone 		= $('.editPhone').val();
			var department 		= $('.editDepartment').val();
			
			$.ajax({
                url: "{{ url('update-employee') }}",
                method: "POST",
                data: { 
                    _token: "{{ @csrf_token() }}", 	
					name 	 :name, 	
					password :password, 	
					phone    :phone,
					department :department,
                    id:id
                },
                success: function(data) {
                    var d = $.parseJSON( data );

                    if( d.success == 1 ) {
                        employeeTbl.api().ajax.reload();
                        $('#userEditModal').modal('hide');
						toastr.success('Updated Successfuly!');
                    } else {
                       
					 
					   
                    }
                }
            });
        })
    })

    $(document).on('click', '.suspend-user', function() {

        
        var user_id = $(this).attr('data-id');
        $('#final-suspend-user').attr('data-id', user_id);
        $('#suspendUserModal').modal("show");

    });

    $(document).on('click', '#final-suspend-user', function() {
        var user_id = $(this).attr('data-id');
        $.ajax({
            url: "{{ url('ajax/admin/suspend-user') }}",
            type: "POST",
            data: { _token: "{{ @csrf_token() }}", user_id: user_id},
            success: function(data) {
                employeeTbl.api().ajax.reload();
                $('#suspendUserModal').modal("hide");
                toastr.success('Suspended Successfuly!');
            }
        });
    });

    $(document).on('click', '.resume-user', function() {        
        var user_id = $(this).attr('data-id');
        $.ajax({
            url: "{{ url('ajax/admin/resume-user') }}",
            type: "POST",
            data: { _token: "{{ @csrf_token() }}", user_id: user_id},
            success: function(data) {
                employeeTbl.api().ajax.reload();
                toastr.success('Resumed Successfuly!');
            }
        });
    });
    
    </script>
