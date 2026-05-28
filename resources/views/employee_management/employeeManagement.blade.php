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
                                    data-toggle="modal"  data-target="#addUserModal" >
                               Add Employee</button>
                        </div>
                    </div>




                    <br/>





                    <div class="table-rep-plugin">
                        <div class="table-responsive b-0" data-pattern="priority-columns">


                            <table id="datatable"   class="table table-striped table-bordered"
                                   cellspacing="0"
                                   width="100%">
                                <thead>
                                <tr>
                                    <th>USER ID</th>
                                    <th>USER ROLE</th>
                                    <th>NAME</th>
                                    <th>CONTACT NUMBER</th>
                                    <th>GENDER</th>
                                    <th>STATUS</th>
                                    <th>OPTIONS</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(isset($users))
                                    @if(count($users)>0)
                                        @foreach($users as $user)
                                            <tr>
                                                <td>REG-{{$user->idmaster_user}}</td>
                                                <td>{{$user->UserRole->role_name}}</td>
                                                <td>{{$user->first_name}} {{$user->last_name}}</td>
                                                <td>{{$user->contact_number}}</td>
                                                <td>{{$user->gender}}</td>

                                                <!--Status Start-->
                                                @if($user->status == 1)
                                                    <td>
                                                        <p>
                                                            <input type="checkbox"
                                                                   onchange="adMethod('{{ $user->idmaster_user}}','master_user')"
                                                                   id="{{"c".$user->idmaster_user}}" checked
                                                                   switch="none"/>
                                                            <label for="{{"c".$user->idmaster_user}}"
                                                                   data-on-label="On"
                                                                   data-off-label="Off"></label>
                                                        </p>
                                                    </td>


                                                @else
                                                    <td>
                                                        <p>
                                                            <input type="checkbox"
                                                                   onchange="adMethod('{{ $user->idmaster_user}}','master_user')"
                                                                   id="{{"c".$user->idmaster_user}}"
                                                                   switch="none"/>
                                                            <label for="{{"c".$user->idmaster_user}}"
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
                                                                class="btn btn-sm btn-default  waves-effect waves-light"
                                                                data-toggle="modal"

                                                                data-fname="{{ $user->first_name }}"
                                                                data-lname="{{ $user->last_name }}"
                                                                data-contactno="{{ $user->contact_number }}"
                                                                data-gender="{{ $user->gender }}"
                                                                data-dob="{{ $user->dob }}"
                                                                data-email="{{ $user->email }}"

                                                                id="viewUserID"
                                                                data-target="#viewUserModal">
                                                            <i class="fa fa-eye"></i>
                                                        </button>





                                                        <!--Update Button Start-->
                                                        <button type="button"
                                                                class="btn btn-sm btn-warning  waves-effect waves-light"
                                                                data-toggle="modal"

                                                                data-id="{{ $user->idmaster_user }}"
                                                                data-fname="{{ $user->first_name }}"
                                                                data-lname="{{ $user->last_name }}"
                                                                data-role="{{$user->user_role_iduser_role}}"
                                                                data-contactno="{{ $user->contact_number }}"
                                                                data-dob="{{ $user->dob }}"
                                                                data-email="{{ $user->email }}"
                                                                data-gender="{{ $user->gender }}"

                                                                id="updateUserID"
                                                                data-target="#updateUserModal"><i
                                                                    class="fa fa-edit"></i>
                                                        </button>
                                                        <!--Update Button End-->








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

                </div>
            </div>
        </div>
    </div> <!-- container -->

</div> <!-- Page content Wrapper -->

</div> <!-- content -->





