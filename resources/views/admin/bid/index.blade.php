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
                                        Add Commission
                                    </a>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table border-0" id="bid-table" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Range</th>
                                            <th>Commission</th>
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
                    <h5 class="modal-title" id="exampleModalLabel">Add Bid</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="form-bidinvite" class="form-bidinvite" novalidate data-parsley-validate>
                    <div class="modal-body"> 
                        <div class="row">
                            <div class="col-12">
                                <p class="error-add text-danger text-sm"></p>
                            </div> 
                            
                            <div class="col-12 col-md-12  mb-2">
                                <label for="from" class="fs-6 text-secondary">From</label>
                                <input type="text" name="from"  class="from form-control" required="" data-parsley-group="block-1">
                            </div>
                            
                            <div class="col-12 col-md-12  mb-2">
                                <label for="to" class="fs-6 text-secondary">To</label>
                                <input type="text" name="to"  class="to form-control" required="" data-parsley-group="block-1">
                            </div>
                            
                            <div class="col-12 col-md-12 mb-2">
                                <label for="commission" class="fs-6 text-secondary">Commission (%)</label>
                                <input type="text" name="commission" class="commission form-control" data-parsley-type="number" required="" data-parsley-group="block-1">
                            </div>
                            
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary add-bid btn-sm">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal-->
    <div class="modal fade" id="bidEditModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Bid</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="edit-form-bidinvite" class="edit-form-bidinvite" novalidate data-parsley-validate>
                    <div class="modal-body"> 
                        <div class="row">
                            
                            <div class="col-12">
                                <p class="error-edit text-danger text-sm"></p>
                            </div> 
                            
                            <div class="col-12 col-md-12  mb-2">
                                <label for="efrom" class="fs-6 text-secondary">From</label>
                                <input type="text" name="efrom"  class="efrom form-control" required="" data-parsley-type="number"  data-parsley-group="block-2">
                            </div>
                            
                            <div class="col-12 col-md-12  mb-2">
                                <label for="eto" class="fs-6 text-secondary">To</label>
                                <input type="text" name="eto"  class="eto form-control" required="" data-parsley-type="number"  data-parsley-group="block-2">
                            </div>
                            
                            <div class="col-12 col-md-12 mb-2">
                                <label for="ecommission" class="fs-6 text-secondary">Commission (%)</label>
                                <input type="text" name="ecommission" class="ecommission form-control" data-parsley-type="number" required="" data-parsley-group="block-2">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary btn-sm" type="button" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary update-bid btn-sm">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- suspend modal -->
    <div class="modal fade" id="suspendBidModal" tabindex="-1" role="dialog" aria-labelledby="suspendUserModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Suspend Bid</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-danger">Bid commission will be unavailable in internal system are you sure to suspend this commission?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="final-suspend-bidinvite">Yes</button>
                </div>
            </div>
        </div>
    </div> 

    @include('layouts.admin_footer')
    <script >

   
    var bidTbl = '';
    $(document).ready(function(){
        toastr.options.timeOut = 1500; // 1.5s
        bidTbl = $('#bid-table').dataTable({
            ajax: {
                url: "{{ url('ajax/get-bid-invite') }}",
                type: "POST",
                data: {
                    _token: "{{ @csrf_token() }}",
                    type:1
                },
            },
            processing: true,
            columns: [
                {data: 'range' },
                {data: 'commission' },
                {data: 'status' },
                {data: 'created_at' },
                {data: 'action'}
            ],
            bLengthChange: false,
            searching: true,
            bPaginate: true,
            ordering:false,
            language: { search: "", searchPlaceholder: "Search bid" }
        });

        
    })

    $(document).ready(function() {
        $('#form-bid').parsley();
        
    });
 
    $(document).on('click', '.add-bid', function(){
        $('.error-add').text('');
        $(document).find('#form-bidinvite').parsley().whenValidate({
            group: 'block-1'
        }).done(function(){
            $('#add-bid').attr('disabled',true);
            $('#add-bid').text('Adding');
			var from 		    = $('.from').val();
			var to 		        = $('.to').val();
			var commission      = $('.commission').val();
			var type 		    = 1;
			
			$.ajax({
                url: "{{ url('add-bidinvite') }}",
                method: "POST",
                data: { 
                    _token: "{{ @csrf_token() }}",	
					from 	   :from, 	
					to 	       :to, 	
					commission :commission, 	
					type       :type
                },
                success: function(data) {
                    var d = $.parseJSON( data );

                    if( d.success == 1 ) {
						$('#add-bid').attr('disabled',false);
                        bidTbl.api().ajax.reload();
                        $('#AddModal').modal('hide');
                        toastr.success('Added Successfuly!');
						
                    } else {
                        $('#add-bid').attr('disabled',false);
                        $('#add-bid').text('Add');
                        $('.error-add').text(d.msg);
                    }
                }
            });
        })
    })


    $(document).on('click', '.edit-bidinvite', function() {

        var bid_id = $(this).attr('data-id');
        $('.update-bid').attr('data-id', bid_id);

        $.ajax({
            url: "{{ url('ajax/admin/get-bidinvite-for-edit') }}",
            type: "POST",
            data: { _token: "{{ @csrf_token() }}", bid_id: bid_id },
            success: function(data) {
                var d = $.parseJSON( data );
                console.log( data );
                $('.efrom').val( d.com_from );
                $('.eto').val(d.com_to);
                $('.ecommission').val(d.commission);
                $('#bidEditModal').modal('show');
            }   
        });

    });

    $(document).on('click', '.update-bid', function(){
        $('.error-edit').text('');
        $(document).find('#edit-form-bidinvite').parsley().whenValidate({
            group: 'block-2'
        }).done(function(){
            $('#update-bid').attr('disabled',true);
            $('#update-bid').text('Updating');
            var id          = $('.update-bid').attr('data-id');
			var from 		= $('.efrom').val();
			var to 		    = $('.eto').val();
			var commission  = $('.ecommission').val();
			
			$.ajax({
                url: "{{ url('update-bidinvite') }}",
                method: "POST",
                data: { 
                    _token: "{{ @csrf_token() }}", 	
					from 	:from, 	
					to 	    :to, 	
					commission :commission,
                    id:id,
                    type:1
                },
                success: function(data) {
                    var d = $.parseJSON( data );

                    if( d.success == 1 ) {
                        bidTbl.api().ajax.reload();
                        $('#bidEditModal').modal('hide');
						toastr.success('Updated Successfuly!');
                    } else {
                        $('#update-bid').attr('disabled',false);
                        $('#update-bid').text('Update');
                        $('.error-edit').text(d.msg);
                    }
                }
            });
        })
    })

    $(document).on('click', '.suspend-bidinvite', function() {

        
        var bid_id = $(this).attr('data-id');
        $('#final-suspend-bidinvite').attr('data-id', bid_id);
        $('#suspendBidModal').modal("show");

    });

    $(document).on('click', '#final-suspend-bidinvite', function() {
        var bid_id = $(this).attr('data-id');
        $.ajax({
            url: "{{ url('ajax/admin/suspend-bidinvite') }}",
            type: "POST",
            data: { _token: "{{ @csrf_token() }}", bidinvite_id: bid_id},
            success: function(data) {
                bidTbl.api().ajax.reload();
                $('#suspendBidModal').modal("hide");
                toastr.success('Suspended Successfuly!');
            }
        });
    });

    $(document).on('click', '.resume-bidinvite', function() {        
        var bid_id = $(this).attr('data-id');
        $.ajax({
            url: "{{ url('ajax/admin/resume-bidinvite') }}",
            type: "POST",
            data: { _token: "{{ @csrf_token() }}", bidinvite_id: bid_id},
            success: function(data) {
                bidTbl.api().ajax.reload();
                toastr.success('Resumed Successfuly!');
            }
        });
    });
    
    </script>
