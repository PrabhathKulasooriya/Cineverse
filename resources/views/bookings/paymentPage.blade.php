@extends('moviePage_include.main')
@section('pageSpecificStyles')
<script src="https://kit.fontawesome.com/e8fa2e31b4.js" crossorigin='anonymous'></script>
<link rel="stylesheet" href="{{ asset('css/payment.css') }}">
@endsection

@section('pageSpecificContent')

<div class="payment-main">

    {{-- ── LEFT PANEL: Movie Info + Snacks ── --}}
    <div class="payment-left">

        {{-- Movie Details --}}
        <div class="movie-summary">
            <img src="{{ URL::asset('movieImages/' . ($bookingData['movie_image'] ?? '')) }}"
                 alt="movie" class="movie-summary-img">
            <div class="movie-summary-info">
                <h5>{{ $bookingData['movie_name'] ?? '' }}</h5>
                <p><i class="fa fa-calendar"></i> {{ \Carbon\Carbon::parse($bookingData['show_date'] ?? '')->format('d-m-Y') }}</p>
                <p><i class="fa fa-clock-o"></i> {{ \Carbon\Carbon::parse($bookingData['show_time'] ?? '')->format('h:i A') }}</p>
            </div>
        </div>

        {{-- Countdown --}}
        <div class="countdown-banner" id="countdownBanner">
            <i class="fa fa-clock-o"></i>
            Seat hold expires in: <span class="countdown-time" id="countdown">15:00</span>
            <small>Complete payment before time runs out!</small>
        </div>

        {{-- Snack Selection --}}
        <div class="snack-section">
            <h6><i class="fa fa-shopping-basket"></i> Add Snacks <small>(optional)</small></h6>

            <div class="snack-grid">
                @forelse($snacks as $snack)
                <div class="snack-card">
                    <img src="{{ URL::asset('snackImages/' . $snack->image) }}"
                         alt="{{ $snack->name }}" class="snack-image">
                    <div class="snack-name">{{ $snack->name }}</div>
                    <div class="snack-variants">
                        @foreach($snack->variants as $variant)
                        <div class="variant-row">
                            <span class="size-badge">{{ $variant->size }}</span>
                            <span class="variant-price">Rs.{{ number_format($variant->price, 2) }}</span>
                            <div class="qty-control">
                                <button type="button" class="qty-btn qty-minus"
                                        data-id="v{{ $variant->idsnack_variants }}">−</button>
                                <span class="qty-display"
                                      id="qty_v{{ $variant->idsnack_variants }}">0</span>
                                <button type="button" class="qty-btn qty-plus"
                                        data-id="v{{ $variant->idsnack_variants }}"
                                        data-price="{{ $variant->price }}"
                                        data-name="{{ $snack->name }} ({{ $variant->size }})">+</button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @empty
                <p style="color:#aaa; font-size:13px;">No snacks available.</p>
                @endforelse
            </div>
        </div>

    </div>

    {{-- ── RIGHT PANEL: Payment ── --}}
    <div class="payment-right">

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

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible text-center floating-alert" role="alert">
                <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        {{-- Hidden snack inputs injected here before form submit --}}
        <div id="snackInputsContainer"></div>

        @if(Auth::check() && (Auth::user()->user_role_iduser_role == 3 || Auth::user()->user_role_iduser_role == 1))
        {{-- Cash Payment --}}
        <div class="payment-container" id="cashSection" style="display: block;">
            <div class="payment-header">
                <h5>Counter Checkout</h5>
            </div>
            <div class="payment-content">
                <form id="cashPayForm" method="POST" action="{{ route('manualPayment') }}" class="payment-form">
                    @csrf
                    <input type="hidden" name="bookingId" value="{{ $bookingData['booking_id'] }}">
                    <input type="hidden" name="paymentMethod" value="CASH">
                    <div id="cashSnackInputs"></div>

                    <div class="form-element">
                        <span class="child">
                            <label>Name <span class="required">*</span></label>
                            <input type="text" id="cashName" name="name" placeholder="enter your name">
                            <small class="text-danger" id="cashNameError"></small>
                        </span>
                    </div>
                    <div class="form-element">
                        <span class="child">
                            <label>Email Address <span class="required">*</span></label>
                            <input type="email" id="cashEmail" name="email" placeholder="your@email.com"
                                   oninput="this.value = this.value.toLowerCase();">
                            <small class="text-danger" id="cashEmailError"></small>
                        </span>
                    </div>

                    {{-- Order Summary --}}
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
                        <a href="{{ route('cancel') }}">
                            <button type="button" class="pay-button btn-cancel">
                                <i class="fa fa-trash-o" aria-hidden="true"></i> Cancel Booking
                            </button>
                        </a>
                    </div>
                </form>
            </div>
        </div>
        @endif

        @if(!Auth::check() || Auth::user()->user_role_iduser_role != 3)
        {{-- Card Payment --}}
        <div class="payment-container" id="cardSection"
             style="display: {{ (Auth::check() && Auth::user()->user_role_iduser_role == 1) ? 'none' : 'block' }};">
            <div class="payment-header">
                <h5>Card Payment</h5>
                <span class="card-elements">
                    <img src="assets/images/logo/visa.png" height="50" alt="visa">
                    <img src="assets/images/logo/mastercard.png" height="50" alt="mastercard">
                    <img src="assets/images/logo/amex.png" height="50" alt="amex">
                </span>
            </div>
            <div class="payment-content">
                <form action="{{ route('manualPayment') }}" method="POST" class="payment-form" id="paymentForm">
                    @csrf
                    <input type="hidden" name="paymentMethod" value="CARD">
                    <input type="hidden" id="bookingId" name="bookingId" value="{{ $bookingData['booking_id'] }}">
                    <div id="cardSnackInputs"></div>

                    <div class="form-element">
                        <span class="child">
                            <label>Card Number <span class="required">*</span></label>
                            <input type="text" id="cardNumber" name="cardNumber"
                                   placeholder="1234 5678 9012 3456" maxlength="19">
                            <small class="text-danger" id="cardNumberError"></small>
                        </span>
                    </div>

                    <div class="exp-cvv-row">
                        <div class="form-element form-element-expire">
                            <span class="child">
                                <label>Expire Date <span class="required">*</span></label>
                                <input type="text" id="expireDate" name="expireDate"
                                       placeholder="MM/YY" maxlength="5">
                                <small class="text-danger" id="expireDateError"></small>
                            </span>
                        </div>
                        <div class="form-element form-element-cvv">
                            <span class="child child-cvv">
                                <label>CVV <span class="required">*</span></label>
                                <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="4">
                                <small class="text-danger" id="cvvError"></small>
                            </span>
                        </div>
                    </div>

                    <div class="form-element">
                        <span class="child">
                            <label>Name <span class="required">*</span></label>
                            <input type="text" id="name" name="name" placeholder="enter your name"
                                @if(Auth::check())
                                    value="{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}"
                                @endif>
                            <small class="text-danger" id="nameError"></small>
                        </span>
                    </div>
                    <div class="form-element">
                        <span class="child">
                            <label>Email Address <span class="required">*</span></label>
                            <input type="email" id="email" name="email" placeholder="your@email.com"
                                @if(Auth::check()) value="{{ Auth::user()->email }}" @endif
                                oninput="this.value = this.value.toLowerCase();">
                            <small class="text-danger" id="emailError"></small>
                        </span>
                    </div>

                    {{-- Order Summary --}}
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
                        <a href="{{ route('cancel') }}">
                            <button type="button" class="pay-button btn-cancel">
                                <i class="fa fa-trash-o" aria-hidden="true"></i> Cancel Booking
                            </button>
                        </a>
                    </div>
                </form>
            </div>
        </div>
        @endif

    </div>{{-- end payment-right --}}

