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
<link rel="stylesheet" href="{{ asset('css/ticketPage.css') }}">

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

<!-- PAGE CONTENT START-->

<!-- PAGE CONTENT START -->
<div class="page-content-wrapper">
    <div class="container-fluid">

         <div class="row ticketpage-alert-container">
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
            </div>
       
        <div class="row mt-4">
            
            <!-- LEFT SIDE: Ticket Details-->
            @if(auth()->user()->user_role_iduser_role == 1 || auth()->user()->user_role_iduser_role == 3)
            <div class="col-lg-9 mb-4" id="ticketDetailsSection">
            @else
            <div class="w-100 mb-4" id="ticketDetailsSection">
            @endif

                @if(isset($booking))
                <!-- Added max-width and mx-auto to prevent over-stretching -->
                <div class="ticket-verification-page-main mx-auto" style="max-width: 500px; width: 100%;">
                    <div class="ticket-container">
                        <div class="ticket-header">
                            <h2> <img src="{{ URL::asset('assets/images/logo/logo_1.png')}}" alt="" height="50"> Cineverse Cinema</h2>
                            <p class="mb-0">Seat Booking Confirmation</p>
                        </div>
                        
                        <div class="ticket-body">
                            <div class="ticket-row">
                                <span class="ticket-label">Booking ID</span>
                                <span class="ticket-value">BK{{ $booking['booking_id']}}</span>
                            </div>
                            <div class="ticket-row">
                                <span class="ticket-label">Customer</span>
                                <span class="ticket-value">{{ $booking['customer_name'] }}</span>
                            </div>
                            <div class="ticket-row">
                                <span class="ticket-label">Movie</span>
                                <span class="ticket-value">{{ $booking['movie_name'] ?? 'Movie ID: ' . $booking['movieId'] }}</span>
                            </div>
                            <div class="ticket-row">
                                <span class="ticket-label">Date</span>
                                <span class="ticket-value">{{Carbon\Carbon::parse($booking['show_date'])->format('d-m-Y')}}</span>
                            </div>
                            <div class="ticket-row">
                                <span class="ticket-label">Show Time</span>
                                <span class="ticket-value">{{Carbon\Carbon::parse($booking['show_time'])->format('h:i A')}}</span>
                            </div>
                            
                            <div class="ticket-row">
                                <span class="ticket-label">Seats</span>
                                <div class="seat-numbers">
                                    @if(isset($seats) && is_iterable($seats))
                                        @foreach($seats as $seat)
                                            <span class="seat-badge">{{ $seat->row}}{{ $seat->number }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">No seats assigned</span>
                                    @endif
                                </div>
                            </div>

                            @if(isset($booking['booking_snacks']) && $booking['booking_snacks']->count() > 0)
                            <div class="ticket-row">
                                <span class="ticket-label">Snacks</span>
                                <div class="snack-order-list">
                                    @foreach($booking['booking_snacks'] as $item)
                                    <div class="snack-order-item">
                                        <span class="snack-order-name">
                                            {{ $item->snack->name }}
                                            @if($item->snack->size !== 'REGULAR')
                                                ({{ $item->snack->size }})
                                            @endif
                                        </span>
                                        <span class="snack-order-qty">x{{ $item->quantity }}</span>
                                        <span class="snack-order-price">Rs. {{ number_format($item->price * $item->quantity, 2) }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
            
                            <div class="ticket-row">
                                <span class="ticket-label">Payment Status</span>
                                <span class="ticket-status">{{ $booking['payment_status']}}</span>
                            </div>

                            @if(isset($booking['booking_snacks']) && $booking['booking_snacks']->count() > 0)
                            <div class="ticket-row ticket-amounts-row">
                                <div class="amounts-breakdown">
                                    <div class="amount-line">
                                        <span class="ticket-label">Tickets</span>
                                        <span class="ticket-value">LKR {{ number_format($booking['amount'], 2) }}</span>
                                    </div>
                                    <div class="amount-line">
                                        <span class="ticket-label">Snacks</span>
                                        <span class="ticket-value">LKR {{ number_format($booking['snacks_amount'], 2) }}</span>
                                    </div>
                                    <div class="amount-line grand-total-line">
                                        <span class="ticket-label">Total Amount</span>
                                        <span class="ticket-value ticket-amount">LKR {{ number_format($booking['amount'] + $booking['snacks_amount'], 2) }}</span>
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="ticket-row">
                                <span class="ticket-label">Total Amount</span>
                                <span class="ticket-value ticket-amount">LKR {{ number_format($booking['amount'], 2) }}</span>
                            </div>
                            @endif

                            
                            <div class="d-flex flex-column justify-content-center align-items-center mx-2 mt-3">
                                <div class="d-flex flex-row justify-content-center mb-2">
                                    @if(auth()->user()->user_role_iduser_role == 1 || auth()->user()->user_role_iduser_role == 3 || auth()->user()->user_role_iduser_role == 4)
                                        <a href="{{route('printTicket', ['booking_id' => $booking['booking_id']])}}" target="_blank" style="text-decoration: none;">
                                            <button class="btn btn-ticket-page mx-2">
                                                <i class="fa fa-download" aria-hidden="true"></i> Get Ticket
                                            </button>
                                        </a>
                                    @endif
                                    <form id="emailTicketForm" action="{{ route('sendTicketEmail') }}" method="post" >
                                        @csrf
                                        <input type="hidden" name="booking_id" value="{{ $booking['booking_id']}}">
                                        <button class="btn btn-ticket-page btn-email mx-2" type="submit">
                                            <i class="fa fa-share" aria-hidden="true"></i> Send via Email
                                        </button>
                                    </form>
                                </div>
                            </div>
                           
                            
                            <div class="ticket-footer">
                                <p class="mb-1">Please ensure you keep this ticket and bring it with you on the scheduled movie date</p>
                                <p class="mb-1">Please arrive 15 minutes before showtime</p>
                                <p class="mb-0">Contact us : info@cineverse.com</p>
                                <p class="mb-0">Call us : 0115123456</p>
                            </div>
                        </div>
                    </div>   
                </div>
                @else
                <div class="d-flex w-100 align-items-center justify-content-center mt-5">
                    <h3><i class="fa fa-exclamation-triangle"></i> No Booking Found</h3> 
                </div>
                @endif
            </div>

            <!-- RIGHT SIDE: Action Buttons & Scanner  -->
            <div class="col-lg-3">
                @if(auth()->user()->user_role_iduser_role == 1 || auth()->user()->user_role_iduser_role == 3)
                    
                    <!-- Scanner Button -->
                    <div class="qr-scanner-section w-100 mb-3">
                        <button type="button" class="btn btn-ticket-page w-100 mb-2" id="startScanBtn" style="margin-top:0;">
                            <i class="fa fa-camera"></i> Scan QR Code
                        </button>

                        <button type="button" class="btn btn-secondary w-100 mb-2" id="stopScanBtn" style="display:none;">
                            Stop Scanning
                        </button>

                        <div id="qrScannerContainer" style="display:none;" class="mt-2 text-center w-100">
                            <video id="qrVideo" style="width: 100%; border-radius: 8px;"></video>
                            <canvas id="qrCanvas" style="display:none;"></canvas>
                            <p class="text-muted mt-1" id="qrScanStatus">Point the qr code at the camera</p>
                        </div>

                        <form action="{{ route('verifyTicket') }}" method="post" id="ticketVerificationForm" style="display:none;">
                            <div class="input-group mt-2">
                                <div class="input-group-prepend">
                                    <span class="input-group-text font-weight-bold">BK</span>
                                </div>
                                {{csrf_field()}}
                                <input type="number" class="form-control" name="bookingId" id="bookingId" placeholder="123456789">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-dark" id="verifyBtn">Verify</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Action Buttons Wrapper (Hides during scan) -->
                    <div id="actionButtonsSection" class="w-100">
                        @if(isset($booking))
                            
                            <!-- Confirm Entry Block -->
                            <div class="action-block mb-3 p-3 text-center" style="background: #2d2d2d; border-radius: 10px; border: 1px solid #444;">
                                <h6 style="color: #00d4aa; text-transform: uppercase;">Entry Status</h6>
                                <p class="mb-1 text-white" style="font-size: 13px;">Available: <span class="font-weight-bold">{{ $booking['available_seats'] }}</span></p>
                                <p class="mb-2 text-white" style="font-size: 13px;">Confirmed: <span class="font-weight-bold">{{ $booking['entered_count'] }}</span></p>
                                
                                @if(isset($booking['available_seats']) && $booking['available_seats'] <= 0)
                                    <p class="text-danger mb-0 font-weight-bold">All entries confirmed.</p>
                                @else
                                    <button class="btn btn-ticket-page w-100 mt-2" style="margin-top:0;" type="button" id="confirm-entry-btn" data-toggle="modal" data-target="#changeEntryModal" data-id="{{ $booking['booking_id'] }}" data-availableentries="{{ $booking['available_seats'] }}">
                                        Confirm Entry
                                    </button>
                                @endif
                            </div>

                            <!-- Confirm Snack Block -->
                            @if(isset($booking['booking_snacks']) && $booking['booking_snacks']->count() > 0)
                                <div class="action-block mb-3 p-3 text-center" style="background: #2d2d2d; border-radius: 10px; border: 1px solid #444;">
                                    <h6 style="color: #00d4aa; text-transform: uppercase;">Snack Status</h6>
                                    @if(isset($booking['available_snacks']) && $booking['available_snacks'] <= 0)
                                        <p class="text-danger mb-0 font-weight-bold">All snacks collected.</p>
                                    @else
                                        <button type="button" class="btn btn-ticket-page w-100 mt-2" style="margin-top:0;" data-toggle="modal" data-target="#confirmSnackModal">
                                            Confirm Snacks
                                        </button>
                                    @endif
                                </div>
                            @endif

                        @endif
                    </div>
                @endif
            </div>

        </div> <!-- End Row -->
    </div> <!-- End container -->
...
    
    

{{-- Change Entry Modal --}}
    <div class="modal fade" id="changeEntryModal" tabindex="-1" role="dialog"
        aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title mt-0">Confirm Entry</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">

                    <form id="confirmEntryForm" action="{{ route('confirmEntry') }}" method="post">
                        @csrf
                        <input type="hidden" id="hiddenBookingId" name="booking_id">

                        <div class="form-group text-center">
                            <label class="snack-order-price">Available Entries: <span id="availableEntriesLabel">0</span></label>

                            <div class="input-group w-50 mx-auto d-flex justify-content-center align-items-center">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" id="minusEntryBtn">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                </div>
                                <input type="text" class="form-control text-center" name="confirmEntry"
                                    id="confirmEntry" >
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" id="plusEntryBtn">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>

                            <span class="text-danger d-block mt-2" id="confirmEntryError"></span>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary float-right" id="confirmEntrySubmitBtn">
                                Confirm Entry
                            </button>
                        </div>
                    </form>

                </div>
            </div>
    </div>
    </div>


{{-- Confirm Snack Collection Modal --}}
<div class="modal fade" id="confirmSnackModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mt-0">Confirm Snack Collection</h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">

                <form id="confirmSnackForm" action="{{ route('confirmSnack') }}" method="post">
                    @csrf
                    <input type="hidden" name="booking_id" value="{{ $booking['booking_id'] ?? '' }}">

                    <div id="snackItemsContainer">
                        @if(isset($booking['booking_snacks']))
                            @foreach($booking['booking_snacks'] as $item)
                                @php
                                    $remaining = $item->quantity - $item->received_quantity;
                                    $label = $item->snack->name;
                                    if ($item->snack->size !== 'REGULAR') {
                                        $label .= ' (' . $item->snack->size . ')';
                                    }
                                @endphp

                                <div class="snack-item-row d-flex justify-content-between align-items-center mb-2"
                                     data-booking-snack-id="{{ $item->idbooking_snacks }}"
                                     data-max="{{ $remaining }}">

                                    <span>{{ $label }}  </span> - 
                                    <span class="ticket-label">Received: {{ $item->received_quantity }}</span> - 
                                    <span class="snack-order-price">Remaining: {{ $remaining }}</span>

                                    <div class="input-group" style="width: 150px;">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary btn-minus-snack">
                                                <i class="fa fa-minus"></i>
                                            </button>
                                        </div>
                                        <input type="text" class="form-control text-center received-input"
                                               readonly value="{{ $remaining }}">
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary btn-plus-snack">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <span class="text-danger d-block mt-2" id="confirmSnackError"></span>

                    <div class="form-group mt-3">
                        <button type="submit" class="btn btn-primary float-right">
                            Confirm Collection
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
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
<script src="{{ URL::asset('assets/plugins/jsqr/jsQR.js') }}"></script>

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

    $(document).on("wheel", "input[type=number]", function (e) {
        $(this).blur();
    });

    $(document).ready(function (){
        var hasbooking = @json(isset($booking));

        if( !hasbooking ){
            startScanning();
        }
    })


    //QR Code Scanning****************************************************************

    var qrVideoElement = document.getElementById('qrVideo');
    var qrCanvasElement = document.getElementById('qrCanvas');
    var qrCanvasContext = qrCanvasElement.getContext('2d');
    var qrScanStatus = document.getElementById('qrScanStatus');

    var cameraStream = null;
    var scanningActive = false;

    
    document.getElementById('startScanBtn').addEventListener('click', function () {
        startScanning();
    });



    document.getElementById('stopScanBtn').addEventListener('click', function () {
        stopScanning();
    });

    function startScanning(){
        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
            .then(function (stream) {
                cameraStream = stream;
                qrVideoElement.srcObject = stream;
                qrVideoElement.play();

                document.getElementById('qrScannerContainer').style.display = 'block';
                document.getElementById('startScanBtn').style.display = 'none';
                document.getElementById('stopScanBtn').style.display = 'inline-block';
                document.getElementById('ticketVerificationForm').style.display = 'block';

                scanningActive = true;
                requestAnimationFrame(scanVideoFrame);
            })
            .catch(function (error) {
                qrScanStatus.textContent = 'Camera access denied or not available.';
            });
    }

    function stopScanning() {
        scanningActive = false;

        if (cameraStream) {
            cameraStream.getTracks().forEach(function (track) {
                track.stop();
            });
            cameraStream = null;
        }

        document.getElementById('qrScannerContainer').style.display = 'none';
        document.getElementById('startScanBtn').style.display = 'inline-block';
        document.getElementById('stopScanBtn').style.display = 'none';
        document.getElementById('ticketVerificationForm').style.display = 'none';
    }


    function scanVideoFrame() {
        if (!scanningActive) {
            return;
        }

        if (qrVideoElement.readyState === qrVideoElement.HAVE_ENOUGH_DATA) {
            // Instead of processing millions of pixels, restrict it to a 400x400 square.
            // This makes jsQR run instantly without freezing the browser.
            var scanSize = 400; 
            
            qrCanvasElement.width = scanSize;
            qrCanvasElement.height = scanSize;
            
            var minDimension = Math.min(qrVideoElement.videoWidth, qrVideoElement.videoHeight);
            var startX = (qrVideoElement.videoWidth - minDimension) / 2;
            var startY = (qrVideoElement.videoHeight - minDimension) / 2;
            
            qrCanvasContext.drawImage(
                qrVideoElement, 
                startX, startY, minDimension, minDimension, 
                0, 0, scanSize, scanSize                      
            );

            var imageData = qrCanvasContext.getImageData(0, 0, scanSize, scanSize);
            
            // This helps detect QR codes displayed on phone screens with dark mode enabled.
            var qrResult = jsQR(imageData.data, imageData.width, imageData.height, {
                inversionAttempts: "attemptBoth",
            });

            if (qrResult) {
                handleSuccessfulScan(qrResult.data);
                return; 
            }
        }

        setTimeout(function() {
            requestAnimationFrame(scanVideoFrame);
        }, 200);
    }

    function handleSuccessfulScan(scannedText) {

        var bookingId = scannedText.replace(/\D/g, '');

        qrScanStatus.textContent = 'QR code detected! Loading ticket...';

        stopScanning();

        document.getElementById('bookingId').value = bookingId;
        document.getElementById('ticketVerificationForm').submit();
    }

    //Confirm Entry****************************************************************
    
    var maxEntries = 0;
    var currentEntry = 0;

    // Populate data for Confirm Entry Modal
    $(document).on('click', '#confirm-entry-btn', function () {
        var bookingId = $(this).data('id');
        var availableEntries = $(this).data('availableentries');

        maxEntries = parseInt(availableEntries);
        currentEntry = maxEntries;

        $("#changeEntryModal #hiddenBookingId").val(bookingId);
        $("#changeEntryModal #availableEntriesLabel").text(maxEntries);
        $("#changeEntryModal #confirmEntry").val(currentEntry);
        $("#changeEntryModal #confirmEntryError").text('');
    });

    $('#plusEntryBtn').on('click', function () {
        if (currentEntry < maxEntries) {
            currentEntry = currentEntry + 1;
            $('#confirmEntry').val(currentEntry);
            $('#confirmEntryError').text('');
        } else {
            $('#confirmEntryError').text('Cannot exceed available entries.');
        }
    });

    
    $('#minusEntryBtn').on('click', function () {
        if (currentEntry > 0) {
            currentEntry = currentEntry - 1;
            $('#confirmEntry').val(currentEntry);
            $('#confirmEntryError').text('');
        } else {
            $('#confirmEntryError').text('Entry cannot go below 0.');
        }
    });

    
    $('#confirmEntryForm').on('submit', function (e) {
        e.preventDefault();

        var formData = $(this).serialize();

        $.ajax({
            url: '{{ route('confirmEntry') }}',
            method: 'POST',
            data: formData,
            success: function (response) {
                $('#changeEntryModal').modal('hide');

                notify({
                    type: "success",
                    title: 'Entry Confirmed',
                    autoHide: true,
                    delay: 2500,
                    position: {x: "right", y: "top"},
                    icon: '<img src="{{ URL::asset('assets/images/correct.png')}}" />',
                    message: response.message,
                });

                $('#actionButtonsSection .action-block').eq(0)
                    .find('.font-weight-bold').eq(0).text(response.available_seats);

                $('#actionButtonsSection .action-block').eq(0)
                    .find('.font-weight-bold').eq(1).text(response.entered_count);

                $('#confirm-entry-btn').data('availableentries', response.available_seats);
                
                if (response.available_seats <= 0) {
                    $('#confirm-entry-btn').replaceWith('<p class="text-danger mb-0 font-weight-bold">All entries confirmed.</p>');
                }
            },
            error: function (xhr) {
                var errorMessage = 'Something went wrong. Please try again.';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                $('#confirmEntryError').text(errorMessage);
            }
        });
    });

    //Confirm Snack Collection****************************************************************

    // Plus button for a snack row
    $(document).on('click', '.btn-plus-snack', function () {
        var row = $(this).closest('.snack-item-row');
        var input = row.find('.received-input');
        var max = parseInt(row.data('max'));
        var current = parseInt(input.val());

        if (current < max) {
            input.val(current + 1);
        }
    });

    // Minus button for a snack row
    $(document).on('click', '.btn-minus-snack', function () {
        var row = $(this).closest('.snack-item-row');
        var input = row.find('.received-input');
        var current = parseInt(input.val());

        if (current > 0) {
            input.val(current - 1);
        }
    });

    // Submit the whole form via AJAX
    $('#confirmSnackForm').on('submit', function (e) {
        e.preventDefault();

        var bookingId = $('input[name="booking_id"]', this).val();
        var items = [];

        $('.snack-item-row').each(function () {
            var bookingSnackId = $(this).data('booking-snack-id');
            var chosenQuantity = $(this).find('.received-input').val();

            items.push({
                booking_snack_id: bookingSnackId,
                quantity: chosenQuantity
            });
        });

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: {
                booking_id: bookingId,
                items: items
            },
            success: function (response) {
                $('#confirmSnackModal').modal('hide');

                notify({
                    type: "success",
                    title: 'Snacks Received',
                    autoHide: true,
                    delay: 2500,
                    position: {x: "right", y: "top"},
                    icon: '<img src="{{ URL::asset('assets/images/correct.png')}}" />',
                    message: response.message,
                });

                response.items.forEach(function (item) {
                var row = $('.snack-item-row[data-booking-snack-id="' + item.booking_snack_id + '"]');

                row.find('.ticket-label').text('Received: ' + item.received_quantity);
                row.find('.snack-order-price').text('Remaining: ' + item.remaining);
                row.attr('data-max', item.remaining);
                row.find('.received-input').val(item.remaining);
            });

            var snackBlock = $('#ticketDetailsSection').length
                ? $('.action-block').eq(1)
                : null;

            if (response.available_snacks <= 0) {
                $('.action-block').eq(1).find('button[data-target="#confirmSnackModal"]')
                    .replaceWith('<p class="text-danger mb-0 font-weight-bold">All snacks collected.</p>');
            }
        },
            error: function (xhr) {
                var errorMessage = 'Something went wrong. Please try again.';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                $('#confirmSnackError').text(errorMessage);
            }
        });
    });

    //Download Ticket****************************************************************
    function triggerDownload() {

        const elementsToHide = [
            '.btn-download',
            '.btn-ticket-page', 
            '.alert',
            '.ticket-success-container',
            'nav',
            'header',
            'footer',
            '.navbar'
        ];
        
        elementsToHide.forEach(selector => {
            const elements = document.querySelectorAll(selector);
            elements.forEach(el => {
                el.style.display = 'none';
            });
        });
        
        
        window.print();
        
        
        setTimeout(() => {
            elementsToHide.forEach(selector => {
                const elements = document.querySelectorAll(selector);
                elements.forEach(el => {
                    el.style.display = '';
                });
            });
        }, 500);
    }

    //Send Email***************************************************************************
    $('#emailTicketForm').on('submit', function (e) {
        e.preventDefault();

        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        var originalHtml = submitBtn.html();

        submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sending...');

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function (response) {
                notify({
                    type: "success",
                    title: 'Email Send',
                    autoHide: true,
                    delay: 2500,
                    position: {x: "right", y: "top"},
                    icon: '<img src="{{ URL::asset('assets/images/correct.png')}}" />',
                    message: response.message,
                });
            },
            error: function (xhr) {
                var errorMessage = 'Something went wrong. Please try again.';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                $.notify(errorMessage, 'error');
            },
            complete: function () {
                submitBtn.prop('disabled', false).html(originalHtml);
            }
        });
    });



    document.addEventListener('DOMContentLoaded', function() {
        document.querySelector('.btn-download')?.addEventListener('click', function() {
            triggerDownload();
        });
    });

</script>

@include('includes/footer_end')
