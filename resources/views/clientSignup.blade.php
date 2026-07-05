@include('includes.header_account')



<!-- Begin page -->

<!--Background Image-->
<div class="accountbg"> </div>


<div class="wrapper-page signup-page">

                @if(session('success'))
                        <div class="alert alert-success text-center position-absolute fade show" style="top: 20px; right: 20px; z-index: 1050; min-width: 350px;">
                            <i class="fa fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                @endif          

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible position-absolute fade show" style="top: 20px; right: 20px; z-index: 1050; min-width: 350px;">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <p>{{ session('error') }}</p>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible position-absolute fade show" style="top: 20px; right: 20px; z-index: 1050; min-width: 350px;">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <p>{{ session('warning') }}</p>
                    </div>
                @endif

    <div class="card signup-card" style="background-color: rgba(245, 245, 245, 0.8);">
        
        <div class="card-body">
            <div class="closebutton-container">
            <a href="{{route('home')}}"><i class='fa fa-times fa-2x close-button' aria-hidden="true"></i></a>
            </div>
            <div class="p-3">
                <h4 class="header-text m-b-5 text-center">Sign Up</h4>

                <form class="form-horizontal m-t-30" enctype="multipart/form-data" id="saveUser" action="{{ route('saveClient') }}" method="POST">
                    {{csrf_field()}}

                    <div class="row justify-content-center">
                        <!-- Left Column -->
                        <div class="col-md-6 col-lg-5">
                            <div class="form-group">
                                <label>First Name<span style="color: red">*</span></label>
                                <input type="text" class="form-control" id="fName" autocomplete="off" name="fName" placeholder="First Name">
                                <small class="text-danger" id="fNameError"></small>
                            </div>

                            <div class="form-group">
                                <label for="email">Email<span style="color: red">*</span></label>
                                <input type="email" class="form-control" id="email" autocomplete="off" name="email" placeholder="example@email.com" oninput="this.value = this.value.toLowerCase();">
                                <small class="text-danger" id="emailError"></small>
                            </div>

                            <div class="form-group">
                                <label for="password">Password<span style="color: red">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" autocomplete="off" name="password" placeholder="Enter password">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="togglePassword">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                    </button>
                                </div>
                                <small class="text-danger" id="passwordError"></small>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6 col-lg-5">
                            <div class="form-group">
                                <label>Last Name<span style="color: red">*</span></label>
                                <input type="text" class="form-control" id="lName" autocomplete="off" name="lName" placeholder="Last Name">
                                <small class="text-danger" id="lNameError"></small>
                            </div>

                            <div class="form-group">
                                <label>Contact No<span style="color: red">*</span></label>
                                <input type="number" class="form-control" id="contactNo" autocomplete="off" name="contactNo" placeholder="07X XXX XXXX" maxlength="10">
                                <small class="text-danger" id="contactNoError"></small>
                            </div>

                            <div class="form-group">
                                <label for="confirmPassword">Confirm Password<span style="color: red">*</span></label>
                                <div class="input-group">
                                    {{-- ✅ Unique id and NO name — so it's never sent to the server --}}
                                    <input type="password" class="form-control" id="confirmPassword" autocomplete="off" placeholder="Confirm password">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="toggleConfirmPassword">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                    </button>
                                </div>
                                <small class="text-danger" id="confirmPasswordError"></small>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="form-group row m-t-20">
                        <div class="col-lg text-right">
                            <button class="btn btn-primary waves-effect waves-light" type="submit">Register</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div class="m-t-40 text-center">
        <p class="text-white font-14">Already have an account ?
            <a href="{{route('signin')}}" class="font-500 font-16 font-secondary bottom-link"> Sign In </a>
        </p>
    </div>

</div>




@include('includes.footer_account')

<script src="{{ URL::asset('assets/js/jquery.notify.min.js') }}"></script>

<script>

    setTimeout(() => $(".alert").fadeOut("slow", function() { $(this).remove(); }), 5000);

    $(document).ready(function () {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });
    });

    // Toggle for Password
    document.getElementById("togglePassword").addEventListener("click", function () {
        var input = document.getElementById("password");
        if (input.type === "password") {
            input.type = "text";
            this.innerHTML = '<i class="fa fa-eye-slash" aria-hidden="true"></i>';
        } else {
            input.type = "password";
            this.innerHTML = '<i class="fa fa-eye" aria-hidden="true"></i>';
        }
    });

    // Toggle for Confirm Password (separate id)
    document.getElementById("toggleConfirmPassword").addEventListener("click", function () {
        var input = document.getElementById("confirmPassword");
        if (input.type === "password") {
            input.type = "text";
            this.innerHTML = '<i class="fa fa-eye-slash" aria-hidden="true"></i>';
        } else {
            input.type = "password";
            this.innerHTML = '<i class="fa fa-eye" aria-hidden="true"></i>';
        }
    });

    // Contact No — digits only, max 10
    document.getElementById('contactNo').addEventListener('input', function (e) {
        let value = e.target.value.replace(/[^0-9]/g, '');
        if (value.length > 10) value = value.substring(0, 10);
        e.target.value = value;
    });

    

    $(document).on("wheel", "input[type=number]", function (e) {
        $(this).blur();
    });

    setTimeout(() => $(".alert").fadeOut("slow", function() { $(this).remove(); }), 3000);

    $("#saveUser").on("submit", function (e) {
        e.preventDefault();

        // Clear all errors
        $("#fNameError").html('');
        $("#lNameError").html('');
        $("#contactNoError").html('');
        $("#emailError").html('');
        $("#passwordError").html('');
        $("#confirmPasswordError").html('');

        var password        = $("#password").val();
        var confirmPassword = $("#confirmPassword").val();
        var fName           = $("#fName").val();
        var lName           = $("#lName").val();
        var contactNo       = $("#contactNo").val();
        var email           = $("#email").val();

        if (fName === '') {
            $("#fNameError").html('First name is required.');
            return; 
        }

        if (lName === '') {
            $("#lNameError").html('Last name is required.');
            return; 
        }

        if (!contactNo) {
            $('#contactNoError').html('Contact No is required.');
            hasError = true;
        } else {
            var phonePattern = /^07\d{8}$/;
            if (!phonePattern.test(contactNo)) {
                $('#contactNoError').html('Enter a valid 10-digit phone number (e.g., 07XXXXXXXX).');
                hasError = true;
            }
        }

        if (email === '') {
            $("#emailError").html('Email is required.');
            return; 
        }

        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            $('#emailError').html('Please enter a valid email address.');
            return;
        }

        if (password === '') {
            $("#passwordError").html('Password is required.');
            return; 
        }

        if (confirmPassword === '') {
            $("#confirmPasswordError").html('Please confirm your password.');
            return; 
        }

        if (password !== confirmPassword) {
            $("#confirmPasswordError").html('Passwords do not match!');
            return; 
        }

        this.submit();
    });
</script>

