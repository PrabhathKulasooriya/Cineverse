@extends('moviePage_include.main')
@section('pageSpecificStyles')
<link rel="stylesheet" href="{{ asset('css/payment.css') }}">
@endsection

@section('pageSpecificContent')

<div class="payment-main">
        @if(session('error'))
            <div class="alert alert-danger text-center position-absolute fade show" style="top: 20px; right: 20px; z-index: 1050; min-width: 350px;">
                <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        @endif
   {{-- payment-left --}}
    <div class="payment-left glass-panel">

       
        <div class="movie-header-row">
            
            <div class="movie-summary">
                <img src="{{ URL::asset('movieImages/' . ($bookingData['movie_image'] ?? '')) }}"
                     alt="movie" class="movie-summary-img">
                <div class="movie-summary-info">
                    <h5>{{ $bookingData['movie_name'] ?? '' }}</h5>
                    <p><i class="fa fa-calendar"></i> {{ \Carbon\Carbon::parse($bookingData['show_date'] ?? '')->format('d-m-Y') }}</p>
                    <p><i class="fa fa-clock-o"></i> {{ \Carbon\Carbon::parse($bookingData['show_time'] ?? '')->format('h:i A') }}</p>
                </div>
            </div>

            
            <div class="countdown-banner" id="countdownBanner">
                <i class="fa fa-clock-o"></i>
                <div class="countdown-text">
                    <span>Seat hold expires in: <strong class="countdown-time" id="countdown">{{ $formattedTimeLeft }}</strong></span>
                    <small class="countdown-subtext">Complete payment before time runs out!</small>
                </div>
            </div>
        </div>

        <div class="section-divider"></div>

        
        <div class="snack-section">
    <h6 class="snack-header">
        <i class="fa fa-shopping-basket"></i> Add Snacks <small>(optional)</small>
    </h6>

    <div class="snack-grid">

        @forelse($snacks as $snackName => $snackGroup)

            <div class="snack-card">

                <img src="{{ URL::asset('snackImages/' . $snackGroup->first()->image) }}"
                     alt="{{ $snackName }}"
                     class="snack-image">

                <div class="snack-details-wrapper">

                    <div class="snack-name">
                        {{ $snackName }}
                    </div>

                    <div class="snack-variants">

                        @foreach($snackGroup as $snackRow)

                            <div class="variant-row {{ $snackRow->available ? '' : 'disabled-variant' }}">

                                <span class="size-badge">
                                    {{ $snackRow->size }}
                                </span>

                                <span class="variant-price">
                                    Rs. {{ number_format($snackRow->price,2) }}
                                </span>

                                <div class="qty-control">

                                    @if($snackRow->available)

                                        <button
                                            type="button"
                                            class="qty-btn qty-minus"
                                            data-id="v{{ $snackRow->idsnacks }}">
                                            −
                                        </button>

                                        <span
                                            class="qty-display"
                                            id="qty_v{{ $snackRow->idsnacks }}">
                                            0
                                        </span>

                                        <button
                                            type="button"
                                            class="qty-btn qty-plus"
                                            data-id="v{{ $snackRow->idsnacks }}"
                                            data-price="{{ $snackRow->price }}"
                                            data-name="{{ $snackName }} ({{ $snackRow->size }})">
                                            +
                                        </button>

                                    @else

                                        <span class="text-danger small">
                                            Sold Out
                                        </span>

                                    @endif

                                </div>

                            </div>

                        @endforeach

                    </div>

                </div>

            </div>

        @empty

            <div class="text-center w-100">
                <p style="color:#999;">
                    No snacks available.
                </p>
            </div>

        @endforelse

    </div>
