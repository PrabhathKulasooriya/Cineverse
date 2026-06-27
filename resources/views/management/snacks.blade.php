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
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger text-center position-absolute fade show" style="top: 20px; right: 20px; z-index: 1050; min-width: 350px;">
                            <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
        <div class="col-lg-12">
            <div class="card m-b-20">
                <div class="card-body">

                    <div class="row">
                        <div class="col-lg-8"></div>
                        @if(Auth::user()->user_role_iduser_role == 1 || Auth::user()->user_role_iduser_role == 2)
                        <div class="col-lg-4">
                            <button type="button" class="btn btn-primary float-right"
                                    data-toggle="modal" data-target="#addSnackModal">
                                Add Snack
                            </button>
                        </div>
                        @endif
                    </div>

                    

                    <br/>

                    <div class="table-rep-plugin">
                        <div class="table-responsive b-0" data-pattern="priority-columns">
                            <table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>NAME</th>
                                        <th>IMAGE</th>
                                        <th>SIZES & PRICES</th>
                                        @if(Auth::user()->user_role_iduser_role == 1 || Auth::user()->user_role_iduser_role == 2)
                                        <th>AVAILABLE</th>
                                        <th>OPTIONS</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($snacks as $snack)
                                    <tr>
                                        <td>{{ $snack->name }}</td>

                                        <td>
                                            <img src="{{ URL::asset('snackImages/' . $snack->image) }}"
                                                 alt="{{ $snack->name }}" height="50">
                                        </td>

                                        <td>
                                            @foreach($snack->variants as $variant)
                                                <span class="badge badge-secondary">
                                                    {{ $variant->size }} — Rs.{{ number_format($variant->price, 2) }}
                                                </span>
                                            @endforeach
                                        </td>

                                        @if(Auth::user()->user_role_iduser_role == 1 || Auth::user()->user_role_iduser_role == 2)
                                        <td>
                                            <p>
                                                <input type="checkbox"
                                                       onchange="toggleSnack('{{ $snack->idsnacks }}')"
                                                       id="s{{ $snack->idsnacks }}"
                                                       {{ $snack->available ? 'checked' : '' }}
                                                       switch="none"/>
                                                <label for="s{{ $snack->idsnacks }}"
                                                       data-on-label="On"
                                                       data-off-label="Off"></label>
                                            </p>
                                        </td>

                                        <td style="display:flex; flex-direction:row; gap:5px;">
                                            <button class="btn btn-sm btn-warning edit-snack-btn"
                                                    data-toggle="modal"
                                                    data-target="#editSnackModal"
                                                    data-id="{{ $snack->idsnacks }}"
                                                    data-name="{{ $snack->name }}"
                                                    data-image="{{ $snack->image }}"
                                                    data-variants="{{ $snack->variants->toJson() }}">
                                                <i class="fa fa-edit"></i>
                                            </button>

                                            <form action="{{ route('destroySnack', ['snack_id' => $snack->idsnacks]) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
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
                <form action="{{ route('saveSnack') }}" method="POST" enctype="multipart/form-data" id="addSnackForm">
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
                            {{-- first row --}}
                            <div class="size-row d-flex mb-2" style="gap:8px;">
                                <input type="text" class="form-control" name="sizes[0][size_label]" placeholder="e.g. S / M / L / Regular" required/>
                                <input type="number" class="form-control" name="sizes[0][price]" placeholder="Price" min="0" step="0.01" required/>
                                <button type="button" class="btn btn-danger remove-size-btn">−</button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-secondary btn-sm mt-1" id="addSizeRowBtn">+ Add Size</button>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary float-right">Save Snack</button>
                    </div>
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
                <form action="{{ route('updateSnack') }}" method="POST" enctype="multipart/form-data" id="editSnackForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="snack_id" id="editSnackId">

                    <div class="form-group">
                        <label>Snack Name <span style="color:red">*</span></label>
                        <input type="text" class="form-control" name="name" id="editSnackName" required/>
                    </div>

                    <div class="form-group">
                        <label>Image <small>(leave blank to keep current)</small></label>
                        <div class="mb-1">
                            <img id="editSnackCurrentImage" src="" height="50" alt="current image"/>
                        </div>
                        <input type="file" class="form-control" name="image" accept="image/*"/>
                    </div>

                    <div class="form-group">
                        <label>Sizes & Prices <span style="color:red">*</span></label>
                        <div id="editSizeRows"></div>
                        <button type="button" class="btn btn-secondary btn-sm mt-1" id="editSizeRowBtn">+ Add Size</button>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary float-right">Update Snack</button>
                    </div>
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
    $(document).ready(function() {
        setTimeout(function() {
            $(".alert").fadeOut("slow", function() {
                $(this).remove();
            });
        }, 3000); 
    });
