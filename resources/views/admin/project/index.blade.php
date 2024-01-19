@include('layouts.admin_header')
<style>
.codex-editor.codex-editor--narrow::-webkit-scrollbar {
    display: none;
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
                                <div class="col-12 col-md-10">
                                    <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#AddModal">
                                        Add Project
                                    </a>
                                </div>
                                <div class="col-12 col-md-2 mb-2">
                                    <div class="text-end">
                                        <select id="platform-select" name="platform-select" class="platform-select form-select form-control">
                                            <option value="" selected="">Select Platform</option>
                                            <option value="1" >Fiver</option>
                                            <option value="2">Upwork</option>
                                            <option value="3">Direct</option>
                                            <option value="4">CV Marketing</option>
                                        </select>
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
                        <div class="row my-4">
                            <div class="col-6 text-center border-bottom border-primary border-2">
                                <div class="d-flex justify-content-center align-items-center mb-3">
                                    <i class="fas fa-fw fa-file text-primary mx-1"></i>
                                    <h6 class="text-primary mb-0">Project</h6>
                                </div>
                            </div>
                            <div class="col-6 text-center border-bottom border-dark">
                                <div class="d-flex justify-content-center align-items-center mb-3">
                                    <i class="fas fa-fw fa-check text-dark mx-1"></i>
                                    <h6 class="text-dark mb-0">Confirmation</h6>
                                </div>
                            </div>
                            
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <p class="error text-danger text-sm"></p>
                            </div> 
                            
                            <div class="col-12 col-md-6  mb-2">
                                <label for="title" class="fs-6 text-secondary">Title</label>
                                <input type="text" name="title"  class="title form-control" required="" data-parsley-group="block-1">
                            </div>
                            <div class="col-12 col-md-6  mb-2">
                                <label for="client_name" class="fs-6 text-secondary">Client Name</label>
                                <input type="text" name="client_name"  class="client_name form-control" required="" data-parsley-group="block-1">
                            </div>
                            <div class="col-12 col-md-6 mb-2">
                                <label for="client_email" class="fs-6 text-secondary">Client Email</label>
                                <input type="email" name="client_email" class="client_email form-control" required="" data-parsley-group="block-1">
                            </div>
                            <div class="col-12 col-md-6  mb-2">
                                <label for="amount" class="fs-6 text-secondary">Total Amount</label>
                                <input type="text" name="amount"  class="amount form-control" required="" data-parsley-group="block-1" data-parsley-type="number">
                            </div>
                            <div class="col-12 col-md-6 mb-2">
                                <label for="platform" class="fs-6 text-secondary">Type</label>
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

                            <div class="col-12 col-md-12  mb-2">
                                <label for="client_name" class="fs-6 text-secondary">discription</label>
                                <div id="editorjs" class="border rounded"></div>
                            </div>
                            <div class="col-12 text-end">
                                <button type="button" id="step1-btn" class="btn btn-primary btn-sm px-4 mt-2 float-right">Next</button>
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
                            <div class="col-12 text-end">
                                <div class="d-flex justify-content-end align-items-center">
                                    <button class="btn btn-secondary btn-sm px-4 mx-2" id="step2-back-btn" type="button">Back</button>
                                    <button type="button" id="submit-btn" class="btn btn-primary btn-sm px-4">Submit</button>
                                </div>
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
        $(document).ready(function() {
            // $('#platform').select2({
            //     dropdownParent: $('#AddModal')
            // });
        });
    /**
     * To initialize the Editor, create a new instance with configuration object
     * @see docs/installation.md for mode details
     */
    var editor = new EditorJS({
      /**
       * Enable/Disable the read only mode
       */
      readOnly: false,

      /**
       * Wrapper of Editor
       */
      holder: 'editorjs',

      /**
       * Common Inline Toolbar settings
       * - if true (or not specified), the order from 'tool' property will be used
       * - if an array of tool names, this order will be used
       */
      // inlineToolbar: ['link', 'marker', 'bold', 'italic'],
      // inlineToolbar: true,

      /**
       * Tools list
       */
      tools: {
        /**
         * Each Tool is a Plugin. Pass them via 'class' option with necessary settings {@link docs/tools.md}
         */
        header: {
          class: Header,
          inlineToolbar: ['marker', 'link'],
          config: {
            placeholder: 'Header'
          },
          shortcut: 'CMD+SHIFT+H'
        },

        /**
         * Or pass class directly without any configuration
         */
        image: SimpleImage,

        list: {
          class: List,
          inlineToolbar: true,
          shortcut: 'CMD+SHIFT+L'
        },

        checklist: {
          class: Checklist,
          inlineToolbar: true,
        },

        quote: {
          class: Quote,
          inlineToolbar: true,
          config: {
            quotePlaceholder: 'Enter a quote',
            captionPlaceholder: 'Quote\'s author',
          },
          shortcut: 'CMD+SHIFT+O'
        },

        warning: Warning,

        marker: {
          class:  Marker,
          shortcut: 'CMD+SHIFT+M'
        },

        code: {
          class:  CodeTool,
          shortcut: 'CMD+SHIFT+C'
        },

        delimiter: Delimiter,

        inlineCode: {
          class: InlineCode,
          shortcut: 'CMD+SHIFT+C'
        },

        linkTool: LinkTool,

        embed: Embed,

        table: {
          class: Table,
          inlineToolbar: true,
          shortcut: 'CMD+ALT+T'
        },

      },

      /**
       * This Tool will be used as default
       */
      // defaultBlock: 'paragraph',

      /**
       * Initial Editor data
       */
      data: {
        blocks: [
          
        ]
      },
      onReady: function(){
        //saveButton.click();
      },
      onChange: function(api, event) {
        console.log('something changed', event);
      }
    });

    // /**
    //  * Saving button
    //  */
    // const saveButton = document.getElementById('saveButton');

    // /**
    //  * Toggle read-only button
    //  */
    // const toggleReadOnlyButton = document.getElementById('toggleReadOnlyButton');
    // const readOnlyIndicator = document.getElementById('readonly-state');

    // /**
    //  * Saving example
    //  */
    // saveButton.addEventListener('click', function () {
    //   editor.save()
    //     .then((savedData) => {
    //       cPreview.show(savedData, document.getElementById("output"));
    //     })
    //     .catch((error) => {
    //       console.error('Saving error', error);
    //     });
    // });

    // /**
    //  * Toggle read-only example
    //  */
    // toggleReadOnlyButton.addEventListener('click', async () => {
    //   const readOnlyState = await editor.readOnly.toggle();

    //   readOnlyIndicator.textContent = readOnlyState ? 'On' : 'Off';
    // });
        
   
    var projectTbl = '';
    $(document).ready(function(){
        toastr.options.timeOut = 1500; // 1.5s
        projectTbl = $('#project-table').dataTable({
            ajax: {
                url: "{{ url('ajax/get-project') }}",
                type: "POST",
                data: {
                    _token: "{{ @csrf_token() }}"
                },
            },
            processing: true,
            columns: [
                {data: 'project_data' }
            ],
            bLengthChange: false,
            searching: true,
            bPaginate: false,
            language: { search: "", searchPlaceholder: "Search Project" }
        });   
    })

    $(document).on('change','.platform-select',function(){
        var search = $(this).val();
        $('#project-table').DataTable().destroy();
        projectTbl = $('#project-table').dataTable({
            ajax: {
                url: "{{ url('ajax/get-project') }}",
                type: "POST",
                data: {
                    _token: "{{ @csrf_token() }}",
                    search:search
                },
            },
            processing: true,
            columns: [
                {data: 'project_data' }
            ],
            bLengthChange: false,
            searching: true,
            bPaginate: false,
            language: { search: "", searchPlaceholder: "Search Project" }
        });  
    })

    $(document).ready(function() {
        $('#form-employee').parsley();
        
    });
 
    $(document).on('click', '#step1-btn', function(){
        $(document).find('#step1').parsley().whenValidate({
            group: 'block-1'
        }).done(function(){

            editor.save()
            .then((savedData) => {
                if(savedData.blocks.length == 0){
                    toastr.error('Description Required');
                }else{
                    var com     = $('#platform').find(':selected').attr('data-com');
                    var amount  = $('.amount').val();
                    console.log(com, amount);
                    var user_com = amount*(com/100);
                    var revenue  = amount - user_com; 

                    $('.revenue').attr('disabled', false);
                    $('.commission').attr('disabled', false);

                    $('.revenue').val(revenue);
                    $('.commission').val(user_com);

                    $('.revenue').attr('disabled', true);
                    $('.commission').attr('disabled', true);

                    $('#step1').addClass('d-none');
                    $('#step2').removeClass('d-none');
                }
            })
            
			
        })
    })


    $(document).on('click', '#step2-back-btn', function(){
        $('#step2').addClass('d-none');
        $('#step1').removeClass('d-none');
    })



    $(document).on('click', '#submit-btn', function(){
        
        $('#submit-btn').attr('disabled',true);
        $('#submit-btn').text('Processing');
        
        var title           = $('.title').val();
        var client_email 	= $('.client_email').val();
        var client_name     = $('.client_name').val();
        var amount 		    = $('.amount').val();
        var platform 	    = $('.platform').val();

        editor.save()
        .then((savedData) => {
            
            var description         = savedData.blocks;
            var description_html    = convertDataToHtml(savedData.blocks);      
            $.ajax({
                url: "{{ url('add-project') }}",
                method: "POST",
                data: { 
                    _token: "{{ @csrf_token() }}", 	
                    title 	 :title, 	
                    client_email :client_email, 	
                    client_name    :client_name,
                    amount :amount,
                    platform:platform,
                    description:description,
                    description_html:description_html,
                },
                success: function(data) {
                    var d = $.parseJSON( data );

                    if( d.success == 1 ) {
                        projectTbl.api().ajax.reload();
                        $('#userEditModal').modal('hide');
                        toastr.success('Updated Successfuly!');
                        $('#submit-btn').attr('disabled',false);
                        $('#submit-btn').text('Submit');
                        $('#AddModal').modal('hide');
                        empty_value();
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
                        projectTbl.api().ajax.reload();
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
                projectTbl.api().ajax.reload();
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
                projectTbl.api().ajax.reload();
                toastr.success('Resumed Successfuly!');
            }
        });
    });

    function convertDataToHtml(blocks) {
      var convertedHtml = "";
      blocks.map(block => {
        
        switch (block.type) {
          case "header":
            convertedHtml += `<h${block.data.level}>${block.data.text}</h${block.data.level}>`;
            break;
          case "embded":
            convertedHtml += `<div><iframe width="560" height="315" src="${block.data.embed}" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe></div>`;
            break;
          case "paragraph":
            convertedHtml += `<p>${block.data.text}</p>`;
            break;
          case "delimiter":
            convertedHtml += "<hr />";
            break;
          case "image":
            convertedHtml += `<img class="img-fluid" src="${block.data.file.url}" title="${block.data.caption}" /><br /><em>${block.data.caption}</em>`;
            break;
          case "list":
            convertedHtml += "<ul>";
            block.data.items.forEach(function(li) {
              convertedHtml += `<li>${li}</li>`;
            });
            convertedHtml += "</ul>";
            break;
          default:
            console.log("Unknown block type", block.type);
            break;
        }
      });
      return convertedHtml;
    }

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
