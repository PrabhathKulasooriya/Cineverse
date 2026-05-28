@include('includes/header_start')

<link href="{{ URL::asset('assets/plugins/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::asset('assets/plugins/datatables/buttons.bootstrap4.min.css')}}" rel="stylesheet" type="text/css"/>
<!-- Responsive datatable examples -->
<link href="{{ URL::asset('assets/plugins/datatables/responsive.bootstrap4.min.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::asset('assets/plugins/sweet-alert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css">

<!-- Plugins css -->
<link href="{{ URL::asset('assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css')}}" rel="stylesheet">
<link href="{{ URL::asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}" rel="stylesheet">
<link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::asset('assets/plugins/bootstrap-touchspin/css/jquery.bootstrap-touchspin.min.css')}}" rel="stylesheet"/>
<link href="{{ URL::asset('assets/css/custom_checkbox.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::asset('assets/css/jquery.notify.css')}}" rel="stylesheet" type="text/css">
<link href="{{ URL::asset('assets/css/mdb.css')}}" rel="stylesheet" type="text/css">

<meta name="csrf-token" content="{{ csrf_token() }}"/>


@include('includes/header_end')

<!-- Page title -->
<ul class="list-inline menu-left mb-0">
    <li class="list-inline-item">
        <button type="button" class="button-menu-mobile open-left waves-effect">
            <i class="ion-navicon"></i>
        </button>
    </li>
    <li class="hide-phone list-inline-item app-search">
        <h3 class="page-title">{{ $title }}</h3>
    </li>
</ul>

<div class="clearfix"></div>
</nav>

</div>
<!-- Top Bar End -->

<!-- ==================
     PAGE CONTENT START
     ================== -->

<div class="page-content-wrapper">
    <div class="container-fluid">
        <div class="col-lg-12">
            <div class="card m-b-20">
                <div class="card-body">

                    <div class="row">

                        <div class="col-lg-8">
                        </div>

                        <div class="col-lg-4">
                            <button type="button" class="btn btn-primary float-right"
                                    data-toggle="modal"  data-target="#addShowtimeModal" >
                                Add Showtime
                            </button>
                        </div>

                    </div>



                <br/>




             <!--Data Table Start-->

                    <div class="table-rep-plugin">
                        <div class="table-responsive b-0" data-pattern="priority-columns">


                            <table id="datatable"   class="table table-striped table-bordered"
                                   cellspacing="0"
                                   width="100%">

                                <thead>
                                    <tr>
                                        <th>SHOWTIME ID</th>
                                        <th>TIME</th>
                                        <th>STATUS</th>
                                        <th>OPTIONS</th>
                                    </tr>
                                </thead>

                                <tbody>

                               
                                @if(isset($showtimes))
                                    @if(count($showtimes)>0)
                                        @foreach($showtimes as $showtime)

                                            <tr>
                                                <td>0{{$showtime->idshowtimes}}</td>
                                                <td>{{\Carbon\Carbon::parse($showtime->time)->format('h:i A')}}</td>

                                                 <!--Status Start-->
                                                 @if($showtime->status == 1)
                                                 <td>
                                                     <p>
                                                         <input type="checkbox"
                                                                onchange="adMethod('{{ $showtime->idshowtimes }}','showtimes')"
                                                                id="{{"c".$showtime->idshowtimes}}" checked
                                                                switch="none"/>
                                                         <label for="{{"c".$showtime->idshowtimes}}"
                                                                data-on-label="On"
                                                                data-off-label="Off"></label>
                                                     </p>
                                                 </td>
                                                @else
                                                 <td>
                                                     <p>
                                                         <input type="checkbox"
                                                                onchange="adMethod('{{ $showtime->idshowtimes }}','showtimes')"
                                                                id="{{"c".$showtime->idshowtimes}}"
                                                                switch="none"/>
                                                         <label for="{{"c".$showtime->idshowtimes}}"
                                                                data-on-label="On"
                                                                data-off-label="Off"></label>
                                                     </p>
                                                 </td>
                                                @endif
                                                   <!--Status End-->


                                                <td style="display:flex; flex-direction:row; gap: 5px;">

                                                    <button class="btn btn-sm btn-warning  edit-Showtime-btn" 
                                                            data-toggle="modal" 

                                                            data-id="{{$showtime->idshowtimes}}"
                                                            data-time="{{$showtime->time}}"

                                                            id="edit-showtime-btn"     
                                                            data-target="#editShowtimeModal">
                                                          <i class="fa fa-edit"></i>
                                                        </button>

                                                    <form action="{{route('destroyShowtime', ['Showtime_id'=>$showtime->idshowtimes])}}" method="post">
                                                        @csrf
                                                        @method('DELETE')

                                                        <button type='submit' class="btn btn-sm  btn-danger"><i class="fa fa-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>

                                        @endforeach
                                    @endif
                                @endif

                                </tbody>

                            </table>

                        </div>
                    </div>


              <!--Data Table End-->





                </div>
            </div>
        </div>
    </div> <!-- container -->

</div> <!-- Page content Wrapper -->

</div> <!-- content -->