</script>

<script>
$(document).ready(function () {

    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // Datatable
    $('#datatable').DataTable({
        "order": [0, 'asc'],
        "columnDefs": [
            { "orderable": false, "targets": [1, 2, 3, 4] }
        ]
    });

    // ── Add size row (Add modal) ──────────────────────────────
    let addRowIndex = 1;
    $('#addSizeRowBtn').on('click', function () {
        $('#addSizeRows').append(`
            <div class="size-row d-flex mb-2" style="gap:8px;">
                <input type="text"   class="form-control" name="sizes[${addRowIndex}][size_label]" placeholder="e.g. S / M / L / Regular" required/>
                <input type="number" class="form-control" name="sizes[${addRowIndex}][price]" placeholder="Price" min="0" step="0.01" required/>
                <button type="button" class="btn btn-danger remove-size-btn">−</button>
            </div>
        `);
        addRowIndex++;
    });

    // ── Remove size row ───────────────────────────────────────
    $(document).on('click', '.remove-size-btn', function () {
        // keep at least one row
        const container = $(this).closest('#addSizeRows, #editSizeRows');
        if (container.find('.size-row').length > 1) {
            $(this).closest('.size-row').remove();
        }
    });

    // ── Populate Edit modal ───────────────────────────────────
    const imageBase = "{{ URL::asset('snackImages') }}";

    $(document).on('click', '.edit-snack-btn', function () {
        const id       = $(this).data('id');
        const name     = $(this).data('name');
        const image    = $(this).data('image');
        const variants = $(this).data('variants'); // already parsed by jQuery

        $('#editSnackId').val(id);
        $('#editSnackName').val(name);
        $('#editSnackCurrentImage').attr('src', imageBase + '/' + image);

        // Build size rows
        let editRowIndex = 0;
        let html = '';
        variants.forEach(function (v) {
            html += `
                <div class="size-row d-flex mb-2" style="gap:8px;">
                    <input type="text"   class="form-control" name="sizes[${editRowIndex}][size_label]" value="${v.size}" required/>
                    <input type="number" class="form-control" name="sizes[${editRowIndex}][price]" value="${v.price}" min="0" step="0.01" required/>
                    <button type="button" class="btn btn-danger remove-size-btn">−</button>
                </div>`;
            editRowIndex++;
        });
        $('#editSizeRows').html(html);

        // Store next index for "add size" in edit modal
        $('#editSizeRowBtn').data('index', editRowIndex);
    });

    // ── Add size row (Edit modal) ─────────────────────────────
    $('#editSizeRowBtn').on('click', function () {
        let idx = $(this).data('index') || 0;
        $('#editSizeRows').append(`
            <div class="size-row d-flex mb-2" style="gap:8px;">
                <input type="text"   class="form-control" name="sizes[${idx}][size_label]" placeholder="e.g. S / M / L / Regular" required/>
                <input type="number" class="form-control" name="sizes[${idx}][price]" placeholder="Price" min="0" step="0.01" required/>
                <button type="button" class="btn btn-danger remove-size-btn">−</button>
            </div>
        `);
        $(this).data('index', idx + 1);
    });

    // ── Toggle available ──────────────────────────────────────
    window.toggleSnack = function(id) {
        $.post('{{ route("toggleSnackAvailable") }}', { id: id });
    };

    // ── Clear add modal on close ──────────────────────────────
    $('#addSnackModal').on('hidden.bs.modal', function () {
        $(this).find('input[type=text], input[type=number], input[type=file]').val('');
        // reset to one empty size row
        $('#addSizeRows').html(`
            <div class="size-row d-flex mb-2" style="gap:8px;">
                <input type="text"   class="form-control" name="sizes[0][size_label]" placeholder="e.g. S / M / L / Regular" required/>
                <input type="number" class="form-control" name="sizes[0][price]"      placeholder="Price" min="0" step="0.01" required/>
                <button type="button" class="btn btn-danger remove-size-btn">−</button>
            </div>
        `);
        addRowIndex = 1;
    });
});
</script>

@include('includes/footer_end')