</div>

    </div>{{-- end payment-left --}}

    {{-- ── RIGHT PANEL: Payment Form ── --}}
    <div class="payment-right ">
        <div class="payment-right-inner glass-panel">
        {{-- Payment Methods Header --}}
        <div class="header payement-methods-header">
            @if(Auth::check() && Auth::user()->user_role_iduser_role == 3)
                <button class="toggle-method-btn active" id="cashTab">Cash</button>
            @elseif(Auth::check() && Auth::user()->user_role_iduser_role == 1)
                <button class="toggle-method-btn active" id="cashTab">Cash</button>
                <button class="toggle-method-btn" id="cardTab">Card Payments</button>
            @else
                <button class="toggle-method-btn active" id="cardTab">Card Payments</button>
            @endif
        </div>

       

        <div id="snackInputsContainer"></div>

        @if(Auth::check() && (Auth::user()->user_role_iduser_role == 3 || Auth::user()->user_role_iduser_role == 1))
        {{-- Cash Payment Form --}}
        <div class="payment-container" id="cashSection" style="display: block;">
            <div class="payment-header">
                <h5>COUNTER CHECKOUT</h5>
            </div>
            
            <form id="cashPayForm" method="POST" action="{{ route('manualPayment') }}" class="payment-form">
                @csrf
                <input type="hidden" name="bookingId" value="{{ $bookingData['booking_id'] }}">
                <input type="hidden" name="paymentMethod" value="CASH">
                <div id="cashSnackInputs"></div>

                <div class="form-element">
                    <span class="child">
                        <label>Name <span class="required">*</span></label>
                        <input type="text" id="cashName" name="name" placeholder="enter your name">
                    </span>
                    <small class="text-danger error-msg" id="cashNameError"></small>
                </div>
                
                <div class="form-element">
                    <span class="child">
                        <label>Email <span class="required">*</span></label>
                        <input type="email" id="cashEmail" name="email" placeholder="your@email.com" oninput="this.value = this.value.toLowerCase();">
                    </span>
                    <small class="text-danger error-msg" id="cashEmailError"></small>
                </div>

                <div class="order-summary">
                    <div class="summary-row">
                        <span>Tickets</span>
                        <span>Rs. {{ $bookingData['amount'] }}</span>
                    </div>
                    <div class="summary-row" id="cashSnackSummaryRow" style="display:none;">
                        <span>Snacks</span>
                        <span id="cashSnackTotalDisplay">Rs. 0.00</span>
                    </div>
                    <div class="summary-row summary-total">
                        <strong>Total</strong>
                        <strong id="cashGrandTotal">Rs. {{ $bookingData['amount'] }}</strong>
                    </div>
                </div>

                <div class="btn-container">
                    <button type="button" class="pay-button btn-pay" id="cashPayButton">
                        <i class="fa fa-money" aria-hidden="true"></i> Pay
                    </button>
                    <a href="{{ route('cancel') }}" class="cancel-link">
                        <button type="button" class="pay-button btn-cancel">
                            <i class="fa fa-trash-o" aria-hidden="true"></i> Cancel Booking
                        </button>
                    </a>
                </div>
            </form>
        </div>
        @endif

        @if(!Auth::check() || Auth::user()->user_role_iduser_role != 3)
        {{-- Card Payment Form --}}
        <div class="payment-container" id="cardSection" style="display: {{ (Auth::check() && Auth::user()->user_role_iduser_role == 1) ? 'none' : 'block' }};">
            <div class="payment-header">
                <h5>CARD PAYMENT</h5>
                <span class="card-elements">
                    <img src="assets/images/logo/visa.png" height="30" alt="visa">
                    <img src="assets/images/logo/mastercard.png" height="30" alt="mastercard">
                    <img src="assets/images/logo/amex.png" height="30" alt="amex">
                </span>
            </div>

            <form action="{{ route('manualPayment') }}" method="POST" class="payment-form" id="paymentForm">
                @csrf
                <input type="hidden" name="paymentMethod" value="CARD">
                <input type="hidden" id="bookingId" name="bookingId" value="{{ $bookingData['booking_id'] }}">
                <div id="cardSnackInputs"></div>

                <div class="form-element">
                    <span class="child">
                        <label>Card Number <span class="required">*</span></label>
                        <input type="text" id="cardNumber" name="cardNumber" placeholder="1234 5678 9012 3456" maxlength="19">
                    </span>
                    <small class="text-danger error-msg" id="cardNumberError"></small>
                </div>

                <div class="exp-cvv-row">
                    <div class="form-element form-element-expire">
                        <span class="child">
                            <label>Expire Date <span class="required">*</span></label>
                            <input type="text" id="expireDate" name="expireDate" placeholder="MM/YY" maxlength="5">
                        </span>
                        <small class="text-danger error-msg" id="expireDateError"></small>
                    </div>
                    <div class="form-element form-element-cvv">
                        <span class="child child-cvv">
                            <label>CVV <span class="required">*</span></label>
                            <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="4">
                        </span>
                        <small class="text-danger error-msg" id="cvvError"></small>
                    </div>
                </div>

                <div class="form-element">
                    <span class="child">
                        <label>Name <span class="required">*</span></label>
                        <input type="text" id="name" name="name" placeholder="enter your name"
                            @if(Auth::check()) value="{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}" @endif>
                    </span>
                    <small class="text-danger error-msg" id="nameError"></small>
                </div>
                
                <div class="form-element">
                    <span class="child">
                        <label>Email Address <span class="required">*</span></label>
                        <input type="email" id="email" name="email" placeholder="your@email.com"
                            @if(Auth::check()) value="{{ Auth::user()->email }}" @endif
                            oninput="this.value = this.value.toLowerCase();">
                    </span>
                    <small class="text-danger error-msg" id="emailError"></small>
                </div>

                <div class="order-summary">
                    <div class="summary-row">
                        <span>Tickets</span>
                        <span>Rs. {{ $bookingData['amount'] }}</span>
                    </div>
                    <div class="summary-row" id="cardSnackSummaryRow" style="display:none;">
                        <span>Snacks</span>
                        <span id="cardSnackTotalDisplay">Rs. 0.00</span>
                    </div>
                    <div class="summary-row summary-total">
                        <strong>Total</strong>
                        <strong id="cardGrandTotal">Rs. {{ $bookingData['amount'] }}</strong>
                    </div>
                </div>

                <div class="btn-container">
                    <button type="button" class="pay-button btn-pay" id="payButton">
                        <i class="fa fa-credit-card-alt" aria-hidden="true"></i> Pay Now
                    </button>
                    <a href="{{ route('cancel') }}" class="cancel-link">
                        <button type="button" class="pay-button btn-cancel">
                            <i class="fa fa-trash-o" aria-hidden="true"></i> Cancel Booking
                        </button>
                    </a>
                </div>
            </form>
        </div>
        @endif
        </div>
    </div>{{-- end payment-right --}}

