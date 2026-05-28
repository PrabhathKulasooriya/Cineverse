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

                        @if(Auth::user()->user_role_iduser_role == 1 || Auth::user()->user_role_iduser_role == 2)
                        <div class="row">
                            <div class="col-lg-8">
                            </div>
                            <div class="col-lg-4">
                                <button type="button" class="btn btn-primary float-right"
                                        data-toggle="modal"  data-target="#addShowModal" >
                                    Create Shows</button>
                            </div>
                        </div>
                        @endif


                <br/>



                        <!--Data Table Start-->

                        <div class="table-rep-plugin">
                            <div class="table-responsive b-0" data-pattern="priority-columns">


                                <table id="datatable"   class="table table-striped table-bordered"
                                    cellspacing="0"
                                    width="100%">

                                    <thead>
                                    <tr>
                                        <th>SHOW ID</th>
                                        <th>MOVIE</th>
                                        <th>DATE</th>
                                        <th>TIME</th>
                                        <th>DAY</th>                                 
                                        <th class="text-center">OPTIONS</th>

                                    </tr>
                                    </thead>

                                    <tbody>

                                    @if(isset($shows))
                                        @if(count($shows)>0)
                                            @foreach($shows as $show)

                                                <tr>
                                                    <td>S-{{$show->show_id}}</td>

                                                    <td>{{$show->movie_name}}</td>

                                                    <td>{{ \Carbon\Carbon::parse($show->date)->format('d-m-Y') }}</td>

                                                    <td>{{\Carbon\Carbon::parse($show->time)->format('h:i A')}}</td>

                                                    <td>{{\Carbon\Carbon::parse($show->date)->format('l')}}</td>

                                                
                                                    <!--Options Start-->
                                                    <td class=" d-flex justify-content-center">
                                                        
                                                        @if(Auth::user()->user_role_iduser_role == 1 || Auth::user()->user_role_iduser_role == 3)

                                                            <a href="{{ route('seatSelection', ['show_id' => $show->show_id]) }}">
                                                                <button type="button"
                                                                        class="btn btn-sm btn-success waves-effect waves-light mr-2"
                                                                        data-id="{{$show->show_id}}"
                                                                        id="bookTicketBtn">
                                                                    <i class="fa fa-ticket"></i>
                                                                    Book Tickets
                                                                </button>
                                                            </a>
                                                        @endif
                                                        @if(Auth::user()->user_role_iduser_role == 1 || Auth::user()->user_role_iduser_role == 2)
                                                            

                                                            <button type="button"
                                                                    class="btn btn-sm btn-warning  waves-effect waves-light mr-2"
                                                                    data-toggle="modal"

                                                                    data-id="{{$show->show_id}}"
                                                                    data-date="{{$show->date}}"
                                                                    data-time="{{$show->time}}"
                                                                    data-movie="{{$show->movies_movie_id}}"

                                                                    id="updateShowID"
                                                                    data-target="#updateShowModal">
                                                                    <i class="fa fa-edit"></i>
                                                            </button>

                                                            
                                                            <div class="delete-show-button mr-2"> 
                                                                    {{csrf_field()}}
                                                                    <input type="hidden" name="show_id" id="show_id" value="{{$show->show_id}}">
                                                                    <button type="button" class="btn btn-sm btn-danger waves-effect waves-light" id="deleteShowBtn"> 
                                                                        <i class="fa fa-trash"></i>
                                                                    </button>
                                                            </div>
                                                            

                                                        @endif
                                                        
                                                    </td>
                                                    <!--Options End-->
                                                

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





    <!-- Add Show Modal Start-->
    <div class="modal fade" id="addShowModal" tabindex="-1"
        role="dialog"
        aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title mt-0">Create Show</h5>
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">×
                    </button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label>Movie<span style="color: red">*</span></label>
                        <select class="form-control" name="movie" id="movie" required>
                            <option value="" disabled selected>-- Select a Movie --</option>
                            @foreach ($movies as $movie) 
                                <option value="{{ $movie->movie_id }}"> {{ $movie->name }} </option>
                            @endforeach
                        </select>
                        <small class="text-danger" id="movieError"></small>
                    </div>

                    <div class="form-group">
                        <label > Date <span style="color: red">*</span> </label>
                        <input type="text" class="form-control" id="date" name="date"  required placeholder="YYYYY-MM-DD" 
                                    min="<?= date('Y-m-d') ?>" 
                                    max="<?= date('Y-m-d', strtotime('+3 months')) ?>" readonly >
                        <small class="text-danger" id="dateError"></small>
                    </div>

                    <div class="form-group">
                        <label>Time<span style="color: red"> *</span></label>
                        <select class="form-control" name="time" id="time" required>
                            <option value="" disabled selected>-- Select a Showtime --</option>
                            @foreach ($showtimes as $showtime) 
                                <option value="{{ $showtime->time }}"> 
                                    {{$showtime->idshowtimes}}
                                    .
                                    {{ \Carbon\Carbon::createFromFormat('H:i:s', $showtime->time)->format('h:i A') }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-danger" id="timeError"></small>
                    </div>

                    <div class="form-group">
                        <button type="button"  class="btn btn-primary float-right"
                                onclick="addShow()" >
                            Create Show
                        </button>
                    </div>

                </div>

            </div>

        </div>
    </div>
    <!-- Add Show Modal End-->




    <!-- Update Show Modal Start-->
    <div class="modal fade" id="updateShowModal" tabindex="-1"
        role="dialog"
        aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title mt-0">Update Show</h5>
                    <button type="button" class="close" data-dismiss="modal"
                            aria-hidden="true">×
                    </button>
                </div>

                <div class="modal-body">

                    <div class="form-group">
                        <label>Movie<span style="color: red">*</span></label>

                        <input type="hidden" id="hiddenMovieID" name="hiddenMovieID">

                        <select class="form-control" name="movie" id="updateMovie" required>
                            <option value="" disabled selected>-- Select a Movie --</option>
                            @foreach ($movies as $movie) 
                                <option value="{{ $movie->movie_id }}"> {{ $movie->name }} </option>
                            @endforeach
                        </select>
                        <small class="text-danger" id="updateMovieError"></small>
                    </div>

                    <div class="form-group">
                        <label > Date <span style="color: red">*</span> </label>
                        <input type="text" class="form-control" id="updateDate"
                            name="date" required placeholder="date"
                             min="<?= date('Y-m-d') ?>" 
                             max="<?= date('Y-m-d', strtotime('+3 months')) ?>" readonly>
                        <small class="text-danger" id="updateDateError"></small>
                    </div>

                    <div class="form-group">
                        <label>Time<span style="color: red"> *</span></label>
                        <select class="form-control" name="time" id="updateTime" required>
                        </select>
                        <small class="text-danger" id="updateTimeError"></small>
                    </div>

                    <div class="form-group">
                        <button type="button"  class="btn btn-primary float-right"
                                onclick="updateShow()" >
                            Update Show</button>
                    </div>

                </div>

            </div>

        </div>
    </div>
    <!-- Update Show Modal End-->



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


        //change sorting of datatable
        $.fn.dataTable.ext.type.order['id-num-pre'] = function (d) {
            var match = d.match(/\d+/);
            return match ? parseInt(match[0], 10) : 0;
        };
        
        $.fn.dataTable.ext.type.detect.unshift(function (d) {
            return (typeof d === 'string' && d.match(/^\d{1,2}-\d{1,2}-\d{4}$/)) ? 'custom-date' : null;
        });

        $.fn.dataTable.ext.type.order['custom-date-pre'] = function (d) {
            if (!d) return 0;
            var parts = d.split('-');
            return new Date(parts[2], parts[1] - 1, parts[0]).getTime();
        };

        $(document).ready(function () {
            if ($.fn.DataTable.isDataTable('#datatable')) {
                $('#datatable').DataTable().destroy();
            }
            
            $('#datatable').DataTable({
                "order": [2, 'asc'],
                "columnDefs": [
                    { "orderable": false, "targets": [3, 4, -1] },
                    { "type": "id-num", "targets": 0 },
                    { "type": "custom-date", "targets": 2 }
                ]
            });
        });


        
        $(document).on("wheel", "input[type=number]", function (e) {
            $(this).blur();
        });

        function adMethod(dataID, tableName) {

            $.post('activateDeactivate', {id: dataID, table: tableName}, function (data) {


            });
        }



        //Save Show Start


        function addShow(){

            console.log("addShow function called");

            $("#movieError").html('');
            $("#dateError").html('');
            $("#timeError").html('');
            



            var movie = $("#movie").val();
            var date = $("#date").val();
            var time = $("#time").val();
            



            $.post('{{ route('saveShow') }}',{

                movie:movie,
                date:date,
                time:time,

            },function (data) {


                if (data.errors != null) {


                    if(data.errors.movie) {
                        var p = document.getElementById('movieError');
                        p.innerHTML = data.errors.movie[0];
                    }

                    if(data.errors.date) {
                        var p = document.getElementById('dateError');
                        p.innerHTML = data.errors.date[0];
                    }

                    if(data.errors.time) {
                        var p = document.getElementById('timeError');
                        p.innerHTML = data.errors.time[0];
                    }

                    
                }


                //On success
                if (data.success != null) {
                    notify({
                        type: "success",
                        title: 'Show SAVED',
                        autoHide: true,
                        delay: 2500,
                        position: {
                            x: "right",
                            y: "top"
                        },
                        icon: '<img src="{{ URL::asset('assets/images/correct.png')}}" />',

                        message: data.success,
                    });

                    $('input').val('');
                    setTimeout(function () {
                        $('#addShowModal').modal('hide');
                    }, 200);

                    setTimeout(function () {
                        location.reload();
                    }, 1000);
                }

                //On errors
                if(data.errors != null){
                    notify({
                        type: "error", //alert | success | error | warning | info
                        title: 'Show NOT CREATED',
                        autoHide: true, //true | false
                        delay: 2500, //number ms
                        position: {
                            x: "right",
                            y: "top"
                        },
                        icon: '<img src="{{ URL::asset('assets/images/wrong.png')}}" />',
                        message: data.errors,
                    });
                    $('input').val('');
                    setTimeout(function () {
                        $('#addShowModal').modal('hide');
                    }, 200);


                }
        
            });

        }
        //Save Show End



        //Update Show Start
        $(document).on('click', '#updateShowID', function () {

            
            var id = $(this).data("id");
            var date = $(this).data("date");
            var time = $(this).data("time");
            var movieId = $(this).data("movie");


            $("#hiddenMovieID").val(id);
            $("#updateDate").val(date);
            
            $("#updateMovie").val(movieId).change();
            $("#updateTime").val(time).change();
            

        });

        function updateShow() {


            $('#updateMovieError').html('');
            $("#updateDateError").html('');
            $("#updateTimeError").html('');


            var hiddenMovieID=$("#hiddenMovieID").val();
            var movie=$("#updateMovie").val();
            var date=$("#updateDate").val();
            var time=$("#updateTime").val();

            $.post('{{ route('updateShow') }}',{

                hiddenMovieID:hiddenMovieID,
                movie:movie,
                date:date,
                time:time,

            },function (data) {


                if (data.errors != null) {

                    if(data.errors.movie){
                        var p = document.getElementById('updateMovieError');
                        p.innerHTML = data.errors.movie[0];
                    }

                    if(data.errors.date){
                        var p = document.getElementById('updateDateError');
                        p.innerHTML = data.errors.date[0];
                    }


                    if(data.errors.time){
                        var p = document.getElementById('updateTimeError');
                        p.innerHTML = data.errors.time[0];
                    }


                }

                //On Success
                if(data.success != null){
                    notify({
                        type: "success", //alert | success | error | warning | info
                        title: 'Show UPDATED',
                        autoHide: true, //true | false
                        delay: 2500, //number ms
                        position: {
                            x: "right",
                            y: "top"
                        },
                        icon: '<img src="{{ URL::asset('assets/images/correct.png')}}" />',
                        message: data.success,
                    });
                    $('input').val('');
                    setTimeout(function () {
                        $('#updateShowModal').modal('hide');
                    }, 200);

                    setTimeout(function () {
                        location.reload();
                    }, 1000);


                }

                //On errors
                if(data.errors != null){
                    notify({
                        type: "error", //alert | success | error | warning | info
                        title: 'Show NOT UPDATED',
                        autoHide: true, //true | false
                        delay: 2500, //number ms
                        position: {
                            x: "right",
                            y: "top"
                        },
                        icon: '<img src="{{ URL::asset('assets/images/wrong.png')}}" />',
                        message: data.errors,
                    });
                    $('input').val('');
                    setTimeout(function () {
                        $('#updateShowModal').modal('hide');
                    }, 200);


                }
            })
        }
        //Update Show End

        //Delete Show Start

   
        $(document).ready(function() {
           
            $(document).on('click', '#deleteShowBtn', function(e) {
                e.preventDefault();
                var showId = $(this).closest('.delete-show-button').find('#show_id').val();
                if (showId) {
                    deleteShow(showId);
                } else {
                    console.error("Show ID not found");
                }
            });
        });


        function deleteShow(showId) {
            console.log("deleteShow function called for ID:", showId);
            
            // Show confirmation dialog
            if (!confirm("Are you sure you want to delete this show? This action cannot be undone.")) {
                return;
            }
            
            $.post('{{ route('destroyShow') }}', {
                show_id: showId,
                _token: '{{ csrf_token() }}'
            }, function(data) {
                
                // On success
                if (data.success != null) {
                    notify({
                        type: "success",
                        title: 'Show DELETED',
                        autoHide: true,
                        delay: 2500,
                        position: {
                            x: "right",
                            y: "top"
                        },
                        icon: '<img src="{{ URL::asset('assets/images/correct.png')}}" />',
                        message: data.success,
                    });
                    
                    // Reload page after short delay
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                }
                
                // On errors
                if (data.errors != null) {
                    notify({
                        type: "error",
                        title: 'Show NOT DELETED',
                        autoHide: true,
                        delay: 2500,
                        position: {
                            x: "right",
                            y: "top"
                        },
                        icon: '<img src="{{ URL::asset('assets/images/wrong.png')}}" />',
                        message: data.errors,
                    });
                }
                
            }).fail(function(xhr, status, error) {
                // Handle AJAX errors
                console.error('AJAX Error:', error);
                notify({
                    type: "error",
                    title: 'Error',
                    autoHide: true,
                    delay: 2500,
                    position: {
                        x: "right",
                        y: "top"
                    },
                    icon: '<img src="{{ URL::asset('assets/images/wrong.png')}}" />',
                    message: 'An error occurred while deleting the show.',
                });
            });
        }

        //Delete Show End



        //Hide Validation errors after closing the modal without refreshing
        $('.modal').on('hidden.bs.modal', function () {


            $("#movieError").html('');
            $("#dateError").html('');
            $("#timeError").html('');
            

            $('#updateMovieError').html('');
            $("#updateDateError").html('');
            $("#updateTimeError").html('');


            $('input').val(''); //Clear input values of input fields

        });

    //Disable Fully Booked Dates*******************************************************************************
    const fullyBookedDates = @json($fullyBookedDates);
    console.log(fullyBookedDates);

    document.addEventListener('DOMContentLoaded', function () {
        // For the add show modal
        const dateInput = document.getElementById('date');
        const updateDateInput = document.getElementById('updateDate');

        // Set min date to today and max date to 3 months from today
        const today = new Date();
        const maxDate = new Date();
        maxDate.setMonth(today.getMonth() + 3);

        // Initialize date picker for add modal
        $(dateInput).datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            startDate: today, // No dates before today can be selected
            endDate: maxDate, // No dates beyond 3 months can be selected
            beforeShowDay: function(date) {
                const formattedDate = date.getFullYear() + '-' + 
                                        String(date.getMonth() + 1).padStart(2, '0') + '-' + 
                                        String(date.getDate()).padStart(2, '0');
                return {
                    enabled: !fullyBookedDates.includes(formattedDate)
                };
            }
        }).on('changeDate', function(e) {
            // Update the input with the selected date
            dateInput.value = e.format('yyyy-mm-dd');
            document.getElementById('dateError').innerText = "";
        });

        // Initialize date picker for update modal
        $(updateDateInput).datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            startDate: today, // No dates before today can be selected
            endDate: maxDate, // No dates beyond 3 months can be selected
            beforeShowDay: function(date) {
                const formattedDate = date.getFullYear() + '-' + 
                                        String(date.getMonth() + 1).padStart(2, '0') + '-' + 
                                        String(date.getDate()).padStart(2, '0');
                return {
                    enabled: !fullyBookedDates.includes(formattedDate)
                };
            }
        }).on('changeDate', function(e) {
            // Update the input with the selected date
            updateDateInput.value = e.format('yyyy-mm-dd');
            document.getElementById('updateDateError').innerText = "";
        });
    });

    //Disable Fully Booked Dates End

    //Get availabe shows**************************************************************************************************
    function loadAvailableShowtimes(date, targetSelectId, currentTime = null) {
        if (!date) {
            resetShowtimeSelect(targetSelectId);
            return;
        }
        
        $('#' + targetSelectId).html('<option value="" disabled selected>Loading showtimes...</option>');
        
        // Make an AJAX request to get available showtimes for the selected date
        $.ajax({
            url: '{{ route("getAvailableShowtimes") }}',
            type: 'POST',
            data: {
                date: date,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                updateShowtimeSelect(response.showtimes, targetSelectId, currentTime);
            },
            error: function(xhr) {
                console.error('Error loading showtimes:', xhr.responseText);
                
                
                var selectElement = $('#' + targetSelectId);
                selectElement.empty();
                selectElement.append('<option value="" disabled selected>Error loading showtimes</option>');
                
                
                setTimeout(function() {
                    resetShowtimeSelect(targetSelectId);
                }, 2000);
            }
        });
    }

    // Function to update the showtime select dropdown with available options
    function updateShowtimeSelect(showtimes, targetSelectId, currentTime = null) {
        var selectElement = $('#' + targetSelectId);
        
        selectElement.empty();
        
        selectElement.append('<option value="" disabled>-- Select a Showtime --</option>');
        

        if (showtimes && showtimes.length > 0) {
            var currentTimeFound = false;
            $.each(showtimes, function(index, showtime) {
                var isSelected = currentTime && currentTime === showtime.time ? 'selected' : '';
                if (isSelected) {
                    currentTimeFound = true;
                }
                selectElement.append(
                    '<option value="' + showtime.time + '" ' + isSelected + '>' + 
                    showtime.idshowtimes + '. ' + 
                    showtime.formatted_time + 
                    '</option>'
                );
            });
            
            // If currentTime wasn't found in available showtimes, add it as a separate option
            if (currentTime && !currentTimeFound) {
                var currentShowtimeText = 'Current Showtime';
                @foreach ($showtimes as $showtime)
                if ('{{ $showtime->time }}' === currentTime) {
                    currentShowtimeText = '{{$showtime->idshowtimes}}. {{ \Carbon\Carbon::createFromFormat("H:i:s", $showtime->time)->format("h:i A") }} (Current)';
                }
                @endforeach
                
                selectElement.prepend('<option value="' + currentTime + '" selected>' + currentShowtimeText + '</option>');
            }
        } else {
            // No available showtimes, but if we have a current time, show it
            if (currentTime) {
                var currentShowtimeText = 'Current Showtime';
                @foreach ($showtimes as $showtime)
                if ('{{ $showtime->time }}' === currentTime) {
                    currentShowtimeText = '{{$showtime->idshowtimes}}. {{ \Carbon\Carbon::createFromFormat("H:i:s", $showtime->time)->format("h:i A") }} (Current)';
                }
                @endforeach
                
                selectElement.append('<option value="' + currentTime + '" selected>' + currentShowtimeText + '</option>');
            }
            selectElement.append('<option value="" disabled>No other showtimes available for this date</option>');
        }
    }

    // Function to reset showtime select to show all showtimes
    function resetShowtimeSelect(targetSelectId) {
        var selectElement = $('#' + targetSelectId);

        selectElement.empty();
        selectElement.append('<option value="" disabled selected>-- Select a Showtime --</option>');
        
        @foreach ($showtimes as $showtime)
        selectElement.append(
            '<option value="{{ $showtime->time }}">' + 
            '{{$showtime->idshowtimes}}. ' + 
            '{{ \Carbon\Carbon::createFromFormat("H:i:s", $showtime->time)->format("h:i A") }}' + 
            '</option>'
        );
        @endforeach
    }

    // Attach event handlers when document is ready
    $(document).ready(function() {
        // For the add show modal
        $('#date').change(function() {
            var selectedDate = $(this).val();
            loadAvailableShowtimes(selectedDate, 'time');
        });
        
        // For the update show modal
        $('#updateDate').change(function() {
            var selectedDate = $(this).val();
            var currentTime = $("#updateTime").data('current-time');
            loadAvailableShowtimes(selectedDate, 'updateTime', currentTime);
        });
        
        // Make sure we reset the showtime select when modals are opened
        $('#addShowModal').on('show.bs.modal', function() {
            resetShowtimeSelect('time');
        });
        
        // For the update modal, we need to fetch available showtimes when it opens
        $('#updateShowModal').on('show.bs.modal', function() {
            var selectedDate = $('#updateDate').val();
            var currentTime = $("#updateTime").data('current-time');
            if (selectedDate) {
                loadAvailableShowtimes(selectedDate, 'updateTime', currentTime);
            } else {
                resetShowtimeSelect('updateTime');
            }
        });
    });

    $(document).on('click', '#updateShowID', function() {
        var id = $(this).data("id");
        var date = $(this).data("date");
        var time = $(this).data("time");
        var movieId = $(this).data("movie");

        $("#hiddenMovieID").val(id);
        $("#updateDate").val(date);
        $("#updateMovie").val(movieId).change();
        
        
        $("#updateTime").data('current-time', time);
        
        $("#updateDate").trigger('change');
    });

    </script>
@include('includes/footer_end')