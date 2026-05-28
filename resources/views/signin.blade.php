@include('includes.header_account')



<!-- Begin page -->

<!--Background Image-->
<div class="accountbg" style="background-image:url('assets/images/background.jpg')"> </div>

<!--Background Color-->
{{-- <div class="accountbg" style="background: linear-gradient(to bottom, #B22222 0%, #D04A4A 50%, #E57373 100%);"> </div> --}}



<div class="wrapper-page signin-page" >

                 @if(session('success'))
                    <div class="alert alert-success text-center ticket-success-container alert-dismissible" id="ticket-success-container">
                        <i class="fa fa-check-circle" aria-hidden="true"></i> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif           

                @if(\Session::has('error'))
                    <div class="alert alert-danger alert-dismissible ">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <p>{{ \Session::get('error') }}</p>
                    </div>
                @endif

                @if(\Session::has('warning'))
                    <div class="alert alert-warning alert-dismissible ">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <p>{{ \Session::get('warning') }}</p>
                    </div>
                @endif

    <div class="card" style="background-color: rgba(245, 245, 245, 0.8);">
        <div class="card-body">

            <h3 class="text-center m-0">
                <a  href="{{route('home')}}" ><img src="assets/images/logo/logo_3.png" height="90" alt="logo"></a>
            </h3>

            <div class="p-3">
                <h4 class="text-muted font-22 m-b-5 text-center">Welcome Back !</h4>
                <p class="text-muted font-16 text-center">Sign in to continue</p>

                   


                <form class="form-horizontal m-t-30" action="{{ route('login') }}" method="POST">

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email"
                               placeholder="Enter email" oninput="this.value = this.value.toLowerCase();">
                        <small class="text-danger">{{ $errors->first('email') }}</small>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter password">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="togglePassword">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                        </button>
                        </div>
                        <small class="text-danger">{{ $errors->first('password') }}</small>
                    </div>
                    <input type="hidden" name="_token" value="{{ Session::token() }}">

                    <div class="form-group row m-t-20">
                        <div class="col-sm-6">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" name="rememberme" id="customControlInline">
                                <label class="custom-control-label" for="customControlInline">Remember me</label>
                            </div>
                        </div>

                        <div class="col-sm-6 text-right">
                            <button class="btn btn-primary w-md waves-effect waves-light" type="submit">Sign In</button>
                        </div>
                    </div>

                </form>
            </div>

        </div>
    </div>

    <div class="m-t-40 text-center">
        <p class="text-white font-14">Don't have an account ?
            <a href="{{route('clientSignup')}}" class="font-500 font-16 font-secondary bottom-link"> Sign Up </a>
        </p>


    </div>

</div>

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
</script>


@include('includes.footer_account')
