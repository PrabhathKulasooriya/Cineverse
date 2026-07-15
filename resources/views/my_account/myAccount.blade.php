@include('includes/header_start')

<link href="{{ URL::asset('assets/plugins/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::asset('assets/plugins/datatables/buttons.bootstrap4.min.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::asset('assets/plugins/datatables/responsive.bootstrap4.min.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::asset('assets/plugins/sweet-alert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css">
<link href="{{ URL::asset('assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css')}}" rel="stylesheet">
<link href="{{ URL::asset('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}" rel="stylesheet">
<link href="{{ URL::asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::asset('assets/plugins/bootstrap-touchspin/css/jquery.bootstrap-touchspin.min.css')}}" rel="stylesheet"/>
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
        <div class="col-lg-12">
            <div class="card m-b-20">
                <div class="card-body">

                    <div class="row">
                        <div class="col-lg-12" align="right">
                            <button class="btn btn-primary w-md waves-effect waves-light" type="button"
                                    data-toggle="modal" data-target="#changePasswordModal">
                                Change Password
                            </button>
                        </div>
                    </div>

                    <div align="center">
                        <img src="{{ URL::asset('assets/images/users/avatar-1.png')}}" height="90"/>
                    </div>

                    <div class="row">
                        <div class="col-lg-4"></div>
                        <div class="col-lg-4">
                            <h3 class="text-center">{{$users->first_name}} {{$users->last_name}}</h3>
                            @if ($users->user_role_iduser_role == 1)
                                <h5 class="text-center">ADMIN</h5>
                            @elseif ($users->user_role_iduser_role == 2)
                                <h5 class="text-center">EMPLOYEE</h5>
                            @endif
                            <br/>
                        </div>
                        <div class="col-lg-4"></div>
                    </div>

                    <div class="container-fluid">
                        <form action="updateUserDetails" method="POST" id="updateCustomerId">
                            @csrf
                            <input type="hidden" id="hiddenUserId" name="hiddenUserId" value="{{$users->idmaster_user}}"/>

                            <div class="row">
                                <div class="col-lg-6">

                                    <div class="form-group">
                                        <label>First Name</label>
                                        <input type="text" class="form-control" id="fName"
                                               autocomplete="off" name="fName"
                                               placeholder="First Name" readonly
                                               value="{{$users->first_name}}">
                                        <small class="text-danger" id="fNameError"></small>
                                    </div>

                                    <div class="form-group">
                                        <label>Contact No</label>
                                        <input type="number" class="form-control" id="contactNo"
                                               autocomplete="off" name="contactNo"
                                               placeholder="+(94) XX XXX XXXX" readonly
                                               value="{{$users->contact_number}}">
                                        <small class="text-danger" id="contactNoError"></small>
                                    </div>

                                </div>

                                <div class="col-lg-6">

                                    <div class="form-group">
                                        <label>Last Name</label>
                                        <input type="text" class="form-control" id="lName"
                                               autocomplete="off" name="lName"
                                               placeholder="Last Name" readonly
                                               value="{{$users->last_name}}">
                                        <small class="text-danger" id="lNameError"></small>
                                    </div>

                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" class="form-control" id="email"
                                               autocomplete="off" name="email"
                                               placeholder="Email" readonly
                                               value="{{$users->email}}"
                                               oninput="this.value = this.value.toLowerCase();">
                                        <small class="text-danger" id="emailError"></small>
                                    </div>

                                </div>
                            </div>

                            
                            <div class="row">
                                <div class="col-lg-12" align="right">
                                    <button class="btn btn-primary w-md waves-effect waves-light"
                                            id="updateBtn" type="button" onclick="enablefield()">
                                        Edit Profile
                                    </button>
                                    <button id="saveBtn" style="display: none"
                                            class="btn btn-success w-md waves-effect waves-light"
                                            type="submit">
                                        Save
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
</div>