<!-- Start Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1"
     role="dialog"
     aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title mt-0">Add User</h5>
                <button type="button" class="close" data-dismiss="modal"
                        aria-hidden="true">×
                </button>
            </div>

            <div class="modal-body">

              <div class="container-fluid">

                  <div class="row">

                    <div class="col-lg-6">

                        <div class="form-group">
                            <label>User Type<span style="color: red">*</span></label>
                            <select class="form-control" name="userType" id="userType" required>
                                <option disabled value="" selected> Select User Role</option>
                                <option value="1">System Admin</option>
                                <option value="2"> Management Employee </option>
                                <option value="3"> Ticket Counter Employee </option>
                            </select>
                            <small class="text-danger" id="userTypeError"></small>
                        </div>



                           <div class="form-group">
                                <label>First Name<span style="color: red">*</span></label>
                                <input type="text" class="form-control" id="fName"
                                       name="fName" placeholder="First Name">
                                <small class="text-danger" id="fNameError"></small>
                           </div>


                            <div class="form-group">
                                <label> Last Name <span style="color: red">*</span> </label>
                                <input type="text" class="form-control" id="lName"
                                       name="lName" placeholder="Last Name">
                                <small class="text-danger" id="lNameError"></small>
                            </div>


                            <div class="form-group">
                                <label>Email<span style="color: red">*</span></label>
                                <input type="email" class="form-control" id="email"
                                       name="email" placeholder="example@email.com" oninput="this.value = this.value.toLowerCase();">
                                <small class="text-danger" id="emailError"></small>
                            </div>

                    </div>





                    <div class="col-lg-6">



                        <div class="form-group">
                            <label>Gender<span style="color: red">*</span></label>
                            <select class="form-control" name="gender" id="gender" required>
                                <option> Male </option>
                                <option> Female </option>
                            </select>
                            <small class="text-danger" id="genderError"></small>
                        </div>

                        <div class="form-group">
                            <label>Date of Birth <span style="color: red">*</span></label>
                            <input type="date" class="form-control" id="dob"
                                   name="dob" placeholder="Date of Birth" min= "1900-01-01" max="<?= date('Y-m-d') ?>" >
                            <small class="text-danger" id="dobError"></small>
                        </div>

                         <div class="form-group">
                                <label>Contact No<span style="color: red"> *</span></label>
                                <input type="number" class="form-control" id="contactNo"
                                       name="contactNo" placeholder="0 XX XXX XXXX" maxlength="10">
                                <small class="text-danger" id="contactNoError"></small>
                         </div>

                          <div class="form-group">
                                <label>Password<span style="color: red">*</span></label>
                                <input type="password" class="form-control" id="password"
                                       name="password" placeholder="Enter Password">
                                <small class="text-danger" id="passwordError"></small>
                          </div>




                        <div class="form-group">
                            <button type="button"  class="btn btn-primary float-right"
                                    onclick="saveUser()">
                                Save User
                            </button>
                        </div>


                    </div>

                </div>


              </div>




            </div>
        </div>
    </div>
</div>
<!-- End Add User Modal -->




<!--View User modal Start-->
<div class="modal fade" id="viewUserModal" tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title mt-0">User Details</h5>
                <button type="button" class="close" data-dismiss="modal"
                        aria-hidden="true">×
                </button>
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
                            <input type="text" class="form-control" id="viewEmail" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Contact Number</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="viewContactNo" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Gender</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="viewGender" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">DOB</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="viewDob" readonly max="<?= date('Y-m-d') ?>" >
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-12">
                            <button type="button" class="btn btn-md btn-outline-primary waves-effect float-right" data-dismiss="modal" >Close</button>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>
</div>

<!--View User modal End-->






<!-- Update User Modal Start-->
<div class="modal fade" id="updateUserModal" tabindex="-1"
     role="dialog"
     aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title mt-0">Update User</h5>
                <button type="button" class="close" data-dismiss="modal"
                        aria-hidden="true">×
                </button>
            </div>

            <div class="modal-body">

                <div class="form-group">
                    <label>First Name</label>


                    <input type="hidden" id="hiddenUserId" name="hiddenUserId">


                    <input type="text" class="form-control" name="updateFname"
                           id="updateFname" required placeholder="First Name"/>
                    <span class="text-danger" id="updateFnameError"></span>
                </div>


                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" class="form-control" name="updateLname"
                           id="updateLname" required placeholder="Last Name"/>
                    <span class="text-danger" id="updateLnameError"></span>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" name="updateEmail"
                           id="updateEmail" required placeholder="Email" oninput="this.value = this.value.toLowerCase();"/>
                    <span class="text-danger" id="updateEmailError"></span>
                </div>

                <div class="form-group">
                    <label>User Type<span style="color: red">*</span></label>
                    <select class="form-control" name="updateUserType" id="updateUserType" required>
                        <option disabled value="" selected> Select User Role</option>
                        <option value="1">System Admin</option>
                        <option value="2"> Management Employee </option>
                        <option value="3"> Ticket Counter Employee </option>
                    </select>
                    <small class="text-danger" id="userTypeError"></small>
                </div>

                <div class="form-group">
                    <label>Contact Number</label>
                    <input type="number" class="form-control" name="updateContactNo"
                           id="updateContactNo" required placeholder="Contact Number" maxlength="10"/>
                    <span class="text-danger" id="updateContactNoError"></span>
                </div>

                <div class="form-group">
                    <label>Gender</label>
                    <select class="form-control" name="updateGender" id="updateGender" required>
                                <option> Male </option>
                                <option> Female </option>
                            </select>
                    <span class="text-danger" id="updateGenderError"></span>
                </div>


                <div class="form-group">
                    <label>DOB</label>
                    <input type="date" class="form-control" name="updateDob"
                           id="updateDob" required placeholder="DOB" min= "1900-01-01" max="<?= date('Y-m-d') ?>" />
                    <span class="text-danger" id="updateDobError"></span>
                </div>



                <div class="form-group">
                    <button type="button"  class="btn btn-primary float-right"
                            onclick="updateUser()" >
                        Update User</button>
                </div>

            </div>

        </div>

    </div>
