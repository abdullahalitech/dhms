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
                                    <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#AddModal">
                                        Add Platform
                                    </a>
                                </div>
                                <div class="col-12 col-md-2 mb-2">
                                    <div class="text-end">
                                        <select id="com" name="com" class="com form-select form-control">
                                            <option value="" selected="">Select Type</option>
                                            <option value="1" >Fiver</option>
                                            <option value="2">Upwork</option>
                                            <option value="3">Direct</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table border-0" id="platform-table" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th style="width:12% !important;">Commission (%)</th>
                                            <th>Status</th>
                                            <th>Created Date</th>
                                            <th style="width:16% !important;">Actions</th>
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
                    <h5 class="modal-title" id="exampleModalLabel">Add Platform</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="form-platform" class="form-platform" novalidate data-parsley-validate>
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
                                <label for="type" class="fs-6 text-secondary">Type</label>
                                <select id="type" name="type" class="type form-select form-control" required="" data-parsley-group="block-1">
                                    <option value="" selected="">Select Type</option>
                                    <option value="1" >Fiver</option>
                                    <option value="2">Upwork</option>
                                    <option value="3">Direct</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-12 mb-2">
                                <label for="commission" class="fs-6 text-secondary">Commission (%)</label>
                                <input type="text" name="commission" class="commission form-control" data-parsley-type="number" required="" data-parsley-group="block-1">
                            </div>
                            
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary add-platform btn-sm">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal-->
    <div class="modal fade" id="platformEditModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Platform</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="edit-form-platform" class="edit-form-platform" novalidate data-parsley-validate>
                    <div class="modal-body"> 
                        <div class="row">
                            
                            <div class="col-12 col-md-12  mb-2">
                                <label for="eName" class="fs-6 text-secondary">Name</label>
                                <input type="text" name="eName"  class="editName form-control" required="" data-parsley-group="block-2">
                            </div>

                            <div class="col-12 col-md-12 mb-2">
                                <label for="editCommission" class="fs-6 text-secondary">Commission (%)</label>
                                <input type="text" name="editCommission" class="editCommission form-control" data-parsley-type="number" required="" data-parsley-group="block-1">
                            </div>
                           
                            <div class="col-12 col-md-12 mb-2">
                                <label for="editType" class="fs-6 text-secondary">Type</label>
                                <select id="editType" name="editType" class="editType form-select form-control" required="" data-parsley-group="block-1">
                                    <option value="" selected="">Select Type</option>
                                    <option value="1" >Fiver</option>
                                    <option value="2">Upwork</option>
                                    <option value="3">Direct</option>
                                    <option value="4">CV Marketing</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary update-platform btn-sm">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- suspend modal -->
    <div class="modal fade" id="suspendPlatformModal" tabindex="-1" role="dialog" aria-labelledby="suspendUserModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Suspend Platform</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-danger">Platform will be unavailable in internal system are you sure to suspend this platform?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="final-suspend-platform">Yes</button>
                </div>
            </div>
        </div>
    </div> 

    @include('layouts.admin_footer')
    <script >

   
    var platformTbl = '';
    $(document).ready(function(){
        toastr.options.timeOut = 1500; // 1.5s
        platformTbl = $('#platform-table').dataTable({
            ajax: {
                url: "{{ url('ajax/get-platform') }}",
                type: "POST",
                data: {
                    _token: "{{ @csrf_token() }}"
                },
            },
            processing: true,
            columns: [
                {data: 'name' },
                {data: 'type' },
                {data: 'commission' },
                {data: 'status' },
                {data: 'created_at' },
                {data: 'action'}
            ],
            bLengthChange: false,
            searching: true,
            bPaginate: true,
            language: { search: "", searchPlaceholder: "Search platform" }
        });

        
    })

    $(document).on('change','.com',function(){
        var search = $(this).val();
        $('#platform-table').DataTable().destroy();
        platformTbl = $('#platform-table').dataTable({
            ajax: {
                url: "{{ url('ajax/get-platform') }}",
                type: "POST",
                data: {
                    _token: "{{ @csrf_token() }}",
                    search:search
                },
            },
            processing: true,
            columns: [
                {data: 'name' },
                {data: 'type' },
                {data: 'commission' },
                {data: 'status' },
                {data: 'created_at' },
                {data: 'action'}
            ],
            bLengthChange: false,
            searching: true,
            bPaginate: false,
            language: { search: "", searchPlaceholder: "Search platform" }
        });
    })

    $(document).ready(function() {
        $('#form-platform').parsley();
        
    });
 
    $(document).on('click', '.add-platform', function(){
        $(document).find('#form-platform').parsley().whenValidate({
            group: 'block-1'
        }).done(function(){
			var name 		    = $('.name').val();
			var commission      = $('.commission').val();
			var type 		    = $('.type').val();
			
			$.ajax({
                url: "{{ url('add-platform') }}",
                method: "POST",
                data: { 
                    _token: "{{ @csrf_token() }}",	
					name 	 :name, 	
					commission :commission, 	
					type    :type
                },
                success: function(data) {
                    var d = $.parseJSON( data );

                    if( d.success == 1 ) {
						
                        platformTbl.api().ajax.reload();
                        $('#AddModal').modal('hide');
                        toastr.success('Added Successfuly!');
						
                    } else {
                       
					 
					   
                    }
                }
            });
        })
    })


    $(document).on('click', '.edit-platform', function() {

        var platform_id = $(this).attr('data-id');
        $('.update-platform').attr('data-id', platform_id);

        $.ajax({
            url: "{{ url('ajax/admin/get-platform-for-edit') }}",
            type: "POST",
            data: { _token: "{{ @csrf_token() }}", platform_id: platform_id },
            success: function(data) {
                var d = $.parseJSON( data );
                console.log( data );
                $('.editName').val( d.name );
                $('.editCommission').val(d.commission);
                $('.editType').val(d.type).change();
                $('#platformEditModal').modal('show');
            }   
        });

    });

    $(document).on('click', '.update-platform', function(){
        $(document).find('#edit-form-platform').parsley().whenValidate({
            group: 'block-2'
        }).done(function(){
            var id          = $('.update-platform').attr('data-id');
			var name 		= $('.editName').val();
			var commission  = $('.editCommission').val();;
			var type 		= $('.editType').val();
			
			$.ajax({
                url: "{{ url('update-platform') }}",
                method: "POST",
                data: { 
                    _token: "{{ @csrf_token() }}", 	
					name 	 :name, 	
					commission :commission, 	
					type    :type,
                    id:id
                },
                success: function(data) {
                    var d = $.parseJSON( data );

                    if( d.success == 1 ) {
                        platformTbl.api().ajax.reload();
                        $('#platformEditModal').modal('hide');
						toastr.success('Updated Successfuly!');
                    } else {
                       
					 
					   
                    }
                }
            });
        })
    })

    $(document).on('click', '.suspend-platform', function() {

        
        var platform_id = $(this).attr('data-id');
        $('#final-suspend-platform').attr('data-id', platform_id);
        $('#suspendPlatformModal').modal("show");

    });

    $(document).on('click', '#final-suspend-platform', function() {
        var platform_id = $(this).attr('data-id');
        $.ajax({
            url: "{{ url('ajax/admin/suspend-platform') }}",
            type: "POST",
            data: { _token: "{{ @csrf_token() }}", platform_id: platform_id},
            success: function(data) {
                platformTbl.api().ajax.reload();
                $('#suspendPlatformModal').modal("hide");
                toastr.success('Suspended Successfuly!');
            }
        });
    });

    $(document).on('click', '.resume-platform', function() {        
        var platform_id = $(this).attr('data-id');
        $.ajax({
            url: "{{ url('ajax/admin/resume-platform') }}",
            type: "POST",
            data: { _token: "{{ @csrf_token() }}", platform_id: platform_id},
            success: function(data) {
                platformTbl.api().ajax.reload();
                toastr.success('Resumed Successfuly!');
            }
        });
    });
    
    </script>