<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mt-0">Change Password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <label>Current Password <span style="color:red">*</span></label>
                    <div class="input-group">
                        <input type="password" class="form-control" name="currentPassword"
                               id="currentPassword" required placeholder="Current Password"/>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary" id="toggleCurrentPassword">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                    <span class="text-danger" id="currentPasswordError"></span>
                </div>

                <div class="form-group">
                    <label>New Password <span style="color:red">*</span></label>
                    <div class="input-group">
                        <input type="password" class="form-control" name="newPassword"
                               id="newPassword" required placeholder="New Password"/>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary" id="toggleNewPassword">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                    <span class="text-danger" id="newPasswordError"></span>
                </div>

                <div class="form-group">
                    <label>Confirm Password <span style="color:red">*</span></label>
                    <div class="input-group">
                        <input type="password" class="form-control" name="confirmPassword"
                               id="confirmPassword" required placeholder="Confirm Password"/>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary" id="toggleConfirmPassword">
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                    <span class="text-danger" id="confirmPasswordError"></span>
                </div>

                <div class="form-group">
                    <button type="button" class="btn btn-primary float-right" onclick="changePassword()">
                        Save
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>


@include('includes/footer_start')

<script src="{{ URL::asset('assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/select2/js/select2.min.js')}}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js')}}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/plugins/bootstrap-filestyle/js/bootstrap-filestyle.min.js')}}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/plugins/bootstrap-touchspin/js/jquery.bootstrap-touchspin.min.js')}}" type="text/javascript"></script>
<script src="{{ URL::asset('assets/pages/form-advanced.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/dataTables.buttons.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/buttons.bootstrap4.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/jszip.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/pdfmake.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/vfs_fonts.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/buttons.html5.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/buttons.print.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/buttons.colVis.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/dataTables.responsive.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/datatables/responsive.bootstrap4.min.js')}}"></script>
<script src="{{ URL::asset('assets/plugins/sweet-alert2/sweetalert2.min.js')}}"></script>
<script src="{{ URL::asset('assets/pages/sweet-alert.init.js')}}"></script>
<script src="{{ URL::asset('assets/pages/datatables.init.js')}}"></script>
<script type="text/javascript" src="{{ URL::asset('assets/plugins/parsleyjs/parsley.min.js')}}"></script>
<script src="{{ URL::asset('assets/js/bootstrap-notify.js')}}"></script>
<script src="{{ URL::asset('assets/js/jquery.notify.min.js')}}"></script>