</div>
<!-- Update User Modal End-->







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

    //remove sorting of datatable
    $(document).ready(function() {

    if ($.fn.DataTable.isDataTable('#datatable')) {
        $('#datatable').DataTable().destroy();
        }


         $('#datatable').DataTable({
               "order": [], // Disable initial sorting
         "columnDefs": [
             { "orderable": false, "targets": [3,5,6] } // Disable sorting for target columns
          ]
        });
    });
    //remove sorting of datatable end

    $(document).on("wheel", "input[type=number]", function (e) {
        $(this).blur();
    });

    function adMethod(dataID, tableName) {

        $.post('activateDeactivate', {id: dataID, table: tableName}, function (data) {

        });
    }






 //Save User Start
    function saveUser(){

        $('#userTypeError').html('');
        $("#fNameError").html('');
        $("#lNameError").html('');
        $("#contactNoError").html('');
        $("#genderError").html('');
        $("#dobError").html('');
        $("#emailError").html('');
        $("#passwordError").html('');


        var userType = $("#userType").val();
        var fName = $("#fName").val();
        var lName = $("#lName").val();
        var gender = $("#gender").val();
        var email = $("#email").val();
        var dob = $("#dob").val();
        var contactNo = $("#contactNo").val();
        var password = $("#password").val();

        var token = $('meta[name="csrf-token"]').attr('content');

        $.post('saveEmployee',{
            userType:userType,
            fName:fName,
            lName:lName,
            gender:gender,
            dob:dob,
            contactNo:contactNo,
            email:email,
            password:password,



        },function (data) {


            if (data.errors != null) {

                if(data.errors.userType) {
                    var p = document.getElementById('userTypeError');
                    p.innerHTML = data.errors.userType[0];
                }


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

                if(data.errors.dob) {
                    var p = document.getElementById('dobError');
                    p.innerHTML = data.errors.dob[0];
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
                    type: "success",
                    title: 'USER SAVED',
                    autoHide: true,
                    delay: 2500,
                    position: {
                        x: "right",
                        y: "top"
                    },
                    icon: '<img src="{{ URL::asset('assets/images/correct.png')}}" />',

                    message: data.success,
                });
                $('input').val('');
                setTimeout(function () {
                    $('#addUserModal').modal('hide');
                }, 200);
                location.reload();
            }


        });

    }
    //Save User End






    //View User Details Start

    $(document).on('click', '#viewUserID', function () {

        var firstName = $(this).data("fname");
        var lastName = $(this).data("lname");
        var contactNo = $(this).data("contactno");
        var gender = $(this).data("gender");
        var dob = $(this).data("dob");
        var email = $(this).data("email");



        $("#viewFname").val(firstName);
        $("#viewLname").val(lastName);
        $("#viewContactNo").val(contactNo);
        $("#viewGender").val(gender);
        $("#viewDob").val(dob);
        $("#viewEmail").val(email);

    });

    //View User Details End






    //Update User Start
    $(document).on('click', '#updateUserID', function () {

        var userId = $(this).data("id");
        var firstName = $(this).data("fname");
        var lastName = $(this).data("lname");
        var userRole = $(this).data("role");
        var contactNo = $(this).data("contactno");
        var dob = $(this).data("dob");
        var email = $(this).data("email");
        var gender = $(this).data("gender");



        $("#hiddenUserId").val(userId);
        $("#updateFname").val(firstName);
        $("#updateLname").val(lastName);
        $("#updateEmail").val(email);
        $("#updateUserRole").val(userRole);
        $("#updateContactNo").val(contactNo);
        $("#updateDob").val(dob);
        $("#updateGender").val(gender);

         if ($("#updateUserType").val() !== userRole) {
            $("#updateUserType option").prop('selected', false);
            $("#updateUserType option[value='" + userRole + "']").prop('selected', true);
        }
         $("#updateUserType").trigger('change');

    });

    function updateUser() {


        $('#updateFnameError').html('');
        $("#updateLnameError").html('');
        $("#updateContactNoError").html('');
        $("#updateDobError").html('');
        $("#updateEmailError").html('');
        $("#updateGenderError").html('');
        $("#updateUserTypeError").html('');




        var hiddenUserId=$("#hiddenUserId").val();
        var firstName=$("#updateFname").val();
        var lastName=$("#updateLname").val();
        var contactNo=$("#updateContactNo").val();
        var dob=$("#updateDob").val();
        var email=$("#updateEmail").val();
        var gender=$("#updateGender").val();
        var userType=$("#updateUserType").val();

        $.post('updateEmployee',{

            hiddenUserId:hiddenUserId,

            firstName:firstName,
            lastName:lastName,
            contactNo:contactNo,
            dob:dob,
            email:email,
            gender:gender,
            userType:userType

        },function (data) {


            if (data.errors != null) {

                if(data.errors.firstName){
                    var p = document.getElementById('updateFnameError');
                    p.innerHTML = data.errors.firstName[0];
                }

                if(data.errors.lastName){
                    var p = document.getElementById('updateLnameError');
                    p.innerHTML = data.errors.lastName[0];
                }


                if(data.errors.contactNo){
                    var p = document.getElementById('updateContactNoError');
                    p.innerHTML = data.errors.contactNo[0];
                }


                if(data.errors.dob){
                    var p = document.getElementById('updateDobError');
                    p.innerHTML = data.errors.dob[0];
                }


                if(data.errors.email){
                    var p = document.getElementById('updateEmailError');
                    p.innerHTML = data.errors.email[0];
                }

                if(data.errors.gender){
                    var p = document.getElementById('updateGenderError');
                    p.innerHTML = data.errors.gender[0];
                }

                if(data.errors.userType){
                    var p = document.getElementById('updateUserTypeError');
                    p.innerHTML = data.errors.userRole[0];
                }

            }



            if(data.success != null){
                notify({
                    type: "success", //alert | success | error | warning | info
                    title: 'USER UPDATED',
                    autoHide: true, //true | false
                    delay: 2500, //number ms
                    position: {
                        x: "right",
                        y: "top"
                    },
                    icon: '<img src="{{ URL::asset('assets/images/correct.png')}}" />',
                    message: data.success,
                });
                $('input').val('');
                setTimeout(function () {
                    $('#updateUserModal').modal('hide');
                }, 200);

                setTimeout(function () {
                    location.reload();
                }, 1000);


            }
        })
    }
    //Update User End









