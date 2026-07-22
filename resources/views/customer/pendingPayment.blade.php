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

            @if($pendingPayments!=null)
        <div class="col-lg-12">
            <div class="card m-b-20">
                <div class="card-body">


                <br/>

             <div class="table-rep-plugin">
                <div class="table-responsive b-0" data-pattern="priority-columns">

                    <table id="datatable" class="table table-striped table-bordered"
                           cellspacing="0"
                           width="100%">
            
                        <thead>
                            <tr>
                                <th>BOOKING ID</th>
                                <th>MOVIE</th>
                                <th>DATE</th>
                                <th>TIME</th>
                                <th>AMOUNT</th>
                                <th>TIME LEFT</th> 
                                <th>PAYMENT STATUS</th>
                                <th>OPTIONS</th>
                            </tr>
                        </thead>

                        <tbody>
                            @if(count($pendingPayments) > 0)
                                @foreach($pendingPayments as $pendingPayment)
                                    <tr>
                                        <td>BK{{ $pendingPayment['booking_id'] }}</td> 
                                        <td>{{ $pendingPayment['movie'] }}</td> 
                                        <td>{{ $pendingPayment['date'] }}</td> 
                                        <td>{{\Carbon\Carbon::parse($pendingPayment['time'])->format('h:i A')}}</td> 
                                        <td>{{ $pendingPayment['amount'] }}</td> 
                                        
                                        <td>
                                            <span class="text-danger p-2 countdown-timer" 
                                                data-expires-at="{{ $pendingPayment['expires_at'] }}" 
                                                data-booking-id="{{ $pendingPayment['booking_id'] }}">
                                                --:--
                                            </span>
                                        </td>

                                        <td><p class="text-danger mb-0">{{ $pendingPayment['payment_status'] }}</p></td> 
                                        
                                        <td class="d-flex">
                                            <!-- Fixed: Removed duplicate IDs, used dynamic ones -->
                                            <form action="{{ route('payPending') }}" method="post" id="payForm_{{ $pendingPayment['booking_id'] }}">
                                                @csrf
                                                <input type="hidden" name="bookingData" value="{{json_encode($pendingPayment)}}">
                                                <button type="submit" class="btn btn-sm btn-success waves-effect mr-2 pay-btn" id="payBtn_{{ $pendingPayment['booking_id'] }}">
                                                    <i class="fa fa-usd" aria-hidden="true"></i> Pay Now
                                                </button>
                                            </form>
                                            
                                            <form action="{{ route('cancelPayment') }}" method="post">
                                                @csrf
                                                <input type="hidden" name="bookingData" value="{{$pendingPayment['booking_id']}}">
                                                <button type="submit" class="btn btn-sm btn-danger waves-effect">
                                                    <i class="fa fa-trash" aria-hidden="true"></i> 
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>


              <!--Data Table End-->

              @else
              <div>
                <h3><i class="fa fa-exclamation-triangle"></i> No Pending Payments Found.</h3>                   
            </div>
            @endif
            

                </div>
            </div>
        </div>
    </div> <!-- container -->

           

</div> <!-- Page content Wrapper -->

</div> <!-- content -->









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
    $(document).ready(function() {
        setTimeout(function() {
            $(".alert").fadeOut("slow", function() {
                $(this).remove();
            });
        }, 3000); 
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const timers = document.querySelectorAll('.countdown-timer');
        let cleanupFired = false;

        timers.forEach(timer => {
            const expiresAtStr = timer.getAttribute('data-expires-at');
            const expiresAt = new Date(expiresAtStr).getTime();
            const bookingId = timer.getAttribute('data-booking-id');
            const payBtn = document.getElementById('payBtn_' + bookingId);
            
            let timerInterval;

            function formatTime(totalSeconds) {
                let m = Math.floor(totalSeconds / 60).toString().padStart(2, '0');
                let s = (totalSeconds % 60).toString().padStart(2, '0');
                return m + ':' + s;
            }

            function onExpired() {
                clearInterval(timerInterval);
                timer.textContent = "00:00";
                timer.classList.replace('badge-warning', 'badge-danger');
                if (payBtn) {
                    payBtn.disabled = true;
                    payBtn.classList.remove('btn-success');
                    payBtn.classList.add('btn-secondary'); 
                }
                
                triggerCleanup();
            }

            function updateTimer() {
                let secondsLeft = Math.floor((expiresAt - Date.now()) / 1000);

                if (secondsLeft <= 0) {
                    onExpired();
                    return;
                }

                timer.textContent = formatTime(secondsLeft);
            }

            updateTimer();

            if (Math.floor((expiresAt - Date.now()) / 1000) > 0) {
                timerInterval = setInterval(updateTimer, 1000);
            }
        });

        function triggerCleanup() {
            if (cleanupFired) return;
            cleanupFired = true;

            fetch("{{ route('cleanup.expired') }}", {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            }).then(response => {
                console.log("Cleanup executed");
                setTimeout(() => { cleanupFired = false; }, 10000);
            }).catch(error => {
                console.error("Cleanup failed", error);
            });
        }
    });
</script>

<script>

    const payButton = document.getElementById('payNowBtn');
    const payNowForm = document.getElementById('payNowForm');

    payButton.addEventListener('click', function(event) {
        event.preventDefault();
        payNowForm.submit();
    });
</script>

@include('includes/footer_end')
