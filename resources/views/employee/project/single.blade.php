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
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">{{$title}}</h1>
                
            </div>
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
                                <input type="text" name="title"  class="title form-control" value="{{$project->title}}" required="" data-parsley-group="block-1">
                            </div>
                            <div class="col-12 col-md-6  mb-2">
                                <label for="client_name" class="fs-6 text-secondary">Client Name</label>
                                <input type="text" name="client_name"  class="client_name form-control" value="{{$project->client_name}}" required="" data-parsley-group="block-1">
                            </div>
                            <div class="col-12 col-md-6 mb-2">
                                <label for="client_email" class="fs-6 text-secondary">Client Email</label>
                                <input type="email" name="client_email" class="client_email form-control" value="{{$project->client_email}}" required="" data-parsley-group="block-1">
                            </div>
                            <div class="col-12 col-md-6  mb-2">
                                <label for="amount" class="fs-6 text-secondary">Total Amount</label>
                                <input type="text" name="amount"  class="amount form-control" required="" value="{{$project->total_amount}}" data-parsley-group="block-1" data-parsley-type="number">
                            </div>
                            <div class="col-12 col-md-6 mb-2">
                                <label for="platform" class="fs-6 text-secondary">Type</label>
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

    <!-- Assign Modal-->
    <div class="modal fade" id="assignModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Assign User</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-12 col-md-12 mb-2">
                        <label for="assign_user" class="fs-6 text-secondary">Select the business developer</label>
                        <select id="assign_user" name="assign_user" class="assign_user form-select form-control" required="" data-parsley-group="block-3">
                        <option value="">Select</option>

                        @foreach($bd_users as $bd)
                            <option value="{{$bd->id}}" <?php if($project->owner == $bd->id){ echo 'selected'; } ?>>{{$bd->name}}</option>
                        @endforeach
                        
                        </select>
                    </div>
                </div>
                
                <div class="col-12 text-end mb-4">
                    <div class="d-flex justify-content-end align-items-center">
                        <button class="btn btn-secondary btn-sm" type="button">Cancel</button>
                        <button type="button" class="btn btn-primary update-assign-btn btn-sm mx-2">Update</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

     <!-- Share Modal-->
     <div class="modal fade" id="shareModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Shared User</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-12 col-md-12 mb-2">
                        <label for="shared_user" class="fs-6 text-secondary">Project will be manage by</label>
                        <select id="shared_user" name="shared_user" class="shared_user form-select form-control" required="" data-parsley-group="block-3">
                        <option value="">Select</option>

                        @foreach($s_users as $s)
                            <option value="{{$s->id}}" <?php if($project->shared_user == $s->id){ echo 'selected'; } ?>>{{$s->name}}</option>
                        @endforeach
                        
                        </select>
                    </div>
                </div>
                
                <div class="col-12 text-end mb-4">
                    <div class="d-flex justify-content-end align-items-center">
                        <button class="btn btn-secondary btn-sm" type="button">Cancel</button>
                        <button type="button" class="btn btn-primary update-shared-btn btn-sm mx-2">Update</button>
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
   
    /**
     * To initialize the Editor, create a new instance with configuration object
     * @see docs/installation.md for mode details
     */
    var editor;
    $('.edit').on('click',function(){
        editor = new EditorJS({
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
        setTimeout(function() { 
            var editor_blocks = <?php echo json_encode($desc_array); ?>;
            console.log(editor_blocks);
            var text = "";
            $.each( editor_blocks, function( key, value ) {
                
                editor.blocks.insert(value.type, value.data);

            });
        }, 1000);
        
        //editor.blocks.insert(editor_blocks);
      },
      onChange: function(api, event) {
        console.log('something changed', event);
      }
    });
    })
    

   
    

    
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
                url: "{{ url('user/edit-project') }}",
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
            
            
        })
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
