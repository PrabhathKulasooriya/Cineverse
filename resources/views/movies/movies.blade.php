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

                        @if(Auth::user()->user_role_iduser_role==1 || Auth::user()->user_role_iduser_role==2)
                        <div class="col-lg-4">
                            <button type="button" class="btn btn-primary float-right"
                                    data-toggle="modal"  data-target="#addMovieModal" >
                                Add Movie
                            </button>
                        </div>
                        @endif
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success text-center ticket-success-container" id="ticket-success-container">
                            <i class="fa fa-exclamation-circle" aria-hidden="true"></i> {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger text-center">
                            <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
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
                                        @if(Auth::user()->user_role_iduser_role == 1 || Auth::user()->user_role_iduser_role == 2)
                                        <th>STATUS</th>
                                        <th>OPTIONS</th>
                                        @endif
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


                                                @if(Auth::user()->user_role_iduser_role == 1 || Auth::user()->user_role_iduser_role == 2)
                                                 <!--Status Start-->
                                                @if($movie->status == 1)
                                                    <td>
                                                        <p>
                                                            <input type="checkbox"
                                                                   onchange="adMethod('{{ $movie->movie_id}}','movies')"
                                                                   id="{{"c".$movie->movie_id}}" checked
                                                                   switch="none"/>
                                                            <label for="{{"c".$movie->movie_id}}"
                                                                   data-on-label="On"
                                                                   data-off-label="Off"></label>
                                                        </p>
                                                    </td>


                                                @else
                                                    <td>
                                                        <p>
                                                            <input type="checkbox"
                                                                   onchange="adMethod('{{ $movie->movie_id}}','movies')"
                                                                   id="{{"c".$movie->movie_id}}"
                                                                   switch="none"/>
                                                            <label for="{{"c".$movie->movie_id}}"
                                                                   data-on-label="On"
                                                                   data-off-label="Off"></label>
                                                        </p>
                                                    </td>

                                                @endif
                                                <!--Status End-->


                                                <td style="display:flex; flex-direction:row; gap: 5px;">

                                                    <button class="btn btn-sm btn-warning  edit-movie-btn" 
                                                         data-toggle="modal" 

                                                            data-id="{{$movie->movie_id}}"
                                                            data-name="{{$movie->name}}"
                                                            data-category="{{$movie->category}}"
                                                            data-duration="{{$movie->formatted_duration}}"
                                                            data-language="{{$movie->language}}"
                                                            data-rating="{{$movie->rating}}"
                                                            data-date="{{$movie->release_date}}"
                                                            data-trailer="{{$movie->trailer}}"

                                                            id="edit-movie-btn"     
                                                            data-target="#editMovieModal">
                                                          <i class="fa fa-edit"></i>
                                                        </button>

                                                        <form action="{{route('destroyMovie', ['movie_id'=>$movie->movie_id])}}" method="post">
                                                            @csrf
                                                            @method('DELETE')
                                                        <button type='submit' class="btn btn-sm  btn-danger" id="deleteMovieBtn"><i class="fa fa-trash"></i></button>
                                                        </form>
                                                </td>
                                                @endif
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








<!-- Add Movie Modal Start-->
<div class="modal fade" id="addMovieModal" tabindex="-1"
     role="dialog"
     aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title mt-0">Add Movie</h5>
                <button type="button" class="close" data-dismiss="modal"
                        aria-hidden="true">×
                </button>
            </div>
            
                <div class="modal-body" >

                    <form action="{{ route('saveMovie') }}" method="post" id="addMovieForm" enctype="multipart/form-data" >
                        @csrf

                        <div>
                        <div class="form-group">
                            <label>Movie Name <span style="color:red">*</span> </label>
                            <input type="text" class="form-control" name="name"
                                id="name" required placeholder="Movie Name" />
                            <span class="text-danger" id="nameError"></span>
                        </div>

                        <div class="form-group">
                         <label>Category <span style="color:red">*</span> </label>
                            <input type="text" class="form-control" name="category"
                                 id="category" required placeholder="category" />
                          <span class="text-danger" id="categoryError"></span>
                        </div>

                        <div class="form-group">
                            <label>Duration <span style="color:red">*</span> </label>
                            <input type="text" class="form-control" name="duration"
                                id="duration" required placeholder="2h 15min" />
                            <span class="text-danger" id="durationError"></span>
                        </div>

                        <div class="form-group">
                            <label>Language <span style="color:red">*</span> </label>
                            <input type="text" class="form-control" name="language"
                                id="language" required placeholder="Movie Language" />
                            <span class="text-danger" id="languageError"></span>
                        </div>
                        </div>

                        <div>
                        <div class="form-group">
                            <label>Rating <span style="color:red">*</span> </label>
                            <input type="number" step="0.1" max="10" min="0" class="form-control" name="rating"
                                id="rating" required placeholder="Movie Rating 1-10" />
                            <span class="text-danger" id="ratingError"></span>
                        </div>

                        <div class="form-group">
                            <label >Release Date <span style="color: red">*</span> </label>
                            <input type="date" class="form-control" id="date"
                                   name="date" placeholder="date" required>
                            <small class="text-danger" id="dateError"></small>
                        </div>

                        <div class="form-group">
                            <label>Trailer <span style="color:red">*</span> </label>
                            <input type="text" class="form-control" name="trailer"
                                id="trailer" required placeholder="Movie Trailer" />
                            <span class="text-danger" id="trailerError"></span>
                        </div>

                        <div class="form-group">
                            <label>Image <span style="color:red">*</span> </label>
                            <input type="file" class="form-control" name="image" id="image" accept="image/*"/>
                            <span class="text-danger" id="imageError"></span>
                        </div>
                        

                        <div class="form-group">
                            <button type="submit"  class="btn btn-primary float-right" >
                                 Save Movie
                            </button>
                        </div>
                        </div>

                     </form>

                </div>
        </div>

    </div>
</div>
<!-- Add Movie Modal End-->




<!-- Edit Movie Modal -->
<div class="modal fade" id="editMovieModal" tabindex="-1"
     role="dialog"
     aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog">

        <div class="modal-content">
            <form action="{{ route('updateMovie') }}" method="post" enctype="multipart/form-data" id="editMovieForm">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title mt-0">Edit Movie</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                
                <div class="modal-body" >

                    <div class="form-group">
                        <label>Movie Name <span style="color:red">*</span></label>
                        <input type="hidden" id="hiddenMovieId" name="hiddenMovieId">
                        <input type="text" class="form-control" name="name" id="name" required placeholder="Movie Name" />
                    </div>

                    <div class="form-group">
                        <label>Category <span style="color:red">*</span></label>
                        <input type="text" class="form-control" name="category" id="category" required placeholder="Category" />
                    </div>

                    <div class="form-group">
                        <label>Duration <span style="color:red">*</span></label>
                        <input type="text" class="form-control" name="duration" id="duration" required placeholder=" *h **min" />
                    </div>

                    <div class="form-group">
                        <label>Language <span style="color:red">*</span></label>
                        <input type="text" class="form-control" name="language" id="language" required placeholder="Movie Language" />
                    </div>

                    <div class="form-group">
                        <label>Rating <span style="color:red">*</span></label>
                        <input type="number" step="0.1" class="form-control" name="rating" id="rating" required placeholder="Movie Rating" max="10" min="0"/>
                    </div>

                    <div class="form-group">
                        <label >Release Date <span style="color: red">*</span> </label>
                        <input type="date" class="form-control" id="date" name="date" required placeholder="date">
                    </div>

                    <div class="form-group">
                        <label>Trailer <span style="color:red">*</span></label>
                        <input type="text" class="form-control" name="trailer" id="trailer" required placeholder="Movie Trailer" />
                    </div>

                    <div class="form-group">
                        <label>Image (Optional)</label>
                        <input type="file" class="form-control" name="image" id="image" accept="image/*"/>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary float-right" id="updateMovie">Update Movie</button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
<!-- Edit Movie Modal End-->

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
            "order": [0,'desc'], 
            "columnDefs": [
            { "orderable": false, "targets": [4, -1] } 
            ]
        });
        });
    


    //Change Status
    function adMethod(dataID, tableName) {

        $.post('activateDeactivate', {id: dataID, table: tableName}, function (data) {

        });
    }

    //Populate data for Update Movie Modal
     $(document).on('click', '#edit-movie-btn', function () {
        var movieId = $(this).data('id');
        var name = $(this).data('name');
        var category = $(this).data('category');
        var duration = $(this).data('duration');
        var language = $(this).data('language');
        var rating = $(this).data('rating');
        var date = $(this).data('date');
        var trailer = $(this).data('trailer');
        
        $("#editMovieModal #hiddenMovieId").val(movieId);
        $("#editMovieModal #name").val(name);
        $("#editMovieModal #category").val(category);
        $("#editMovieModal #duration").val(duration);
        $("#editMovieModal #language").val(language);
        $("#editMovieModal #rating").val(rating);
        $("#editMovieModal #date").val(date);
        $("#editMovieModal #trailer").val(trailer);
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


    //Hide Validation errors after closing the modal without refreshing
    $('#addMovieModal').on('hidden.bs.modal', function () {

                        //Add Movie
                                $('#movieError').html('');
                                $('#movieError').html('');


                         //Update Movie
                                 $('#updateMovieError').html('');
                                 $('#updateMovieError').html('');


                                $(this).find('input').val(''); // Clear all inputs inside Add Movie Modal
                                $(this).find('.text-danger').html(''); // Clear error messages if any

    });


</script>

@include('includes/footer_end')