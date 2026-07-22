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
                        <div class="alert alert-success text-center position-fixed fade show" style="top: 100px; right: 20px; z-index: 1000; min-width: 350px;">
                            <i class="fa fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
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

                    <div class="row">
                        <div class="col-lg-8">
                        </div>
                        <div class="col-lg-4">
                            <button type="button" class="btn btn-primary float-right"
                                    data-toggle="modal" data-target="#addClientModal">
                                Register Client</button>
                        </div>
                    </div>

                    <br/>

                    <!--Data Table Start-->
                    <div class="table-rep-plugin">
                        <div class="table-responsive b-0" data-pattern="priority-columns">
                            <table id="datatable" class="table table-striped table-bordered"
                                   cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>USER ID</th>
                                    <th>NAME</th>
                                    <th>EMAIL</th>
                                    <th>CONTACT NUMBER</th>
                                    <th>STATUS</th>
                                    <th>OPTIONS</th>
                                </tr>
                                </thead>

                                <tbody>
                                @if(isset($userClients))
                                    @if(count($userClients)>0)
                                        @foreach($userClients as $userClient)
                                            <tr>
                                                <td>REG-{{$userClient->idmaster_user}}</td>
                                                <td>{{$userClient->first_name}} {{$userClient->last_name}}</td>
                                                <td>{{$userClient->email}}</td>
                                                <td>{{$userClient->contact_number}}</td>

                                                <!--Status Start-->
                                                @if($userClient->status == 1)
                                                    <td>
                                                        <p>
                                                            <input type="checkbox"
                                                                   onchange="adMethod('{{ $userClient->idmaster_user}}','master_user')"
                                                                   id="{{"c".$userClient->idmaster_user}}" checked
                                                                   switch="none"/>
                                                            <label for="{{"c".$userClient->idmaster_user}}"
                                                                   data-on-label="On"
                                                                   data-off-label="Off"></label>
                                                        </p>
                                                    </td>
                                                @else
                                                    <td>
                                                        <p>
                                                            <input type="checkbox"
                                                                   onchange="adMethod('{{ $userClient->idmaster_user}}','master_user')"
                                                                   id="{{"c".$userClient->idmaster_user}}"
                                                                   switch="none"/>
                                                            <label for="{{"c".$userClient->idmaster_user}}"
                                                                   data-on-label="On"
                                                                   data-off-label="Off"></label>
                                                        </p>
                                                    </td>
                                                @endif
                                                <!--Status End-->

                                                <!--Options Start-->
                                                <td>
                                                    <p>
                                                        <button type="button" title="View"
                                                                class="btn btn-sm btn-default waves-effect waves-light"
                                                                data-toggle="modal"
                                                                data-fname="{{ $userClient->first_name }}"
                                                                data-lname="{{ $userClient->last_name }}"
                                                                data-email="{{ $userClient->email }}"
                                                                data-contactno="{{ $userClient->contact_number }}"
                                                                id="viewClientID"
                                                                data-target="#viewClientModal">
                                                            <i class="fa fa-eye"></i>
                                                        </button>

                                                        <button type="button"
                                                                class="btn btn-sm btn-warning waves-effect waves-light"
                                                                data-toggle="modal"
                                                                data-id="{{ $userClient->idmaster_user }}"
                                                                data-fname="{{ $userClient->first_name }}"
                                                                data-lname="{{ $userClient->last_name }}"
                                                                data-email="{{ $userClient->email }}"
                                                                data-contactno="{{ $userClient->contact_number }}"
                                                                id="updateClientID"
                                                                data-target="#updateClientModal">
                                                            <i class="fa fa-edit"></i>
                                                        </button>
                                                    </p>
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

<!-- Add Client Modal Start-->
<div class="modal fade" id="addClientModal" tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mt-0">Register Client</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>First Name<span style="color: red">*</span></label>
                    <input type="text" class="form-control" id="fName" name="fName" placeholder="First Name">
                    <small class="text-danger" id="fNameError"></small>
                </div>

                <div class="form-group">
                    <label>Last Name<span style="color: red">*</span></label>
                    <input type="text" class="form-control" id="lName" name="lName" placeholder="Last Name">
                    <small class="text-danger" id="lNameError"></small>
                </div>

                <div class="form-group">
                    <label>Email<span style="color: red">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="example@email.com" oninput="this.value = this.value.toLowerCase();">
                    <small class="text-danger" id="emailError"></small>
                </div>

                <div class="form-group">
                    <label>Contact No<span style="color: red"> *</span></label>
                    <input type="text" class="form-control" id="contactNo" name="contactNo" placeholder="07X XXX XXXX" maxlength="10">
                    <small class="text-danger" id="contactNoError"></small>
                </div>

                <div class="form-group">
                    <label>Password<span style="color: red">*</span></label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password">
                    <small class="text-danger" id="passwordError"></small>
                </div>

                <div class="form-group">
                    <button type="button" class="btn btn-primary float-right" onclick="saveClient()">
                        Save Client
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Add Client Modal End-->

