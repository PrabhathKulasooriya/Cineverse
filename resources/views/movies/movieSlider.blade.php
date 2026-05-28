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
                                    data-toggle="modal"  data-target="#addImageModal" >
                                Add Image
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
                                        <th>MOVIE NAME</th>
                                        <th>POSTER</th>
                                        <th>OPTIONS</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @if(isset( $imageSlider) )

                                        @foreach($imageSlider as $sliderImage)

                                            <tr>
                                                <td>{{ $sliderImage->movie_name }}</td>
                                                <td><img src="{{ URL::asset('sliderImages/'.$sliderImage->image) }}" style="width: 200px; "></td>
                                                <td style="display:flex; flex-direction:row; gap: 5px;">
                                                     <button class="btn btn-warning edit-movie-btn" 
                                                             data-toggle="modal" 
                                                             data-target="#editSliderModal-{{ $sliderImage->idimageSlider }}">
                                                        <i class="fa fa-edit"></i> 
                                                      </button>


                                                    <form action="{{route('destroySliderImage', ['idimageSlider'=>$sliderImage->idimageSlider])}}" method="post">
                                                        @csrf
                                                        @method('DELETE')

                                                        <button type='submit' class="btn btn-danger">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form> </td>
                                            </tr>

                                            <!-- Edit Modal for Each Image -->
                                            <div class="modal fade" id="editSliderModal-{{ $sliderImage->idimageSlider }}" tabindex="-1" role="dialog" aria-labelledby="editSliderModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                 <div class="modal-content">
                                                     <form action="{{ route('updateSliderImage', ['idimageSlider' => $sliderImage->idimageSlider]) }}" method="post" enctype="multipart/form-data">
                                                         @csrf
                                                         @method('PUT')

                                                            <div class="modal-header">
                                                             <h5 class="modal-title mt-0">Edit Image</h5>
                                                             <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                                         </div>

                                                         <div class="modal-body">
                                                              <div class="form-group">
                                                                <label>Movie Name <span style="color:red">*</span> </label>
                                                                    <select class="form-control" name="movie_id" id="movie_id" required>
                                                                        <option value="" disabled>-- Select a Movie --</option>
                                                                        @foreach ($movies as $movie) 
                                                                            <option value="{{ $movie->movie_id }}" @if ($movie->movie_id == $sliderImage->movies_movie_id) selected @endif>
                                                                                {{ $movie->name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                    <span class="text-danger" id="nameError"></span>
                                                              </div>
                                                         
                                                             <div class="form-group">
                                                                 <label>Image (Optional)</label>
                                                                 <input type="file" class="form-control" name="image" accept="image/*" />
                                                             </div>
                                                         
                                                             <div class="form-group">
                                                                 <button type="submit" class="btn btn-primary float-right">Update Movie</button>
                                                              </div>
                                                           </div>
                                                       </form>
                                                    </div>
                                               </div>
                                            </div>
                                            <!-- Edit Modal for Each Image End-->

                                        @endforeach
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








<!-- Add Image Modal Start-->
<div class="modal fade" id="addImageModal" tabindex="-1"
     role="dialog"
     aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title mt-0">Add Image</h5>
                <button type="button" class="close" data-dismiss="modal"
                        aria-hidden="true">×
                </button>
            </div>
            
                <div class="modal-body">

                    <form action="{{ route('saveSliderImage') }}" method="post" id="addSliderImageForm" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label>Movie Name <span style="color:red">*</span> </label>
                            <select class="form-control" name="movie_id" id="movie_id" required>
                                <option value="" disabled selected>-- Select a Movie --</option>
                                @foreach ($movies as $movie) 
                                    <option value="{{ $movie->movie_id }}"> {{ $movie->name }} </option>
                                @endforeach
                            </select>
                            <span class="text-danger" id="nameError"></span>
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

                     </form>

                </div>
        </div>

    </div>
</div>
<!-- Add Image Modal End-->




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

<script>
    $(document).ready(function () {
        $('form').parsley();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

    });

    //remove sorting of datatable
    $(document).ready(function() {

    if ($.fn.DataTable.isDataTable('#datatable')) { 
        $('#datatable').DataTable().destroy();
        }

    $('#datatable').DataTable({
        "order": [], // Disable initial sorting
        "columnDefs": [
        { "orderable": false, "targets": [1, 2] } // Disable sorting for target columns "targets": 'no-sort' if use a class in th as no sort
        ]
    });
    });
//remove sorting of datatable end

    
    $(document).on("wheel", "input[type=number]", function (e) {
        $(this).blur();
    });

    $('.modal').on('hidden.bs.modal', function () {
        $('input').val(''); 
        $('select').prop('selectedIndex', 0);
    });
    
</script>


@include('includes/footer_end')