//Hide Validation errors after closing the modal without refreshing
    $('.modal').on('hidden.bs.modal', function () {


        $('#userTypeError').html('');
        $("#fNameError").html('');
        $("#lNameError").html('');
        $("#contactNoError").html('');
        $("#genderError").html('');
        $("#dobError").html('');
        $("#emailError").html('');
        $("#passwordError").html('');




        $('#updateFnameError').html('');
        $("#updateLnameError").html('');
        $("#updateContactNoError").html('');
        $("#updateDobError").html('');


        $('input').val(''); //Clear input values of input fields

    });









  <!--After selecting a user type load the categories to a select box-->
    //when selecting a value of selectBox box loads another value set in another selectBox box

    /*function CategoryList(type) {
            /!*CategoryList(this.value)*!/ //User Type select box eke theena this.value walin ena value eka CategoryList() function eke input parameter eka widihata gannawa
            /!*alert(type)*!/ //type ekata ena value eka alert krnva
    $.post('category-select-box',{
        type:type
    },function (data) {
      /!*  console.log(data) *!/  //View the data on console that passed(returned) from back-end

        $("#categorySelectBox").html(data); //data wala theenne back-end eken ena selectBox eka. eka categorySelectBox eka id wena thana html ekata danava
    })
    }*/



</script>
@include('includes/footer_end')