<script type="text/javascript">

    $(document).ready(function() {
            setTimeout(function() {
                $(".alert").fadeOut("slow", function() {
                    $(this).remove();
                });
            }, 3000); 
        });

    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        togglePasswordVisibility("toggleCurrentPassword", "currentPassword");
        togglePasswordVisibility("toggleNewPassword", "newPassword");
        togglePasswordVisibility("toggleConfirmPassword", "confirmPassword");

        $(document).on('focus', ':input', function () {
            $(this).attr('autocomplete', 'off');
        });
    });

    $(document).on("wheel", "input[type=number]", function (e) {
        $(this).blur();
    });


    // Toggle password visibility
    function togglePasswordVisibility(toggleButtonId, passwordFieldId) {
        var toggleButton = document.getElementById(toggleButtonId);
        var passwordInput = document.getElementById(passwordFieldId);
        if (toggleButton && passwordInput) {
            toggleButton.addEventListener("click", function () {
                if (passwordInput.type === "password") {
                    passwordInput.type = "text";
                    this.innerHTML = '<i class="fa fa-eye-slash" aria-hidden="true"></i>';
                } else {
                    passwordInput.type = "password";
                    this.innerHTML = '<i class="fa fa-eye" aria-hidden="true"></i>';
                }
            });
        }
    }


    // Enable editing
    function enablefield() {
        // Remove readonly from profile fields only (not hidden fields)
        $("#fName, #lName, #email, #contactNo").removeAttr('readonly');

        var profile = $("#hiddenUserId").val();

        $.post('getUserDetails', {profile: profile}, function (data) {
            $("#fName").val(data.first_name);
            $("#lName").val(data.last_name);
            $("#email").val(data.email);
            $("#contactNo").val(data.contact_number);
        });

        $("#saveBtn").show();
        $("#updateBtn").hide();
    }


    // Form submit
    $('#updateCustomerId').on('submit', function (event) {
        event.preventDefault();

        // Clear previous errors
        $("#fNameError, #lNameError, #contactNoError, #emailError").html('');

        $.ajax({
            type: 'POST',
            url: "{{ route('updateUserDetails') }}",
            data: new FormData(this),
            dataType: 'JSON',
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                if (data.errors != null) {
                    if (data.errors.fName) {
                        document.getElementById('fNameError').innerHTML = data.errors.fName[0];
                    }
                    if (data.errors.lName) {
                        document.getElementById('lNameError').innerHTML = data.errors.lName[0];
                    }
                    if (data.errors.contactNo) {
                        document.getElementById('contactNoError').innerHTML = data.errors.contactNo[0];
                    }
                    if (data.errors.email) {
                        document.getElementById('emailError').innerHTML = data.errors.email[0];
                    }
                }
                if (data.success != null) {
                    notify({
                        type: "success",
                        title: 'PROFILE UPDATED',
                        autoHide: true,
                        delay: 2500,
                        position: {x: "right", y: "top"},
                        icon: '<img src="{{ URL::asset('assets/images/correct.png') }}" />',
                        message: data.success,
                    });
                    if(data.isEmailChanged) {
                        setTimeout(function () {
                            window.location.href='{{ route('signin') }}';
                        }, 2000)
                    }else{
                       setTimeout(function () {
                        location.reload();
                    }, 1000); 
                    }
                    
                }
            }
        });
    });


    // Change Password
    function changePassword() {
        $("#currentPasswordError").html('');
        $("#newPasswordError").html('');
        $("#confirmPasswordError").html('');

        var currentPassword = $("#currentPassword").val();
        var newPassword = $("#newPassword").val();
        var confirmPassword = $("#confirmPassword").val();

        if (currentPassword.length === 0) {
            document.getElementById("currentPasswordError").innerHTML = "Current Password should be provided";
            return;
        }
        if (newPassword.length === 0) {
            document.getElementById("newPasswordError").innerHTML = "New Password should be provided";
            return;
        }
        if (confirmPassword.length === 0) {
            document.getElementById("confirmPasswordError").innerHTML = "Confirm Password should be provided";
            return;
        }
        if (newPassword !== confirmPassword) {
            document.getElementById("confirmPasswordError").innerHTML = "Confirm password is not matching";
            return;
        }

        $.post('changePassword', {
            currentPassword: currentPassword,
            newPassword: newPassword,
            confirmPassword: confirmPassword,
        }, function (data) {
            if (data.errors != null) {
                if (data.errors.newPassword) {
                    document.getElementById('newPasswordError').innerHTML = data.errors.newPassword[0];
                }
                if (data.errors.confirmPassword) {
                    document.getElementById('confirmPasswordError').innerHTML = data.errors.confirmPassword[0];
                }
                if (data.errors.currentPassword) {
                    document.getElementById('currentPasswordError').innerHTML = data.errors.currentPassword[0];
                }
            }
            if (data.success != null) {
                notify({
                    type: "success",
                    title: 'Password Changed',
                    autoHide: true,
                    delay: 2500,
                    position: {x: "right", y: "top"},
                    icon: '<img src="{{ URL::asset('assets/images/correct.png')}}" />',
                    message: data.success,
                });
                $('input').val('');
                setTimeout(function () {
                    $('#changePasswordModal').modal('hide');
                }, 200);
                setTimeout(function () {
                    location.reload();
                }, 1000);
            }
        });
    }


    // Reset modal on close
    $('#changePasswordModal').on('hidden.bs.modal', function () {
        $("#currentPassword, #newPassword, #confirmPassword").val('').attr('type', 'password');
        $("#currentPasswordError, #newPasswordError, #confirmPasswordError").html('');
        $("#toggleCurrentPassword i, #toggleNewPassword i, #toggleConfirmPassword i")
            .removeClass('fa-eye-slash').addClass('fa-eye');
    });

</script>

@include('includes/footer_end')