</div>{{-- end payment-main --}}

@endsection

@section('pageSpecificScript')
<script>

// ── Countdown ────────────────────────────────────────────────────────────────
let secondsLeft = {{ $secondsRemaining }};
let timerInterval;
const ticketAmount = {{ $bookingData['amount'] }};

function formatTime(s) {
    const m   = Math.floor(s / 60).toString().padStart(2, '0');
    const sec = (s % 60).toString().padStart(2, '0');
    return `${m}:${sec}`;
}

function onExpired() {
    clearInterval(timerInterval);
    document.getElementById('countdown').textContent = '00:00';
    document.getElementById('countdownBanner').classList.add('expired');
    document.querySelectorAll('.qty-btn, #payButton, #cashPayButton').forEach(el => el.disabled = true);

    Swal.fire({
        title: 'Seat Hold Expired',
        text: 'Your seats have been released. Please start a new booking.',
        icon: 'error',
        confirmButtonText: 'Go Home',
        confirmButtonColor: '#dc3545',
        allowOutsideClick: false,
    }).then(() => {
        window.location.href = "{{ route('home') }}";
    });
}

function startTimer() {
    const el     = document.getElementById('countdown');
    const banner = document.getElementById('countdownBanner');
    if (secondsLeft <= 0) { onExpired(); return; }
    el.textContent = formatTime(secondsLeft);

    timerInterval = setInterval(() => {
        secondsLeft--;
        el.textContent = formatTime(secondsLeft);
        if (secondsLeft <= 120) banner.classList.add('warning');
        if (secondsLeft <= 0)   { onExpired(); return; }
        if (secondsLeft % 60 === 0) syncWithServer();
    }, 1000);
}