<!--View Client modal Start-->
<div class="modal fade" id="viewClientModal" tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mt-0">Client Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">First Name</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="viewFname" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Last Name</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="viewLname" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Email</label>
                        <div class="col-sm-8">
                            <input type="email" class="form-control" id="viewEmail" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Contact Number</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="viewContactNo" readonly>
                        </div>
                    </div>


                    <div class="form-group row">
                        <div class="col-sm-12">
                            <button type="button" class="btn btn-md btn-outline-primary waves-effect float-right" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--View Client modal End-->

<!-- Update Client Modal Start-->
<div class="modal fade" id="updateClientModal" tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mt-0">Update Client</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="hiddenUserId" name="hiddenUserId">
                
                <div class="form-group">
                    <label>First Name<span style="color: red">*</span></label>
                    <input type="text" class="form-control" name="updateFname" id="updateFname" 
                           required placeholder="First Name"/>
                    <span class="text-danger" id="updateFnameError"></span>
                </div>

                <div class="form-group">
                    <label>Last Name<span style="color: red">*</span></label>
                    <input type="text" class="form-control" name="updateLname" id="updateLname" 
                           required placeholder="Last Name"/>
                    <span class="text-danger" id="updateLnameError"></span>
                </div>

                <div class="form-group">
                    <label>Email<span style="color: red">*</span></label>
                    <input type="email" class="form-control" id="updateEmail" name="updateEmail" 
                           placeholder="example@email.com" oninput="this.value = this.value.toLowerCase();">
                    <small class="text-danger" id="updateEmailError"></small>
                </div>

                <div class="form-group">
                    <label>Contact Number<span style="color: red">*</span></label>
                    <input type="text" class="form-control" name="updateContactNo" id="updateContactNo" 
                           required placeholder="07X XXX XXXX" maxlength="10"/>
                    <span class="text-danger" id="updateContactNoError"></span>
                </div>

                

                <div class="form-group">
                    <button type="button" class="btn btn-primary float-right" onclick="updateClient()">
                        Update Client
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Update Client Modal End-->

@include('includes/footer_start')

<!-- Plugins js -->
<script src="{{ URL::asset('assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js')}}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js')}}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js')}}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/plugins/bootstrap-touchspin/js/jquery.bootstrap-touchspin.min.js')}}" type="text/javascript"></script>

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
    $(document).ready(function() {
        setTimeout(function() {
            $(".alert").fadeOut("slow", function() {
                $(this).remove();
            });
        }, 3000); 
    });
