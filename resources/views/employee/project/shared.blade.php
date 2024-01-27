@include('layouts.employee_header')
<style>
.codex-editor.codex-editor--narrow::-webkit-scrollbar {
    display: none;
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
                                <div class="col-12 col-md-10">
                                    
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

           
    
    @include('layouts.employee_footer')
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
                url: "{{ url('user/ajax/get-project-shared') }}",
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
                url: "{{ url('user/ajax/get-project-shared') }}",
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

   
    
    </script>