function syncWithServer() {
    fetch("{{ route('timeRemaining') }}")
        .then(r => r.json())
        .then(data => {
            if (data.expired) { onExpired(); return; }
            secondsLeft = data.seconds;
        })
        .catch(() => {});
}

// ── Snack qty controls ───────────────────────────────────────────────────────
const selectedSnacks = {};

document.querySelectorAll('.qty-plus').forEach(btn => {
    btn.addEventListener('click', function () {
        const id    = this.dataset.id;
        const price = parseFloat(this.dataset.price);
        const name  = this.dataset.name;
        const el    = document.getElementById('qty_' + id);
        let   qty   = parseInt(el.textContent) + 1;
        if (qty > 10) return;
        el.textContent = qty;
        selectedSnacks[id] = { name, price, qty };
        updateOrderSummary();
    });
});

document.querySelectorAll('.qty-minus').forEach(btn => {
    btn.addEventListener('click', function () {
        const id  = this.dataset.id;
        const el  = document.getElementById('qty_' + id);
        let   qty = parseInt(el.textContent) - 1;
        if (qty < 0) return;
        el.textContent = qty;
        if (selectedSnacks[id]) selectedSnacks[id].qty = qty;
        updateOrderSummary();
    });
});

function updateOrderSummary() {
    let snackTotal = 0;
    for (const item of Object.values(selectedSnacks)) {
        if (item.qty > 0) snackTotal += item.price * item.qty;
    }

    const grandTotal = ticketAmount + snackTotal;

    // Update card form summary
    const cardSnackRow = document.getElementById('cardSnackSummaryRow');
    if (cardSnackRow) {
        cardSnackRow.style.display = snackTotal > 0 ? 'flex' : 'none';
        document.getElementById('cardSnackTotalDisplay').textContent = 'Rs. ' + snackTotal.toFixed(2);
        document.getElementById('cardGrandTotal').textContent = 'Rs. ' + grandTotal.toFixed(2);
    }

    // Update cash form summary
    const cashSnackRow = document.getElementById('cashSnackSummaryRow');
    if (cashSnackRow) {
        cashSnackRow.style.display = snackTotal > 0 ? 'flex' : 'none';
        document.getElementById('cashSnackTotalDisplay').textContent = 'Rs. ' + snackTotal.toFixed(2);
        document.getElementById('cashGrandTotal').textContent = 'Rs. ' + grandTotal.toFixed(2);
    }
}

