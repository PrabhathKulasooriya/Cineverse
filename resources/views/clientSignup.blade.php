@include('includes.header_account')



<!-- Begin page -->

<!--Background Image-->
<div class="accountbg"> </div>


<div class="wrapper-page signup-page">

    <div class="card signup-card" style="background-color: rgba(245, 245, 245, 0.8);">
        <div class="card-body">
            <div class="closebutton-container">
            <a href="{{route('home')}}"><i class='fa fa-times fa-2x close-button' aria-hidden="true"></i></a>
            </div>
            <div class="p-3">
                <h4 class="header-text m-b-5 text-center">Sign Up</h4>

                <form class="form-horizontal m-t-30" enctype="multipart/form-data" id="saveUser">
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
                                <label>Contact No<span style="color: red">*</span></label>
                                <input type="number" class="form-control" id="contactNo" autocomplete="off" name="contactNo" placeholder="07X XXX XXXX" maxlength="10">
                                <small class="text-danger" id="contactNoError"></small>
                            </div>

                            <div class="form-group">
                                <label>Date of Birth<span style="color: red">*</span></label>
                                <input type="date" class="form-control" id="date" autocomplete="off" name="date" placeholder="Date of Birth" min= "1900-01-01" max="<?= date('Y-m-d') ?>" >
                                <small class="text-danger" id="dateError"></small>
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
                                <label for="password">Password<span style="color: red">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" autocomplete="off" name="password" placeholder="Enter password">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="togglePassword">
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                    </button>
                                </div>
                                <small class="text-danger" id="passwordError"></small>
                            </div>


                            <div class="form-group">
                                <label>Gender<span style="color: red">*</span></label>
                                <select class="form-control" name="gender" id="gender" required>
                                    <option> Male </option>
                                    <option> Female </option>
                                </select>
                                <small class="text-danger" id="genderError"></small>
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

<script src="{{ URL::asset('assets/js/jquery.notify.min.js')}}"></script>

<script>
    document.getElementById("togglePassword").addEventListener("click", function () {
        var passwordInput = document.getElementById("password");
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            this.innerHTML = '<i class="fa fa-eye-slash" aria-hidden="true"></i>';
        } else {
            passwordInput.type = "password";
            this.innerHTML = '<i class="fa fa-eye" aria-hidden="true"></i>';
        }
    });

    document.getElementById('contactNo').addEventListener('input', function(e) {
    let value = e.target.value.replace(/[^0-9]/g, ''); 

    if (value.length > 10) {
        value = value.substring(0, 10);
    }

    e.target.value = value;
});
</script>

<script type="text/javascript">

    $(document).ready(function () {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

    });
    $(document).on("wheel", "input[type=number]", function (e) {
        $(this).blur();
    });





    //Sign Up
    $("#saveUser").on("submit", function (event) {

            $("#fNameError").html('');
            $("#lNameError").html('');
            $("#contactNoError").html('');
            $("#genderError").html('');
            $("#dateError").html('');
            $("#emailError").html('');
            $("#passwordError").html('');


            event.preventDefault();

            $.ajax({
                url: '{{route('saveClient')}}',
                type: 'POST',
                data: $(this).serialize(),
                success: function (data) {

                 if (data.errors != null) {

                        if(data.errors.fName) {
                            var p = document.getElementById('fNameError');
                            p.innerHTML = data.errors.fName[0];
                        }

                         if(data.errors.lName) {
                             var p = document.getElementById('lNameError');
                             p.innerHTML = data.errors.lName[0];
                         }

                        if(data.errors.contactNo) {
                            var p = document.getElementById('contactNoError');
                            p.innerHTML = data.errors.contactNo[0];
                        }

                        if(data.errors.gender) {
                            var p = document.getElementById('genderError');
                            p.innerHTML = data.errors.gender[0];
                        }

                        if(data.errors.date) {
                            var p = document.getElementById('dateError');
                            p.innerHTML = data.errors.date[0];
                        }

                        if(data.errors.email) {
                            var p = document.getElementById('emailError');
                            p.innerHTML = data.errors.email[0];
                        }

                         if(data.errors.password) {
                             var p = document.getElementById('passwordError');
                             p.innerHTML = data.errors.password[0];
                         }



                    }



                    if (data.success != null) {


                        notify({
                            type: "success", //alert | success | error | warning | info
                            title: 'Registration Success',
                            autoHide: true, //true | false
                            delay: 300, //number ms
                            position: {
                                x: "right",
                                y: "top"
                            },
                            message: data.success,
                        });

                        setTimeout(function () {
                            window.location.href = "signin"; 

                        }, 200);


                    }



                }
            });
        }
    );

</script>

