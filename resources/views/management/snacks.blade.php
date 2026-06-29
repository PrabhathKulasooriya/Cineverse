@include('includes/header_start')

<link href="{{ URL::asset('assets/plugins/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::asset('assets/plugins/datatables/buttons.bootstrap4.min.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::asset('assets/plugins/datatables/responsive.bootstrap4.min.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::asset('assets/plugins/sweet-alert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css">
<link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::asset('assets/css/custom_checkbox.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::asset('assets/css/jquery.notify.css')}}" rel="stylesheet" type="text/css">
<link href="{{ URL::asset('assets/css/mdb.css')}}" rel="stylesheet" type="text/css">
<meta name="csrf-token" content="{{ csrf_token() }}"/>

@include('includes/header_end')

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

<div class="page-content-wrapper">
    <div class="container-fluid">

        @if(session('success'))
            <div class="alert alert-success text-center position-absolute fade show" style="top: 20px; right: 20px; z-index: 1050; min-width: 350px;">
                <i class="fa fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        @endif

        c

        <div class="col-lg-12">
            <div class="card m-b-20">
                <div class="card-body">

                    <div class="row mb-3">
                        <div class="col-lg-8"></div>
                        @if(Auth::user()->user_role_iduser_role == 1 || Auth::user()->user_role_iduser_role == 2)
                        <div class="col-lg-4">
                            <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#addSnackModal">
                                Add Snack
                            </button>
                        </div>
                        @endif
                    </div>

                    <div class="table-rep-plugin">
                        <div class="table-responsive b-0" data-pattern="priority-columns">
                            <table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>NAME</th>
                                        <th>IMAGE</th>
                                        <th>SIZES, PRICES & AVAILABILITY</th>
                                        @if(Auth::user()->user_role_iduser_role == 1 || Auth::user()->user_role_iduser_role == 2)
                                        <th>OPTIONS</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($snacks as $snackName => $snackGroup)
                                    <tr>
                                        <td class="align-middle">{{ $snackName }}</td>

                                        <td class="align-middle">
                                            <img src="{{ URL::asset('snackImages/' . $snackGroup->first()->image) }}" alt="{{ $snackName }}" height="50">
                                        </td>

                                        <td class="align-middle">
                                            @foreach($snackGroup as $snackRow)
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="badge badge-secondary mr-3" style="font-size: 13px; min-width: 120px; text-align: left;">
                                                        {{ $snackRow->size }} — Rs.{{ number_format($snackRow->price, 2) }}
                                                    </span>
                                                    
                                                    @if(Auth::user()->user_role_iduser_role == 1 || Auth::user()->user_role_iduser_role == 2)
                                                    <div style="margin-top: 5px;">
                                                        <input type="checkbox"
                                                               onchange="toggleSnack('{{ $snackRow->idsnacks }}')"
                                                               id="s{{ $snackRow->idsnacks }}"
                                                               {{ $snackRow->available ? 'checked' : '' }}
                                                               switch="none"/>
                                                        <label for="s{{ $snackRow->idsnacks }}" data-on-label="On" data-off-label="Off" class="mb-0"></label>
                                                    </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </td>

                                        @if(Auth::user()->user_role_iduser_role == 1 || Auth::user()->user_role_iduser_role == 2)
                                        <td class="align-middle">
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-sm btn-warning edit-snack-btn mr-1"
                                                        data-toggle="modal"
                                                        data-target="#editSnackModal"
                                                        data-oldname="{{ $snackName }}"
                                                        data-image="{{ $snackGroup->first()->image }}"
                                                        data-variants="{{ json_encode($snackGroup) }}">
                                                    <i class="fa fa-edit"></i>
                                                </button>

                                                <form action="{{ route('destroySnack') }}" method="POST" class="m-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="snack_name" value="{{ $snackName }}">
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- ======================== ADD SNACK MODAL ======================== --}}
<div class="modal fade" id="addSnackModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mt-0">Add Snack</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <form action="{{ route('saveSnack') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>Snack Name <span style="color:red">*</span></label>
                        <input type="text" class="form-control" name="name" placeholder="e.g. Coke" required/>
                    </div>
                    <div class="form-group">
                        <label>Image <span style="color:red">*</span></label>
                        <input type="file" class="form-control" name="image" accept="image/*" required/>
                    </div>
                    <div class="form-group">
                        <label>Sizes & Prices <span style="color:red">*</span></label>
                        <div id="addSizeRows">
                            <div class="size-row d-flex mb-2" style="gap:8px;">
                                <input type="text" class="form-control" name="sizes[]" placeholder="Size (e.g. S / M / L)" required/>
                                <input type="number" class="form-control" name="prices[]" placeholder="Price" min="0" step="0.01" required/>
                                <button type="button" class="btn btn-danger remove-size-btn">−</button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-secondary btn-sm mt-1 add-row-btn" data-target="#addSizeRows">+ Add Size</button>
                    </div>
                    <button type="submit" class="btn btn-primary float-right">Save Snack</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ======================== EDIT SNACK MODAL ======================== --}}