</div>{{-- end payment-main --}}

@endsection

@section('pageSpecificScript')
<script>

// ── Hide the success/error alert after 3 seconds ───────────────────────────
setTimeout(function () {
    var alertBox = document.querySelector(".alert");
    if (alertBox) {
        alertBox.style.display = "none";
    }
}, 3000);


// ── Countdown timer ─────────────────────────────────────────────────────────
var secondsLeft = {{ $secondsRemaining }};
var timerInterval;
var expiresAt = new Date('{{ $expiresAt }}').getTime();
var ticketAmount = {{ $bookingData['amount'] }};

function formatTime(totalSeconds) {
    var minutes = Math.floor(totalSeconds / 60);
    var seconds = totalSeconds % 60;
    if (minutes < 10) { minutes = "0" + minutes; }
    if (seconds < 10) { seconds = "0" + seconds; }

    return minutes + ":" + seconds;
}

function onExpired() {
    clearInterval(timerInterval);
    document.getElementById('countdown').textContent = '00:00';
    document.getElementById('countdownBanner').classList.add('expired');

    var buttonsToDisable = document.querySelectorAll('.qty-btn, #payButton, #cashPayButton');
    buttonsToDisable.forEach(function (btn) {
        btn.disabled = true;
    });

    Swal.fire({
        title: 'Seat Hold Expired',
        text: 'Your seats have been released. Please start a new booking.',
        icon: 'error',
        confirmButtonText: 'Go Home',
        confirmButtonColor: '#dc3545',
        allowOutsideClick: false,
    }).then(function () {
        window.location.href = "{{ route('home') }}";
    });
}

function startTimer() {
    var countdownEl = document.getElementById('countdown');
    var bannerEl = document.getElementById('countdownBanner');

    if (secondsLeft <= 0) {
        onExpired();
        return;
    }

    countdownEl.textContent = formatTime(secondsLeft);

    timerInterval = setInterval(function () {
        
        secondsLeft = Math.floor((expiresAt - Date.now()) / 1000);
        countdownEl.textContent = formatTime(secondsLeft);

        if (secondsLeft <= 120) {
            bannerEl.classList.add('warning');
        }

        if (secondsLeft <= 0) {
            onExpired();
            return;
        }

        if (secondsLeft % 60 === 0) {
            syncWithServer();
        }
    }, 1000);
}

function syncWithServer() {
    fetch("{{ route('timeRemaining') }}")
        .then(function (response) {
            return response.json();
        })
        .then(function (data) {
            if (data.expired) {
                onExpired();
                return;
            }
            secondsLeft = data.seconds;
        })
        .catch(function () {
            console.error('Error fetching time remaining from server.');
        });
}


// ── Snack quantity controls ─────────────────────────────────────────────────
var selectedSnacks = {};

