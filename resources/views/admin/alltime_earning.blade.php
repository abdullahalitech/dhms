@include('layouts.admin_header')

        <!-- Begin Page Content -->
        <div class="container-fluid">
            <!-- Content Row -->
            <div class="row">

                <div class="col-12">
                    <div class="card shadow mb-4">
                        
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <p>Total earning of all time is: <span class="font-weight-bold">{{$amount_total}}</span></p>
                                </div>    
                            </div>
                            <div class="table-responsive">
                                <table class="table border-0" id="employee-table" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Fiver</th>
                                            <th>Upwork</th>
                                            <th>Direct</th>
                                            <th>Jobs</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    
                                    <tbody>
                                        @foreach($data_array as $key=>$value)
                                            @foreach($value as $k=>$v)
                                                <tr>
                                                    <td>{{$k}}</td>
                                                    <td>{{$v['Fiver']}}</td>
                                                    <td>{{$v['Upwork']}}</td>
                                                    <td>{{$v['Direct']}}</td>
                                                    <td>{{$v['Jobs']}}</td>
                                                    <td>{{$v['total']}}</td>
                                                </tr>
                                            @endforeach
                                        @endforeach
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

    @include('layouts.admin_footer')
   
