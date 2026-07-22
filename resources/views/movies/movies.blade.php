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
<link rel="stylesheet" href="{{ asset('css/management.css') }}">

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
                        <div class="alert alert-success text-center position-fixed fade show" style="top: 100px; right: 20px; z-index: 1000; min-width: 350px;">
                            <i class="fa fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger text-center position-fixed fade show" style="top: 100px; right: 20px; z-index: 1000; min-width: 350px;">
                            <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
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
                                                <td style="max-width: 200px;"> 
                                                    <div class="category-cards-container" style="margin-top: 0;">
                                                        @foreach(explode(',', $movie->category) as $cat)
                                                            @if(trim($cat) !== '')
                                                                <span class="category-card">{{ trim($cat) }}</span>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </td>
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

                                                        <form action="{{route('destroyMovie', ['movie_id'=>$movie->movie_id])}}" method="post" class="delete-movie-form">
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
 
            <div class="modal-body">
 
                <form action="{{ route('saveMovie') }}" method="post" id="addMovieForm" enctype="multipart/form-data">
                    @csrf
 
                    <div>
                        <div class="form-group">
                            <label>Movie Name <span style="color:red">*</span></label>
                            <input type="text" class="form-control" name="name"
                                   id="name" placeholder="Movie Name"/>
                            <span class="text-danger" id="nameError"></span>
                        </div>
 
                        <div class="form-group">
                            <label>Category <span style="color:red">*</span></label>
 
                            <select class="form-control" id="categorySelect">
                                <option value="">-- Select a category --</option>
                            </select>
 
                            <div class="category-cards-container" id="categoryCardsContainer"></div>
 
                            <input type="hidden" name="category" id="category">
                            <span class="text-danger" id="categoryError"></span>
                        </div>
 
                        <div class="form-group">
                            <label>Duration <span style="color:red">*</span></label>
                            <input type="text" class="form-control" name="duration"
                                   id="duration" placeholder="0h 00min" autocomplete="off" maxlength="8"/>
                            <span class="text-danger" id="durationError"></span>
                        </div>
 
                        <div class="form-group">
                            <label>Language <span style="color:red">*</span></label>
                            <input type="text" class="form-control" name="language"
                                   id="language" placeholder="Movie Language"/>
                            <span class="text-danger" id="languageError"></span>
                        </div>
                    </div>
 
                    <div>
                        <div class="form-group">
                            <label>Rating <span style="color:red">*</span></label>
                            <input type="number" step="0.1" max="10" min="0" class="form-control" name="rating"
                                   id="rating" placeholder="Movie Rating 1-10"/>
                            <span class="text-danger" id="ratingError"></span>
                        </div>
 
                        <div class="form-group">
                            <label>Release Date <span style="color: red">*</span></label>
                            <input type="date" class="form-control" id="date"
                                   name="date" placeholder="date">
                            <small class="text-danger" id="dateError"></small>
                        </div>
 
                        <div class="form-group">
                            <label>Trailer <span style="color:red">*</span></label>
                            <input type="text" class="form-control" name="trailer"
                                   id="trailer" placeholder="Movie Trailer"/>
                            <span class="text-danger" id="trailerError"></span>
                        </div>
 
                        <div class="form-group">
                            <label>Image <span style="color:red">*</span></label>
                            <input type="file" class="form-control" name="image" id="image" accept="image/*"/>
                            <span class="text-danger" id="imageError"></span>
                        </div>
 
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary float-right">
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
 
                <div class="modal-body">
 
                    <div class="form-group">
                        <label>Movie Name <span style="color:red">*</span></label>
                        <input type="hidden" id="hiddenMovieId" name="hiddenMovieId">
                        <input type="text" class="form-control" name="name" id="name" placeholder="Movie Name"/>
                        <span class="text-danger" id="nameError"></span>
                    </div>
 
                    <div class="form-group">
                        <label>Category <span style="color:red">*</span></label>
 
                        <select class="form-control" id="categorySelect">
                            <option value="">-- Select a category --</option>
                        </select>
 
                        <div class="category-cards-container" id="categoryCardsContainer"></div>
 
                        <input type="hidden" name="category" id="category">
                        <span class="text-danger" id="categoryError"></span>
                    </div>
 
                    <div class="form-group">
                        <label>Duration <span style="color:red">*</span></label>
                        <input type="text" class="form-control" name="duration" id="duration"
                               placeholder="0h 00min" autocomplete="off" maxlength="8"/>
                        <span class="text-danger" id="durationError"></span>
                    </div>
 
                    <div class="form-group">
                        <label>Language <span style="color:red">*</span></label>
                        <input type="text" class="form-control" name="language" id="language" placeholder="Movie Language"/>
                        <span class="text-danger" id="languageError"></span>
                    </div>
 
                    <div class="form-group">
                        <label>Rating <span style="color:red">*</span></label>
                        <input type="number" step="0.1" class="form-control" name="rating" id="rating"
                               placeholder="Movie Rating" max="10" min="0"/>
                        <span class="text-danger" id="ratingError"></span>
                    </div>
 
                    <div class="form-group">
                        <label>Release Date <span style="color: red">*</span></label>
                        <input type="date" class="form-control" id="date" name="date" placeholder="date">
                        <small class="text-danger" id="dateError"></small>
                    </div>
 
                    <div class="form-group">
                        <label>Trailer <span style="color:red">*</span></label>
                        <input type="text" class="form-control" name="trailer" id="trailer" placeholder="Movie Trailer"/>
                        <span class="text-danger" id="trailerError"></span>
                    </div>
 
                    <div class="form-group">
                        <label>Image (Optional)</label>
                        <input type="file" class="form-control" name="image" id="image" accept="image/*"/>
                        <span class="text-danger" id="imageError"></span>
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

        setTimeout(function() {
            $(".alert").fadeOut("slow", function() {
                $(this).remove();
            });
        }, 3000);

        
        if ($.fn.DataTable.isDataTable('#datatable')) {
            $('#datatable').DataTable().destroy();
        }
        $('#datatable').DataTable({
            "order": [0, 'desc'], 
            "columnDefs": [
                { "orderable": false, "targets": [4, -1] } 
            ]
        });

        
        fillCategorySelect($('#addMovieModal #categorySelect'));
        fillCategorySelect($('#editMovieModal #categorySelect'));
        initDurationMask($('#addMovieModal #duration'));
        initDurationMask($('#editMovieModal #duration'));
    });


    $(document).on("wheel", "input[type=number]", function (e) {
        $(this).blur();
    });


    var availableCategories = [
    "Action", "Adventure", "Animation", "Comedy", "Crime", "Documentary", 
    "Drama", "Family", "Fantasy", "Horror", "Musical", "Mystery", 
    "Romance", "Sci-Fi", "Thriller"
];

    function fillCategorySelect(selectElement) {
        selectElement.empty();
        selectElement.append('<option value="">-- Select a category --</option>');
        availableCategories.forEach(function (categoryName) {
            selectElement.append('<option value="' + categoryName + '">' + categoryName + '</option>');
        });
    }

    function getSelectedCategories(modalId) {
        var selectedCategories = [];
        $(modalId + ' .category-card').each(function () {
            selectedCategories.push($(this).data('category'));
        });
        return selectedCategories;
    }

    function updateCategoryHiddenInput(modalId) {
        var selectedCategories = getSelectedCategories(modalId);
        $(modalId + ' #category').val(selectedCategories.join(','));
    }

    function refreshCategorySelectOptions(modalId) {
        var selectedCategories = getSelectedCategories(modalId);
        var selectElement = $(modalId + ' #categorySelect');
        selectElement.empty();
        selectElement.append('<option value="">-- Select a category --</option>');
        availableCategories.forEach(function (categoryName) {
            if (selectedCategories.indexOf(categoryName) === -1) {
                selectElement.append('<option value="' + categoryName + '">' + categoryName + '</option>');
            }
        });
    }

    function addCategoryCard(modalId, categoryName) {
        if (categoryName === '') return;

        var cardHtml = '<span class="category-card" data-category="' + categoryName + '">' +
            categoryName +
            '<button type="button" class="category-card-remove">&times;</button>' +
            '</span>';

        $(modalId + ' #categoryCardsContainer').append(cardHtml);
        updateCategoryHiddenInput(modalId);
        refreshCategorySelectOptions(modalId);
    }

    function clearCategoryCards(modalId) {
        $(modalId + ' #categoryCardsContainer').html('');
        $(modalId + ' #category').val('');
        refreshCategorySelectOptions(modalId);
    }

    function loadCategoryCards(modalId, categoryString) {
        clearCategoryCards(modalId);
        if (!categoryString) return;

        var categoryList = categoryString.split(',');
        categoryList.forEach(function (categoryName) {
            var trimmedName = categoryName.trim();
            if (trimmedName !== '') {
                addCategoryCard(modalId, trimmedName);
            }
        });
    }

    $(document).on('change', '#categorySelect', function () {
        var modalId = '#' + $(this).closest('.modal').attr('id');
        var selectedValue = $(this).val();
        addCategoryCard(modalId, selectedValue);
        $(this).val('');
    });

    $(document).on('click', '.category-card-remove', function () {
        var modalId = '#' + $(this).closest('.modal').attr('id');
        $(this).closest('.category-card').remove();
        updateCategoryHiddenInput(modalId);
        refreshCategorySelectOptions(modalId);
    });

    // DURATION MASK LOGIC 
    function initDurationMask(inputElement) {
        inputElement.data('durationDigits', ['', '', '']);
        inputElement.data('durationPosition', 0);
    }

    function renderDurationDisplay(inputElement) {
        var digits = inputElement.data('durationDigits');
        var hourDigit = digits[0] !== '' ? digits[0] : '0';
        var minuteTens = digits[1] !== '' ? digits[1] : '0';
        var minuteOnes = digits[2] !== '' ? digits[2] : '0';
        var displayValue = hourDigit + 'h' +" "+ minuteTens + minuteOnes + 'min';
        inputElement.val(displayValue);
    }

    function isDurationValid(inputElement) {
        var digits = inputElement.data('durationDigits');
        if (digits === undefined) return false;
        
        var hourValue = digits[0] !== '' ? parseInt(digits[0], 10) : 0;
        var minuteTens = digits[1] !== '' ? parseInt(digits[1], 10) : 0;
        var minuteOnes = digits[2] !== '' ? parseInt(digits[2], 10) : 0;
        var totalMinutes = (hourValue * 60) + (minuteTens * 10) + minuteOnes;
        return totalMinutes > 0;
    }

    function loadDurationValue(inputElement, durationText) {
        initDurationMask(inputElement);
        if (!durationText) {
            renderDurationDisplay(inputElement);
            return;
        }

        var durationPattern = /(\d)h\s*(\d)(\d)min/i;
        var matches = durationPattern.exec(durationText);

        if (matches) {
            var digits = ['', '', ''];
            digits[0] = matches[1];
            digits[1] = matches[2];
            digits[2] = matches[3];
            inputElement.data('durationDigits', digits);
            inputElement.data('durationPosition', 3);
        }
        renderDurationDisplay(inputElement);
    }

    function handleDurationKeydown(event) {
        var key = event.key;
        if (key === 'Tab' || key === 'Enter' || key === 'Shift' || key === 'Control' || key === 'Alt') return;

        var inputElement = $(this);
        var digits = inputElement.data('durationDigits');
        var position = inputElement.data('durationPosition');

        if (digits === undefined) {
            initDurationMask(inputElement);
            digits = inputElement.data('durationDigits');
            position = inputElement.data('durationPosition');
        }

        if (key >= '0' && key <= '9') {
            if (position === 0) {
                digits[0] = key;
                position = 1;
            } else if (position === 1) {
                if (key <= '5') {
                    digits[1] = key;
                    position = 2;
                }
            } else if (position === 2) {
                digits[2] = key;
                position = 3;
            }
            inputElement.data('durationDigits', digits);
            inputElement.data('durationPosition', position);
            renderDurationDisplay(inputElement);
        } else if (key === 'Backspace') {
            if (position > 0) {
                position = position - 1;
                digits[position] = '';
            }
            inputElement.data('durationDigits', digits);
            inputElement.data('durationPosition', position);
            renderDurationDisplay(inputElement);
        }
        event.preventDefault();
    }

    $(document).on('keydown', '#duration', handleDurationKeydown);
    $(document).on('paste', '#duration', function (event) {
        event.preventDefault();
    });



    function adMethod(dataID, tableName) {
        $.post('activateDeactivate', {id: dataID, table: tableName}, function (data) {
            
        });
    }

    // View Poster Modal 
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

    
    $('#addMovieModal').on('show.bs.modal', function () {
        clearCategoryCards('#addMovieModal');
        initDurationMask($('#addMovieModal #duration'));
        $('#addMovieModal #duration').val('');
    });

    $('#addMovieModal').on('hidden.bs.modal', function () {
        $(this).find('input[type=text], input[type=number], input[type=date]').val('');
        $(this).find('input[type=file]').val('');
        $(this).find('.text-danger').html('');
        $('#movieError').html(''); 
        clearCategoryCards('#addMovieModal');
        initDurationMask($('#addMovieModal #duration'));
    });

    // Reset Edit Movie Modal
    $('#editMovieModal').on('hidden.bs.modal', function () {
        $(this).find('.text-danger').html('');
        $('#updateMovieError').html(''); 
    });

    // Populate Edit Modal
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
        $("#editMovieModal #language").val(language);
        $("#editMovieModal #rating").val(rating);
        $("#editMovieModal #date").val(date);
        $("#editMovieModal #trailer").val(trailer);

        loadCategoryCards('#editMovieModal', category);
        loadDurationValue($('#editMovieModal #duration'), duration);
    });


    function showValidationErrors(modalId, errors) {
        $.each(errors, function (fieldName, messages) {
            $(modalId + ' #' + fieldName + 'Error').html(messages[0]);
        });
    }

    function clearFormErrors(modalId) {
        $(modalId + ' .text-danger').html('');
    }

    // Add Movie Submit
    $("#addMovieForm").on("submit", function (e) {
        e.preventDefault();
        clearFormErrors('#addMovieModal');

        var name = $('#addMovieModal #name').val().trim();
        var categoryValue = $('#addMovieModal #category').val();
        var durationInput = $('#addMovieModal #duration');
        var language = $('#addMovieModal #language').val().trim();
        var rating = $('#addMovieModal #rating').val().trim();
        var date = $('#addMovieModal #date').val();
        var trailer = $('#addMovieModal #trailer').val().trim();
        var image = $('#addMovieModal #image').val(); // Added Image Validation

        var hasError = false;

        if (name === '') { $('#addMovieModal #nameError').html('Movie name is required.'); hasError = true; }
        if (categoryValue === '') { $('#addMovieModal #categoryError').html('Please select at least one category.'); hasError = true; }
        if (!isDurationValid(durationInput)) { $('#addMovieModal #durationError').html('Please enter a valid duration.'); hasError = true; }
        if (language === '') { $('#addMovieModal #languageError').html('Language is required.'); hasError = true; }
        if (rating === '') { $('#addMovieModal #ratingError').html('Rating is required.'); hasError = true; }
        if (date === '') { $('#addMovieModal #dateError').html('Release date is required.'); hasError = true; }
        if (trailer === '') { $('#addMovieModal #trailerError').html('Trailer is required.'); hasError = true; }
        if (image === '') { $('#addMovieModal #imageError').html('Image is required.'); hasError = true; }

        if (hasError) return; 

        var formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $('#addMovieModal').modal('hide'); 
                notify({
                    type: "success",
                    title: 'Movie Created',
                    autoHide: true,
                    delay: 2500,
                    position: {x: "right", y: "top"},
                    icon: '<img src="{{ URL::asset('assets/images/correct.png')}}" />',
                    message: response.success,
                });
                setTimeout(function () {
                    location.reload();
                },1000);
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    showValidationErrors('#addMovieModal', xhr.responseJSON.errors);
                } else {
                    alert("An unexpected error occurred.");
                }
            }
        });
    });

    // Edit Movie Submit
    $("#editMovieForm").on("submit", function (e) {
        e.preventDefault();
        clearFormErrors('#editMovieModal');

        var name = $('#editMovieModal #name').val().trim();
        var categoryValue = $('#editMovieModal #category').val();
        var durationInput = $('#editMovieModal #duration');
        var language = $('#editMovieModal #language').val().trim();
        var rating = $('#editMovieModal #rating').val().trim();
        var date = $('#editMovieModal #date').val();
        var trailer = $('#editMovieModal #trailer').val().trim();
        // Image is optional on edit, so no frontend requirement check needed here

        var hasError = false;

        if (name === '') { $('#editMovieModal #nameError').html('Movie name is required.'); hasError = true; }
        if (categoryValue === '') { $('#editMovieModal #categoryError').html('Please select at least one category.'); hasError = true; }
        if (!isDurationValid(durationInput)) { $('#editMovieModal #durationError').html('Please enter a valid duration.'); hasError = true; }
        if (language === '') { $('#editMovieModal #languageError').html('Language is required.'); hasError = true; }
        if (rating === '') { $('#editMovieModal #ratingError').html('Rating is required.'); hasError = true; }
        if (date === '') { $('#editMovieModal #dateError').html('Release date is required.'); hasError = true; }
        if (trailer === '') { $('#editMovieModal #trailerError').html('Trailer is required.'); hasError = true; }

        if (hasError) return;

        var formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $('#editMovieModal').modal('hide');
                notify({
                    type: "success",
                    title: 'Movie UPDATED',
                    autoHide: true,
                    delay: 2500,
                    position: {x: "right", y: "top"},
                    icon: '<img src="{{ URL::asset('assets/images/correct.png')}}" />',
                    message: response.success,
                });
                setTimeout(function () {
                    location.reload();
                },1000);
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    showValidationErrors('#editMovieModal', xhr.responseJSON.errors);
                } else {
                    alert("An unexpected error occurred.");
                }
            }
        });
    });

    // Delete Movie Submit (AJAX Conversion)
    $(document).on("submit", ".delete-movie-form", function (e) {
        e.preventDefault();
        var form = $(this);

        if (confirm("Are you sure you want to delete this movie?")) {
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                success: function (response) {
                    notify({
                    type: "success",
                    title: 'Movie Deleted',
                    autoHide: true,
                    delay: 2500,
                    position: {x: "right", y: "top"},
                    icon: '<img src="{{ URL::asset('assets/images/correct.png')}}" />',
                    message: response.success,
                });
                setTimeout(function () {
                    location.reload();
                },500);
                },
                error: function (xhr) {

                    if (xhr.status === 400) {
                        swal('Cannot Delete', xhr.responseJSON.error, 'error'); 
                    } else {
                        swal('Error', 'An unexpected error occurred while deleting.', 'error');
                    }
                }
            });
        }
    });

</script>

@include('includes/footer_end')