var plusButtons = document.querySelectorAll('.qty-plus');
plusButtons.forEach(function (button) {
    button.addEventListener('click', function () {
        var id = this.dataset.id;
        var price = parseFloat(this.dataset.price);
        var name = this.dataset.name;
        var qtyEl = document.getElementById('qty_' + id);

        var newQty = parseInt(qtyEl.textContent) + 1;
        if (newQty > 10) {
            return; 
        }

        qtyEl.textContent = newQty;
        selectedSnacks[id] = { name: name, price: price, qty: newQty };

        updateOrderSummary();
    });
});

var minusButtons = document.querySelectorAll('.qty-minus');
minusButtons.forEach(function (button) {
    button.addEventListener('click', function () {
        var id = this.dataset.id;
        var qtyEl = document.getElementById('qty_' + id);

        var newQty = parseInt(qtyEl.textContent) - 1;
        if (newQty < 0) {
            return;
        }

        qtyEl.textContent = newQty;
        if (selectedSnacks[id]) {
            selectedSnacks[id].qty = newQty;
        }

        updateOrderSummary();
    });
});

// Recalculates the snack total + grand total and shows it on screen
function updateOrderSummary() {
    var snackTotal = 0;

    Object.keys(selectedSnacks).forEach(function (key) {
        var item = selectedSnacks[key];
        if (item.qty > 0) {
            snackTotal = snackTotal + (item.price * item.qty);
        }
    });

    var grandTotal = ticketAmount + snackTotal;

    var cardSnackRow = document.getElementById('cardSnackSummaryRow');
    if (cardSnackRow) {
        cardSnackRow.style.display = snackTotal > 0 ? 'flex' : 'none';
        document.getElementById('cardSnackTotalDisplay').textContent = 'Rs. ' + snackTotal.toFixed(2);
        document.getElementById('cardGrandTotal').textContent = 'Rs. ' + grandTotal.toFixed(2);
    }

    var cashSnackRow = document.getElementById('cashSnackSummaryRow');
    if (cashSnackRow) {
        cashSnackRow.style.display = snackTotal > 0 ? 'flex' : 'none';
        document.getElementById('cashSnackTotalDisplay').textContent = 'Rs. ' + snackTotal.toFixed(2);
        document.getElementById('cashGrandTotal').textContent = 'Rs. ' + grandTotal.toFixed(2);
    }
}

// Builds hidden <input> fields so the snack data gets submitted with the form
function injectSnackInputs(containerId) {
    var container = document.getElementById(containerId);
    container.innerHTML = '';

    var snackTotal = 0;

    Object.keys(selectedSnacks).forEach(function (key) {
        var item = selectedSnacks[key];
        if (item.qty <= 0) {
            return; 
        }

        snackTotal = snackTotal + (item.price * item.qty);

        var variantId = key.replace('v', '');

        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'snacks[' + variantId + ']';
        input.value = item.qty;
        container.appendChild(input);
    });

    var totalInput = document.createElement('input');
    totalInput.type = 'hidden';
    totalInput.name = 'grandTotal';
    totalInput.value = (ticketAmount + snackTotal).toFixed(2);
    container.appendChild(totalInput);
}


// ── Card number / expiry / CVV formatting ───────────────────────────────────
var cardNumberInput = document.getElementById('cardNumber');
if (cardNumberInput) {
    cardNumberInput.addEventListener('input', function (e) {
        var digitsOnly = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
        var groups = digitsOnly.match(/.{1,4}/g);
        e.target.value = groups ? groups.join(' ') : digitsOnly;
    });
}

var expireDateInput = document.getElementById('expireDate');
if (expireDateInput) {
    expireDateInput.addEventListener('input', function (e) {
        var value = e.target.value.replace(/\D/g, '');

        if (value.length >= 1) {
            var month = value.substring(0, 2);
            if (parseInt(month) > 12) {
                month = '12';
            } else if (parseInt(month) < 1 && value.length === 2) {
                month = '01';
            }
            value = month + value.substring(2);
        }

        if (value.length >= 3) {
            var year = value.substring(2, 4);
            var currentFullYear = new Date().getFullYear() % 100;
            if (parseInt(year) > currentFullYear + 7) {
                year = (currentFullYear + 7).toString();
            }
            value = value.substring(0, 2) + year;
        }

        if (value.length >= 2) {
            value = value.substring(0, 2) + '/' + value.substring(2, 4);
        }

        e.target.value = value;
    });
}

var cvvInput = document.getElementById('cvv');
if (cvvInput) {
    cvvInput.addEventListener('input', function (e) {
        var value = e.target.value.replace(/[^0-9]/g, '');
        if (value.length > 3) {
            value = value.substring(0, 3);
        }
        e.target.value = value;
    });
}


