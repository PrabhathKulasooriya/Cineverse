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

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible text-center floating-alert" role="alert">
                        <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

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
    const standardPrice = {{ $standard_price }};
    const primePrice = {{ $prime_price }};
    const selectedSeats = [];
    let totalAmount = 0;

    document.addEventListener('DOMContentLoaded', function () {
        const seatButtons = document.querySelectorAll('.btn-seat');
        const selectedSeatsList = document.getElementById('selectedSeatsList');
        const totalAmountEl = document.getElementById('totalAmount');

        function updateSelectedSeatsList() {
            selectedSeatsList.innerHTML = '';
            let total = 0;

            // Sort seats by row (alphabetically) and number (numerically)
                const sortedSeats = selectedSeats.slice().sort((a, b) => {
                    if (a.row === b.row) {
                        return a.number - b.number;
                    }
                    return a.row.localeCompare(b.row);
                });

                sortedSeats.forEach(seat => {
                const div = document.createElement('div');
                div.textContent = `${seat.row}${seat.number}`;
                selectedSeatsList.appendChild(div);

                if (seat.type === '2') {
                    total += primePrice;
                } else if(seat.type === '1'){ 
                    total += standardPrice;
                }
            });

            totalAmountEl.textContent = total;
            totalAmount = total;
        }

        seatButtons.forEach(button => {
            button.addEventListener('click', function () {
                const seatId = this.getAttribute('data-seat-id');
                const seatRow = this.getAttribute('data-row');
                const seatNumber = this.getAttribute('data-number');
                const seatType = this.getAttribute('data-seatType');

                const index = selectedSeats.findIndex(seat => seat.id === seatId);

                if (this.classList.contains('tempSelected')) {
                    this.classList.remove('tempSelected');
                    if (index !== -1) {
                        selectedSeats.splice(index, 1);
                    }
                } else {
                    this.classList.add('tempSelected');
                    selectedSeats.push({
                        id: seatId,
                        row: seatRow,
                        number: seatNumber,
                        type: seatType,
                    });
                }

                updateSelectedSeatsList();
            });
        });

        // Form submission handling
        const confirmSelectionBtn = document.getElementById('confirmSelectionBtn');
        const seatSelectionForm = document.getElementById('seatSelectionForm');

        confirmSelectionBtn.addEventListener('click', function () {

                // Validation 
                if (selectedSeats.length === 0) {
                   Swal.fire({
                      title: 'No Seats Selected!',
                      text: 'Please select at least one seat to proceed with your booking.',
                      icon: 'warning',
                      confirmButtonText: 'OK',
                      confirmButtonColor: '#3085d6'
                  });
                   return;
             }

            // Confirmation dialog before proceeding
            const seatsList = selectedSeats.slice().sort((a, b) => {if (a.row === b.row) {return a.number - b.number;}
                                return a.row.localeCompare(b.row);})
                                .map(seat => `${seat.row}${seat.number}`).join(', ');
    
                Swal.fire({
                  title: 'Confirm Seat Selection',
                  html: `
                      <p><strong>Selected Seats:</strong> ${seatsList}</p>
                      <p><strong>Total Amount:</strong> Rs. ${totalAmount}</p>
                      <p>Do you want to proceed to payment?</p>
                  `,
                  icon: 'question',
                  showCancelButton: true,
                  confirmButtonText: 'Yes, proceed to payment',
                  cancelButtonText: 'Cancel',
                  confirmButtonColor: '#28a745',
                  cancelButtonColor: '#dc3545',
                  reverseButtons: true
                  }).then((result) => {

                  if (result.isConfirmed) {
                    
                      const selectedSeatsId = selectedSeats.map(seat => seat.id);
                      document.getElementById('hiddenTotalAmount').value = totalAmount;
                      document.getElementById('hiddenSelectedSeatsId').value = JSON.stringify(selectedSeatsId);

                      seatSelectionForm.submit();
                   } 
             });
            });
    });
</script>

@endsection