</script>

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
        var match = d.match(/(\d+)$/);
        return match ? parseInt(match[1], 10) : 0;
    };

    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('#datatable')) {
            $('#datatable').DataTable().destroy();
        }

        $('#datatable').DataTable({
            "order": [], // Disable initial sorting
            "columnDefs": [
                { "orderable": false, "targets": [2, 4, 5, 6] },
                {"type":"id-num","targets":0}
            ]
        });
    });

    $(document).on("wheel", "input[type=number]", function (e) {
        $(this).blur();
    });

    function adMethod(dataID, tableName) {
        $.post('activateDeactivate', {id: dataID, table: tableName}, function (data) {
            // Handle response if needed
        });
    }

    //Save Client Start
    function saveClient(){
        // Clear previous errors
        $("#fNameError").html('');
        $("#lNameError").html('');
        $("#contactNoError").html('');
        $("#emailError").html('');
        $("#passwordError").html('');

        var fName = $("#fName").val();
        var lName = $("#lName").val();
        var contactNo = $("#contactNo").val();
        var email = $("#email").val();
        var password = $("#password").val();

        $.post('saveClientByAdmin',{
            fName: fName,
            lName: lName,
            contactNo: contactNo,
            email: email,
            password: password
        }, function (data) {
            if (data.errors != null) {
                if(data.errors.fName) {
                    document.getElementById('fNameError').innerHTML = data.errors.fName[0];
                }
                if(data.errors.lName) {
                    document.getElementById('lNameError').innerHTML = data.errors.lName[0];
                }
                if(data.errors.contactNo) {
                    document.getElementById('contactNoError').innerHTML = data.errors.contactNo[0];
                }
                if(data.errors.email) {
                    document.getElementById('emailError').innerHTML = data.errors.email[0];
                }
                if(data.errors.password) {
                    document.getElementById('passwordError').innerHTML = data.errors.password[0];
                }
            }

            if (data.success != null) {
                notify({
                    type: "success",
                    title: 'CLIENT SAVED',
                    autoHide: true,
                    delay: 2500,
                    position: {
                        x: "right",
                        y: "top"
                    },
                    icon: '<img src="{{ URL::asset('assets/images/correct.png')}}" />',
                    message: data.success,
                });
                
                // Clear form
                $('#addClientModal input').val('');
                $('#addClientModal select').val('');
                
                setTimeout(function () {
                    $('#addClientModal').modal('hide');
                }, 200);
                
                location.reload();
            }
        });
    }

    //View Client Details Start
    $(document).on('click', '#viewClientID', function () {
        var firstName = $(this).data("fname");
        var lastName = $(this).data("lname");
        var contactNo = $(this).data("contactno");
        var email = $(this).data("email");

        $("#viewFname").val(firstName);
        $("#viewLname").val(lastName);
        $("#viewContactNo").val(contactNo);
        $("#viewEmail").val(email);
    });

    //Update Client Start
    $(document).on('click', '#updateClientID', function () {
        var userId = $(this).data("id");
        var firstName = $(this).data("fname");
        var lastName = $(this).data("lname");
        var contactNo = $(this).data("contactno");
        var email = $(this).data("email");

        $("#hiddenUserId").val(userId);
        $("#updateFname").val(firstName);
        $("#updateLname").val(lastName);
        $("#updateEmail").val(email);
        $("#updateContactNo").val(contactNo);

    });

    function updateClient() {
        // Clear previous errors
        $('#updateFnameError').html('');
        $("#updateLnameError").html('');
        $("#updateContactNoError").html('');
        $("#updateEmailError").html('');

        var hiddenUserId = $("#hiddenUserId").val();
        var firstName = $("#updateFname").val();
        var lastName = $("#updateLname").val();
        var email = $("#updateEmail").val();
        var contactNo = $("#updateContactNo").val();

        // Basic client-side validation
        if (!hiddenUserId) {
            alert('User ID is missing!');
            return;
        }

        $.post('updateClient', {
            hiddenUserId: hiddenUserId,
            firstName: firstName,
            lastName: lastName,
            email: email,
            contactNo: contactNo,
        }, function (data) {
            if (data.errors != null) {
                if(data.errors.firstName) {
                    document.getElementById('updateFnameError').innerHTML = data.errors.firstName[0];
                }
                if(data.errors.lastName) {
                    document.getElementById('updateLnameError').innerHTML = data.errors.lastName[0];
                }
                if(data.errors.email) {
                    document.getElementById('updateEmailError').innerHTML = data.errors.email[0];
                }
                if(data.errors.contactNo) {
                    document.getElementById('updateContactNoError').innerHTML = data.errors.contactNo[0];
                }
                if(data.errors.general) {
                    alert(data.errors.general[0]);
                }
            }

            if(data.success != null) {
                notify({
                    type: "success",
                    title: 'CLIENT UPDATED',
                    autoHide: true,
                    delay: 2500,
                    position: {
                        x: "right",
                        y: "top"
                    },
                    icon: '<img src="{{ URL::asset('assets/images/correct.png')}}" />',
                    message: data.success,
                });
                
                // Clear form inputs
                $('#updateClientModal input').val('');
                $('#updateClientModal select').val('');
                
                setTimeout(function () {
                    $('#updateClientModal').modal('hide');
                }, 200);

                setTimeout(function () {
                    location.reload();
                }, 1000);
            }
        })
        .fail(function(xhr, status, error) {
            console.log('Error:', error);
            alert('An error occurred while updating the client. Please try again.');
        });
    }

    //Hide Validation errors after closing the modal
    $('.modal').on('hidden.bs.modal', function () {
        // Clear Add Client Modal errors
        $("#fNameError").html('');
        $("#lNameError").html('');
        $("#contactNoError").html('');
        $("#emailError").html('');
        $("#passwordError").html('');

        // Clear Update Client Modal errors
        $('#updateFnameError').html('');
        $("#updateLnameError").html('');
        $("#updateContactNoError").html('');
        $("#updateEmailError").html('');

        // Clear input values
        $('input').val('');
        $('select').val('');
    });
</script>

@include('includes/footer_end')
