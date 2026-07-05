@extends('moviePage_include.main')

@section('pageSpecificStyles')
<link rel="stylesheet" href="{{ asset('css/seatSelection.css') }}">
@endsection

@section('pageSpecificContent')

    <div id="preloader">
        <div class="preloader">
            <div class="preloader-bounce">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </div>

    <div class="seatselection-main">

        @if(session('error'))
            <div class="alert alert-danger text-center position-absolute fade show" style="top: 20px; right: 20px; z-index: 1050; min-width: 350px;">
                <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        @endif

    <div class="seatSelection-details" >
         
        <h1>{{$movie->name}}</h1>
        <div class="image-container">
            <img src="{{ URL::asset('movieImages/'.$movie->image) }}" alt="{{$movie->name}}" style="width: 150px;">
        </div>
        <h3>Date : {{Carbon\Carbon::parse($show->date)->format('d-m-Y')}}</h3>
        <h3>TIme : {{Carbon\Carbon::parse($show->time)->format('h:i A')}}</h3>

    </div>
    
    <div class="seatSelection-selection">
                <h3 >Select Your Seats</h3>
                <div class="seatSelection-selection-container">
                <div class="screen">SCREEN</div>
            
                @php
                    $groupedRows = $seats->groupBy('row');
                    $rowCount = $groupedRows->count();
                    $index = 0;
                @endphp

        <div class="seat-container">
         @foreach($groupedRows as $row => $rowSeats)
                @php 
                        $index++;
                        $seatCount = $rowSeats->count();
                        $sortedSeats = $rowSeats->sortBy('number')->values();
            
                        // Determine grouping pattern
                        switch ($seatCount) {
                            case 6:
                                $groups = [2, 2, 2];
                                break;
                            case 8:
                                $groups = [2, 4, 2];
                                break;
                            case 10:
                                $groups = [3, 4, 3];
                                break;
                            case 12:
                                $groups = [4, 4, 4];
                                break;
                            default:
                                $groups = [$seatCount]; 
                     }

                     $seatIndex = 0;
                @endphp

                <div class="seat-row">
                    @foreach($groups as $i => $groupSize)
                     @for($j = 0; $j < $groupSize; $j++)
                         @php 
                             $seat = $sortedSeats[$seatIndex]; 
                             $seatClass = $seat->seat_type_idseat_type == 2 ? 'btn-prime' : 'btn-standard';
                             $isBooked = in_array($seat->seat_id, $bookedSeatIds);
                         @endphp
                         <button 
                            type="button"
                            class="btn-seat {{ $seatClass }} {{$isBooked ? 'booked' : ''}}"
                            data-seat-id="{{ $seat->seat_id }}"
                            data-row="{{ $seat->row }}"
                            data-number="{{ $seat->number }}"
                            data-seatType = "{{ $seat->seat_type_idseat_type }}"
                            {{$isBooked ? 'disabled' : ''}}
                        >
                        {{ $seat->row }} {{ $seat->number }}
                    </button>
                    @php $seatIndex++; @endphp
                    @endfor

                    @if ($i < count($groups) - 1)
                        <div style="width: 15px;"></div>
                    @endif
                @endforeach
            </div>
        @endforeach
    </div>
    </div>

    </div>
        <div class="seat-prices-container">
            <div class="seat-prices">
            <h6><i class="fa fa-ticket" aria-hidden="true"></i> Ticket Prices</h6>
            <div class="seat-standard">
                    <div class="btn-price btn-standard">Standard</div>
                    <span class="price"> : Rs. {{$standard_price}}</span>
            </div>
            <div class="seat-prime">
                    <div class=" btn-price btn-prime">Prime</div>
                    <span class="price"> : Rs. {{$prime_price}}</span>
            </div>
            </div>
    
            <div class="selected-seats">

                <!-- Form for seat selection submission -->
                <form id="seatSelectionForm" action="{{ route('confirmSeatSelection') }}" method="POST" class="d-none">
                    @csrf
                    <input type="hidden" name="totalAmount" id="hiddenTotalAmount">
                    <input type="hidden" name="selectedSeatsId" id="hiddenSelectedSeatsId">
                    <input type="hidden" name="movieId" value="{{ $movie->movie_id }}">
                    <input type="hidden" name="showId" value="{{ $show->show_id }}">
                </form>

                <div class="selected-seats-top">
                    <h2>Selected Seats</h2>
                    <div id="selectedSeatsList" class="selected-seats-list">
                    </div>
                </div>
                <div class="selected-seats-bottom"> 
                    <div class="total-price mt-3"><strong>Total: Rs. <span id="totalAmount">0</span></strong></div>
                    <div class="mt-4">
                        <button class="btn btn-proceed-payment waves-effect waves-light" id="confirmSelectionBtn">Proceed to payment</button>
                    </div>
                </div>  
            </div>
            
        </div>
    </div>