// ── Card payment validation + submit ────────────────────────────────────────
var payButton = document.getElementById('payButton');
if (payButton) {
    payButton.addEventListener('click', function (e) {
        e.preventDefault();

        var cardNumber = document.getElementById('cardNumber').value.trim();
        var expireDate = document.getElementById('expireDate').value.trim();
        var cvv = document.getElementById('cvv').value.trim();
        var name = document.getElementById('name').value.trim();
        var email = document.getElementById('email').value.trim();
        var hasError = false;

        document.getElementById('cardNumberError').innerText = "";
        document.getElementById('expireDateError').innerText = "";
        document.getElementById('cvvError').innerText = "";
        document.getElementById('nameError').innerText = "";
        document.getElementById('emailError').innerText = "";

        if (cardNumber.length < 19) {
            document.getElementById('cardNumberError').innerText = "Enter a valid card number.";
            hasError = true;
        }

        if (!/^\d{2}\/\d{2}$/.test(expireDate)) {
            document.getElementById('expireDateError').innerText = "Enter expiry in MM/YY format.";
            hasError = true;
        } else {
            var parts = expireDate.split('/');
            var expMonth = parseInt(parts[0]);
            var expYear = parseInt(parts[1]);
            var currentYear = new Date().getFullYear() % 100;
            var currentMonth = new Date().getMonth() + 1;

            if (expMonth < 1 || expMonth > 12) {
                document.getElementById('expireDateError').innerText = "Invalid month.";
                hasError = true;
            } else if (expYear < currentYear || (expYear === currentYear && expMonth < currentMonth)) {
                document.getElementById('expireDateError').innerText = "Card has expired.";
                hasError = true;
            }
        }

        if (cvv.length < 3 || cvv.length > 4) {
            document.getElementById('cvvError').innerText = "Enter a valid CVV.";
            hasError = true;
        }

        if (!name) {
            document.getElementById('nameError').innerText = "Please enter your name.";
            hasError = true;
        }

        if (!email.includes('@') || email.split('@')[1].length < 3 || email.split('.').length < 2) {
            document.getElementById('emailError').innerText = "Enter a valid email address.";
            hasError = true;
        }

        if (!hasError) {
            injectSnackInputs('cardSnackInputs');
            document.getElementById('paymentForm').submit();
        }
    });
}


// ── Cash payment validation + submit ────────────────────────────────────────
var cashPayButton = document.getElementById('cashPayButton');
if (cashPayButton) {
    cashPayButton.addEventListener('click', function (e) {
        e.preventDefault();

        var cashName = document.getElementById('cashName').value.trim();
        var cashEmail = document.getElementById('cashEmail').value.trim();
        var hasCashError = false;

        document.getElementById('cashNameError').innerText = "";
        document.getElementById('cashEmailError').innerText = "";

        if (!cashName) {
            document.getElementById('cashNameError').innerText = 'Please enter your name.';
            hasCashError = true;
        }

        if (!cashEmail.includes('@') || cashEmail.split('@')[1].length < 3 || cashEmail.split('.').length < 2) {
            document.getElementById('cashEmailError').innerText = "Enter a valid email address.";
            hasCashError = true;
        }

        if (!hasCashError) {
            injectSnackInputs('cashSnackInputs');
            document.getElementById('cashPayForm').submit();
        }
    });
}


// ── Tab switching between Card and Cash ─────────────────────────────────────
var cardTab = document.getElementById('cardTab');
var cashTab = document.getElementById('cashTab');
var cardSection = document.getElementById('cardSection');
var cashSection = document.getElementById('cashSection');

if (cardTab) {
    cardTab.addEventListener('click', function () {
        cardTab.classList.add('active');
        if (cashTab) { cashTab.classList.remove('active'); }
        if (cardSection) { cardSection.style.display = 'block'; }
        if (cashSection) { cashSection.style.display = 'none'; }
        document.getElementById('cashEmailError').innerText = "";
        document.getElementById('cashNameError').innerText = "";
    });
}

if (cashTab) {
    cashTab.addEventListener('click', function () {
        cashTab.classList.add('active');
        if (cardTab) { cardTab.classList.remove('active'); }
        if (cashSection) { cashSection.style.display = 'block'; }
        if (cardSection) { cardSection.style.display = 'none'; }
        document.getElementById('cardNumberError').innerText = "";
        document.getElementById('expireDateError').innerText = "";
        document.getElementById('cvvError').innerText = "";
        document.getElementById('nameError').innerText = "";
        document.getElementById('emailError').innerText = "";
    });
}

document.addEventListener('DOMContentLoaded', startTimer);
</script>
@endsection