// Inject snack hidden inputs into a form before submit
function injectSnackInputs(containerId) {
    const container = document.getElementById(containerId);
    container.innerHTML = '';

    let snackTotal = 0;

    for (const [id, item] of Object.entries(selectedSnacks)) {
        if (item.qty <= 0) continue;
        snackTotal += item.price * item.qty;

        const variantId = id.replace('v', '');
        const input = document.createElement('input');
        input.type  = 'hidden';
        input.name  = `snacks[${variantId}]`;
        input.value = item.qty;
        container.appendChild(input);
    }

    // Add grand total as hidden input
    const totalInput = document.createElement('input');
    totalInput.type  = 'hidden';
    totalInput.name  = 'grandTotal';
    totalInput.value = (ticketAmount + snackTotal).toFixed(2);
    container.appendChild(totalInput);
}

// ── Card payment ─────────────────────────────────────────────────────────────
document.getElementById('cardNumber')?.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
    e.target.value = value.match(/.{1,4}/g)?.join(' ') || value;
});

document.getElementById('expireDate')?.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length >= 1) {
        let month = value.substring(0, 2);
        if (parseInt(month) > 12) month = '12';
        else if (parseInt(month) < 1 && value.length === 2) month = '01';
        value = month + value.substring(2);
    }
    if (value.length >= 3) {
        let year = value.substring(2, 4);
        let currentFullYear = new Date().getFullYear() % 100;
        if (parseInt(year) > currentFullYear + 7) year = (currentFullYear + 7).toString();
        value = value.substring(0, 2) + year;
    }
    if (value.length >= 2) value = value.substring(0, 2) + '/' + value.substring(2, 4);
    e.target.value = value;
});

document.getElementById('cvv')?.addEventListener('input', function(e) {
    let value = e.target.value.replace(/[^0-9]/g, '');
    if (value.length > 3) value = value.substring(0, 3);
    e.target.value = value;
});

document.getElementById('payButton')?.addEventListener('click', function(e) {
    e.preventDefault();

    let cardNumber = document.getElementById('cardNumber').value.trim();
    let expireDate = document.getElementById('expireDate').value.trim();
    let cvv        = document.getElementById('cvv').value.trim();
    let name       = document.getElementById('name').value.trim();
    let email      = document.getElementById('email').value.trim();
    let hasError   = false;

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
        const [month, year] = expireDate.split('/').map(Number);
        const currentYear  = new Date().getFullYear() % 100;
        const currentMonth = new Date().getMonth() + 1;
        if (month < 1 || month > 12) {
            document.getElementById('expireDateError').innerText = "Invalid month.";
            hasError = true;
        } else if (year < currentYear || (year === currentYear && month < currentMonth)) {
            document.getElementById('expireDateError').innerText = "Card has expired.";
            hasError = true;
        }
    }
    if (cvv.length < 3 || cvv.length > 4) {
        document.getElementById('cvvError').innerText = "Enter a valid CVV (3 or 4 digits).";
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

// ── Cash payment ─────────────────────────────────────────────────────────────
document.getElementById('cashPayButton')?.addEventListener('click', function(e) {
    e.preventDefault();

    const cashName  = document.getElementById('cashName').value.trim();
    const cashEmail = document.getElementById('cashEmail').value.trim();
    let hasCashError = false;

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

// ── Tab switching ────────────────────────────────────────────────────────────
const cardTab     = document.getElementById('cardTab');
const cashTab     = document.getElementById('cashTab');
const cardSection = document.getElementById('cardSection');
const cashSection = document.getElementById('cashSection');

cardTab?.addEventListener('click', () => {
    cardTab.classList.add('active');
    cashTab?.classList.remove('active');
    if (cardSection) cardSection.style.display = 'block';
    if (cashSection) cashSection.style.display = 'none';
    document.getElementById('cashEmailError').innerText = "";
    document.getElementById('cashNameError').innerText = "";
});

cashTab?.addEventListener('click', () => {
    cashTab.classList.add('active');
    cardTab?.classList.remove('active');
    if (cashSection) cashSection.style.display = 'block';
    if (cardSection) cardSection.style.display = 'none';
    document.getElementById('cardNumberError').innerText = "";
    document.getElementById('expireDateError').innerText = "";
    document.getElementById('cvvError').innerText = "";
    document.getElementById('nameError').innerText = "";
    document.getElementById('emailError').innerText = "";
});

// ── Init ─────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', startTimer);
</script>
@endsection