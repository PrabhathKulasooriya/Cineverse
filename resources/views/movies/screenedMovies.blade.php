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

                    @if(session('success'))
                        <div class="alert alert-success text-center ticket-success-container" id="ticket-success-container">
                            <i class="fa fa-exclamation-circle" aria-hidden="true"></i> {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger text-center">
                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
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
                                        <th>MOVIE NAME</th>
                                        <th>CATEGORY</th>
                                        <th>DURATION</th>
                                        <th>LANGUAGE</th>
                                        <th>POSTER</th>
                                        <th>RATING</th>
                                        <th>REL_DATE</th>
                                    
                                        <th>OPTIONS</th>
                                    </tr>
                                </thead>

                                <tbody>

                               
                                @if(isset($movies))
                                    @if(count($movies)>0)
                                        @foreach($movies as $movie)

                                            <tr>
                                                <td>{{$movie->name}}</td>
                                                <td>{{$movie->category}}</td>
                                                <td>{{$movie->formatted_duration}}</td>
                                                <td>{{$movie->language}}</td>

                                                <td>
                                                    <button type="button" class="btn btn-secondary  float-right"
                                                        id="viewPosterBtn"
                                                        data-toggle="modal"
                                                        data-name = "{{$movie->name}}"
                                                        data-trailer="{{$movie->trailer}}"
                                                        data-poster="{{$movie->image}}"
                                                        data-target="#viewPosterModal" >
                                                        View Poster
                                                    </button>
                                                </td>

                                                <td>{{$movie->rating}}</td>
                                                <td>{{\Carbon\Carbon::parse($movie->release_date)->format('d-m-Y')}}</td>


                                                


                                                <td style="display:flex; flex-direction:row; gap: 5px;">


                                                        <form action="{{route('activateScreenMovie', ['movie_id'=>$movie->movie_id])}}" method="post">
                                                            @csrf
                                                            @method('PUT')
                                                        <button type='submit' class="btn btn-sm  btn-success" id="deleteMovieBtn"><i class="fa fa-plus-square"></i></button>
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



<!-- View Poster Modal -->
<div class="modal fade" id="viewPosterModal" tabindex="-1"
     role="dialog"
     aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog">

        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mt-0" id="viewPosterHeader"></h5>
                <button type="button" class="close" data-dismiss="modal"
                        aria-hidden="true">×
                </button>
            </div>

            <div class="modal-body view-poster-modal-body" >
    
                <div class="form-group view-poster-modal-body">
                    <img class="img-fluid" id="poster" src="" alt="Poster" style="max-width: 65%; height: auto; " />
                </div>
                
                <div class="form-group">
                    <a href="" id="trailer" target="_blank" class="btn btn-secondary">Watch Trailer</a>
                </div>
            </div>
            
        </div>
    </div>
</div>
<!-- View Poster Modal -->


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
            "columnDefs": [
            { "orderable": false, "targets": [4, 7] } // Disable sorting for target columns "targets": 'no-sort' if use a class in th as no sort
            ]
        });
        });
    


    
    //Populate data for view poster modal
    const imageBaseUrl = "{{ asset('movieImages') }}";
    $(document).on('click','#viewPosterBtn',function(){
        var name = $(this).data('name');
        var poster = $(this).data('poster');
        var trailer = $(this).data('trailer');
        var fullpath = imageBaseUrl + '/' + poster;

        $("#viewPosterModal #viewPosterHeader").text(name);
        $("#viewPosterModal #trailer").attr('href',trailer);
        $("#viewPosterModal #poster").attr('src',fullpath);
    });



</script>

@include('includes/footer_end')