<!-- Add Showtime Modal Start-->
<div class="modal fade" id="addShowtimeModal" tabindex="-1"
     role="dialog"
     aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog">

        <div class="modal-content">

            <form action="{{ route('saveShowtime') }}" method="post" enctype="multipart/form-data" id="saveShowtimeForm">
                @csrf

            <div class="modal-header">
                <h5 class="modal-title mt-0">Add Showtime</h5>
                <button type="button" class="close" data-dismiss="modal"
                        aria-hidden="true">×
                </button>
            </div>
            
            <div class="modal-body">

                <div class="form-group">
                    <label>Showtime ID <span style="color:red">*</span></label>
                    <select class="form-control" name="showtimeId" id="showtimeId" required>
                        <option value="" disabled selected>-- Select Showtime ID --</option>
                        <option value="1">01</option>
                        <option value="2">02</option>
                        <option value="3">03</option>
                        <option value="4">04</option>
                        <option value="5">05</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Time <span style="color:red">*</span></label>
                    <input type="time" class="form-control" name="time" id="time" required placeholder="Showtime" />
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary float-right" id="updateShowtime">Add Showtime</button>
                </div>

                
            </div>
            </form>
        </div>

    </div>
</div>
<!-- Add Showtime Modal End-->




<!-- Edit Showtime Modal -->
<div class="modal fade" id="editShowtimeModal" tabindex="-1" role="dialog" aria-labelledby="editShowtimeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('updateShowtime') }}" method="post" enctype="multipart/form-data" id="editShowtimeForm">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title mt-0">Edit Showtime</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                
                <div class="modal-body">

                    <div class="form-group">
                        <label>Showtime ID <span style="color:red">*</span></label>
                        <input type="hidden" id="hiddenShowtimeId" name="hiddenShowtimeId">
                        <span class="form-control" name="showId" id="showId">0</span>
                    </div>

                    <div class="form-group">
                        <label>Time <span style="color:red">*</span></label>
                        <input type="time" class="form-control" name="time" id="time" required placeholder="Showtime" />
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary float-right" id="updateShowtime">Update Showtime</button>
                    </div>

                    
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Edit Showtime Modal End-->




@include('includes/footer_start')

<!-- Plugins js -->
<script src="{{ URL::asset('assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js')}}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js')}}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js')}}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/plugins/bootstrap-touchspin/js/jquery.bootstrap-touchspin.min.js')}}"
        type="text/javascript"></script>

<!-- Plugins Init js -->
<script src="{{ URL::asset('assets/pages/form-advanced.js')}}"></script>

<!-- Required datatable js -->
<script src="{{ URL::asset('assets/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/dataTables.bootstrap4.min.js')}}"></script>
<!-- Buttons examples -->
<script src="{{ URL::asset('assets/plugins/datatables/dataTables.buttons.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/buttons.bootstrap4.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/jszip.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/pdfmake.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/vfs_fonts.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/buttons.html5.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/buttons.print.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/buttons.colVis.min.js')}}"></script>
<!-- Responsive examples -->
<script src="{{ URL::asset('assets/plugins/datatables/dataTables.responsive.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/responsive.bootstrap4.min.js')}}"></script>

<script src="{{ URL::asset('assets/plugins/sweet-alert2/sweetalert2.min.js')}}"></script>
<script src="{{ URL::asset('assets/pages/sweet-alert.init.js')}}"></script>

<!-- Datatable init js -->
<script src="{{ URL::asset('assets/pages/datatables.init.js')}}"></script>

<!-- Parsley js -->
<script type="text/javascript" src="{{ URL::asset('assets/plugins/parsleyjs/parsley.min.js')}}"></script>
<script src="{{ URL::asset('assets/js/bootstrap-notify.js')}}"></script>
<script src="{{ URL::asset('assets/js/jquery.notify.min.js')}}"></script>

<script type="text/javascript">
    $(document).ready(function () {
        $('form').parsley();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

    });

    $(document).on("wheel", "input[type=number]", function (e) {
        $(this).blur();
    });

    //remove sorting of datatable
    $(document).ready(function() {

        if ($.fn.DataTable.isDataTable('#datatable')) {
        $('#datatable').DataTable().destroy();
        }

        $('#datatable').DataTable({
         "order": [], // Disable initial sorting
        //  "ordering": false, //disable all sorting
        "columnDefs": [
        { "orderable": false, "targets": [1,2,3] } // Disable sorting for target columns "targets": 'no-sort' if use a class in th as no sort
        ]
        });
    });
    //remove sorting of datatable end



    //Change Status
    function adMethod(dataID, tableName) {

        $.post('activateDeactivate', {id: dataID, table: tableName}, function (data) {

        });
    }

    //Populate data for Update Showtime Modal
     $(document).on('click', '#edit-showtime-btn', function () {
        var ShowtimeId = $(this).data('id');
        var time = $(this).data('time');
        
        
        $("#editShowtimeModal #hiddenShowtimeId").val(ShowtimeId);
        $("#editShowtimeModal #showId").text(`0${ShowtimeId}`);
        $("#editShowtimeModal #time").val(time);
    });

    //Hide Validation errors after closing the modal without refreshing
    $('#addShowtimeModal').on('hidden.bs.modal', function () {

                        //Add Showtime
                                $('#ShowtimeError').html('');
                                $('#ShowtimeError').html('');


                         //Update Showtime
                                 $('#updateShowtimeError').html('');
                                 $('#updateShowtimeError').html('');


                                $(this).find('input').val(''); // Clear all inputs inside Add Showtime Modal
                                $(this).find('.text-danger').html(''); // Clear error messages if any

    });


</script>

@include('includes/footer_end')