<div class="modal fade" id="editSnackModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mt-0">Edit Snack</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <form action="{{ route('updateSnack') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="old_name" id="editSnackOldName">

                    <div class="form-group">
                        <label>Snack Name <span style="color:red">*</span></label>
                        <input type="text" class="form-control" name="name" id="editSnackName" required/>
                    </div>
                    <div class="form-group">
                        <label>Image <small>(leave blank to keep current)</small></label>
                        <div class="mb-1"><img id="editSnackCurrentImage" src="" height="50" alt="current image"/></div>
                        <input type="file" class="form-control" name="image" accept="image/*"/>
                    </div>
                    <div class="form-group">
                        <label>Sizes & Prices <span style="color:red">*</span></label>
                        <div id="editSizeRows"></div>
                        <button type="button" class="btn btn-secondary btn-sm mt-1 add-row-btn" data-target="#editSizeRows">+ Add Size</button>
                    </div>
                    <button type="submit" class="btn btn-primary float-right">Update Snack</button>
                </form>
            </div>
        </div>
    </div>
</div>

@include('includes/footer_start')

<script src="{{ URL::asset('assets/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/sweet-alert2/sweetalert2.min.js')}}"></script>
<script src="{{ URL::asset('assets/js/jquery.notify.min.js')}}"></script>

<script>
$(document).ready(function () {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
    setTimeout(() => $(".alert").fadeOut("slow", function() { $(this).remove(); }), 3000);
    $('#datatable').DataTable({ "order": [0, 'asc'], "columnDefs": [{ "orderable": false, "targets": [1, 2, 3] }] });


    const generateSizeRow = (id = '', size = '', price = '') => `
        <div class="size-row d-flex mb-2" style="gap:8px;">
            <input type="hidden" name="ids[]" value="${id}">
            <input type="text" class="form-control" name="sizes[]" value="${size}" placeholder="Size" required/>
            <input type="number" class="form-control" name="prices[]" value="${price}" placeholder="Price" min="0" step="0.01" required/>
            <button type="button" class="btn btn-danger remove-size-btn">−</button>
        </div>`;

  
    $('.add-row-btn').on('click', function () {
        $($(this).data('target')).append(generateSizeRow());
    });

    $(document).on('click', '.remove-size-btn', function () {
        if ($(this).closest('.form-group').find('.size-row').length > 1) {
            $(this).closest('.size-row').remove();
        }
    });

    
    $(document).on('click', '.edit-snack-btn', function () {
        $('#editSnackOldName').val($(this).data('oldname'));
        $('#editSnackName').val($(this).data('oldname'));
        $('#editSnackCurrentImage').attr('src', "{{ URL::asset('snackImages') }}/" + $(this).data('image'));
        
        $('#editSizeRows').empty();
        $(this).data('variants').forEach(v => {
            $('#editSizeRows').append(generateSizeRow(v.idsnacks, v.size, v.price));
        });
    });

    $('#addSnackModal').on('hidden.bs.modal', function () {
        $(this).find('input[type=text], input[type=number], input[type=file]').val('');
        $('#addSizeRows').html(generateSizeRow());
    });

    window.toggleSnack = id => $.post('{{ route("toggleSnackAvailable") }}', { id: id });
});
</script>

@include('includes/footer_end')