@endsection

@section('pageSpecificScript')

<script>
    var standardPrice = {{ $standard_price }};
    var primePrice = {{ $prime_price }};
    var maxSeats = 12;

    @if(Auth::check() && (Auth::user()->user_role_iduser_role == 1 || Auth::user()->user_role_iduser_role == 2 || Auth::user()->user_role_iduser_role == 3))
        var isEmployee = true;
    @else
        var isEmployee = false;
    @endif

    document.addEventListener('DOMContentLoaded', function () {
        var seatButtons = document.querySelectorAll('.btn-seat');
        var selectedSeatsList = document.getElementById('selectedSeatsList');
        var totalAmountEl = document.getElementById('totalAmount');
        var confirmBtn = document.getElementById('confirmSelectionBtn');
        var form = document.getElementById('seatSelectionForm');

        // Rebuilds the seat list and total based on whichever seats are currently selected
        function updateSummary() {
            var selected = document.querySelectorAll('.btn-seat.tempSelected');
            var total = 0;
            selectedSeatsList.innerHTML = '';

            selected.forEach(function (seat) {
                var div = document.createElement('div');
                div.textContent = seat.getAttribute('data-row') + seat.getAttribute('data-number');
                selectedSeatsList.appendChild(div);

                total += (seat.getAttribute('data-seatType') === '2') ? primePrice : standardPrice;
            });

            totalAmountEl.textContent = total;
        }

        seatButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                var alreadySelected = button.classList.contains('tempSelected');
                var selectedCount = document.querySelectorAll('.btn-seat.tempSelected').length;

                if (!alreadySelected && !isEmployee && selectedCount >= maxSeats) {
                    swal.fire({
                        title: 'Seat Selection Limit Reached!',
                        text: 'You can select a maximum of ' + maxSeats + ' seats. Please deselect some seats to select new ones.',
                        icon: 'warning',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }

                button.classList.toggle('tempSelected');
                updateSummary();
            });
        });

        // Confirms selection and submits the booking form
        confirmBtn.addEventListener('click', function () {
            var selected = document.querySelectorAll('.btn-seat.tempSelected');

            if (selected.length === 0) {
                Swal.fire({
                    title: 'No Seats Selected!',
                    text: 'Please select at least one seat to proceed with your booking.',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            var seatIds = [];
            var seatNames = [];
            var total = 0;

            selected.forEach(function (seat) {
                seatIds.push(seat.getAttribute('data-seat-id'));
                seatNames.push(seat.getAttribute('data-row') + seat.getAttribute('data-number'));
                total += (seat.getAttribute('data-seatType') === '2') ? primePrice : standardPrice;
            });

            Swal.fire({
                title: 'Confirm Seat Selection',
                html: '<p><strong>Selected Seats:</strong> ' + seatNames.join(', ') + '</p>' +
                      '<p><strong>Total Amount:</strong> Rs. ' + total + '</p>' +
                      '<p>Do you want to proceed to payment?</p>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, proceed to payment',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#dc3545',
                reverseButtons: true
            }).then(function (result) {
                if (result.isConfirmed) {
                    document.getElementById('hiddenTotalAmount').value = total;
                    document.getElementById('hiddenSelectedSeatsId').value = JSON.stringify(seatIds);
                    form.submit();
                }
            });
        });
    });
</script>

@endsection