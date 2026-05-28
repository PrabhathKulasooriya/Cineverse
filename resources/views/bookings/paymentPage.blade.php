@extends('moviePage_include.main')
@section('pageSpecificStyles')

<script src="https://kit.fontawesome.com/e8fa2e31b4.js" crossorigin='anonymous'></script>
<link rel="stylesheet" href="{{ asset('css/payment.css') }}">

@endsection

@section('pageSpecificContent')

<div class="payment-main">

    <div class="header payement-methods-header">

        @if(Auth::check() &&( Auth::user()->user_role_iduser_role == 3))
        <button class="toggle-method-btn active" id="cashTab">Cash</button>
        @elseif(Auth::check() &&( Auth::user()->user_role_iduser_role == 1))
        <button class="toggle-method-btn active" id="cashTab">Cash</button>
        <button class="toggle-method-btn active" id="cardTab">Card Payments</button>
        <button class="toggle-method-btn" id="paypalTab">PayPal</button>
        @else
        <button class="toggle-method-btn active" id="cardTab">Card Payments</button>
        <button class="toggle-method-btn" id="paypalTab">PayPal</button>
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

  @if(Auth::check() &&( Auth::user()->user_role_iduser_role == 1 || Auth::user()->user_role_iduser_role == 3))
    
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
                            <input type="email" id="cashEmail" name="email" placeholder="your@email.com" oninput="this.value = this.value.toLowerCase();">
                            <small class="text-danger" id="cashEmailError"></small> 
                            </span>

                        </div>

                        <div class="total-amount">
                            <div class="label">Total Amount</div>
                            <div class="amount">LKR.{{ $bookingData['amount'] }}</div>
                        </div>

                        <div class="btn-container">
                            <button type="button" class="pay-button btn-pay" id="cashPayButton">
                                <i class="fa fa-money" aria-hidden="true"></i>
                                Pay
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
            
  @else
  {{-- Card Payment --}}
    <div class="payment-container" id="cardSection">
          
        <div class="payment-header">
            <h5>Card Payment</h5>
            <span class="card-elements">
                <img src="assets/images/logo/visa.png" height="50" alt="logo">
                <img src="assets/images/logo/mastercard.png" height="50" alt="logo">
                <img src="assets/images/logo/amex.png" height="50" alt="logo">
            </span>
        </div>

        <div class="payment-content">
            
            <form action="{{ route('manualPayment') }}" method="POST" class="payment-form" id="paymentForm" >
                {{csrf_field()}}
                <input type="hidden" name="paymentMethod" value="CARD">
                <div class="form-element">
                    <span class="child">
                    <label>Card Number <span class="required">*</span></label>
                    <input type="text" id="cardNumber" name="cardNumber" placeholder="1234 5678 9012 3456" maxlength="19">
                    <input type="hidden" id="bookingId" name="bookingId" value="{{$bookingData['booking_id']}}">
                    <small class="text-danger" id="cardNumberError"></small>
                    </span>
                </div>
                
                <div class="exp-cvv-row">
                    <div class="form-element form-element-expire ">
                        <span class="child">
                        <label>Expire Date <span class="required">*</span></label>
                        <input type="text" id="expireDate" name="expireDate" placeholder="MM/YY" maxlength="5">
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
                    @endif >
                    <small class="text-danger" id="nameError"></small>
                    </span>
                </div>
                <div class="form-element">
                    <span class="child">
                    <label>Email Address <span class="required">*</span></label>
                    <input type="email" id="email" name="email" placeholder="your@email.com"
                    @if(Auth::check())
                        value="{{ Auth::user()->email }}"
                    @endif
                    oninput="this.value = this.value.toLowerCase();">
                    <small class="text-danger" id="emailError"></small>
                    </span>

                </div>
                
                <div class="total-amount">
                    <div class="label">Total Amount</div>
                    <div class="amount" id="totalAmount">LKR.{{$bookingData['amount']}} </div>
                </div>

                <div class="btn-container">
                
                    <button type="submit" class="pay-button btn-pay" id="payButton">
                        <i class="fa fa-credit-card-alt" aria-hidden="true"></i>
                        Pay Now
                    </button>

                    <a href="{{route('cancel')}}">
                    <button type="button" class="pay-button btn-cancel" id="cancelButton">
                        <i class="fa fa-trash-o" aria-hidden="true"></i> Cancel Booking</button>
                    </a>
                </div>
            </form>
            
        </div>
    </div>


    {{-- PayPal Payment --}}
    <div class="payment-container" id="paypalSection" style="display: none;">
        <div class="payment-header">
            <h5>PayPal Checkout</h5>
        </div>

        <div class="payment-content">
            <form id="paypalForm" method="POST" action="{{ route('manualPayment') }}" class="payment-form">
                @csrf

                <input type="hidden" name="bookingId" value="{{ $bookingData['booking_id'] }}">
                <input type="hidden" name="paymentMethod" value="PAYPAL">

                <div class="form-element">
                    <span class="child">
                    <label>Name <span class="required">*</span></label>
                    <input type="text" id="paypalName" name="name" placeholder="enter your name"
                    @if(Auth::check())
                        value="{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}"
                    @endif >
                    <small class="text-danger" id="paypalNameError"></small> 
                    </span>
                </div>
                <div class="form-element">
                    <span class="child">
                    <label>Email Address <span class="required">*</span></label>
                    <input type="email" id="paypalEmail" name="email" placeholder="your@email.com"
                    @if(Auth::check())
                        value="{{ Auth::user()->email }}"
                    @endif
                    oninput="this.value = this.value.toLowerCase();">
                    <small class="text-danger" id="paypalEmailError"></small> 
                    </span>

                </div>

                <div class="total-amount">
                    <div class="label">Total Amount</div>
                    <div class="amount">LKR.{{ $bookingData['amount'] }}</div>
                </div>

                <div class="btn-container">
                    <button type="button" class="pay-button btn-pay" id="openPaypalModal">
                        <i class="fa fa-paypal" aria-hidden="true"></i>
                        Pay Now
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
    <div class="overlay" id="paypalOverlay"></div>

    
    <div class="paypal-modal bg-light " id="paypalModal" >
        <h3 >PayPal Checkout</h3>
        <p><strong>Total:</strong> LKR.{{ $bookingData['amount'] }}</p>
        <p>Pay with your PayPal Wallet</p>

        <button type="button" class="pay-button btn-pay" id="approvePaypal" style="width: 100%;">
            <i class="fa fa-paypal"></i> Approve Payment
        </button>
        <button type="button" class="pay-button btn-cancel" id="cancelPaypal" style="width: 100%; margin-top: 10px;">
            <i class="fa fa-times"></i> Cancel
        </button>
    </div>

</div>



@endsection

@section('pageSpecificScript')

<script>
    
// Format card number input
document.getElementById('cardNumber')?.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
    let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
    e.target.value = formattedValue;
});

// Format expiry date input
document.getElementById('expireDate')?.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    
    // Validate first two digits (month: 01-12)
    if (value.length >= 1) {
        let month = value.substring(0, 2);
        if (parseInt(month) > 12) {
            month = '12';
        } else if (parseInt(month) < 1 && value.length === 2) {
            month = '01';
        }
        value = month + value.substring(2);
    }
    
    // Validate last two digits (year: current year or future, simplified to 24-31)
    if (value.length >= 3) {
        let year = value.substring(2, 4);
        let currentYear = new Date().getFullYear();
        let currentFullYear = currentYear % 100;
        if (parseInt(year) > currentFullYear+7) {
            year = (currentFullYear+7).toString();
        } else if (parseInt(year) < 1 && value.length === 4) {
            year = currentFullYear;
        } 
        value = value.substring(0, 2) + year;
    }


    // Format with slash
    if (value.length >= 2) {
        value = value.substring(0, 2) + '/' + value.substring(2, 4);
    }
    
    e.target.value = value;
});

// Only allow 3 numbers for CVV
document.getElementById('cvv')?.addEventListener('input', function(e) {
    let value = e.target.value.replace(/[^0-9]/g, '');
    if (value.length > 3) {
        value = value.substring(0, 3);
    }
    e.target.value = value;
});

</script>

<script>
    const payBtn = document.getElementById('payButton');
    const paymentForm = document.getElementById('paymentForm');

    payBtn?.addEventListener('click', function (e) {
    e.preventDefault();

    let cardNumber = document.getElementById('cardNumber').value.trim();
    let expireDate = document.getElementById('expireDate').value.trim();
    let cvv = document.getElementById('cvv').value.trim();
    let name = document.getElementById('name').value.trim(); 
    let email = document.getElementById('email').value.trim();

    let hasError = false;

    // Reset error messages
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
        const currentYear = new Date().getFullYear() % 100;
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

    if (name === "") {
        document.getElementById('nameError').innerText = "Please enter your name.";
        hasError = true;
    }

    if (!email.includes('@') || email.split('@')[1].length < 3 || email.split('.').length < 2) { 
        document.getElementById('emailError').innerText = "Enter a valid email address.";
        hasError = true;
    }

    if (!hasError) {
        paymentForm.submit();
    }
});


    const cardTab = document.getElementById('cardTab');
    const paypalTab = document.getElementById('paypalTab');
    const cashTab = document.getElementById('cashTab');
    const cardSection = document.getElementById('cardSection');
    const paypalSection = document.getElementById('paypalSection');
    const cashSection = document.getElementById('cashSection');

    cardTab?.addEventListener('click', () => {
        cardTab.classList.add('active');
        paypalTab?.classList.remove('active');
        if(cashTab){
            cashTab.classList.remove('active');
        }
        cardSection.style.display = 'block';
        paypalSection.style.display = 'none';
        if(cashSection){
            cashSection.style.display = 'none';
        }
        // Clear PayPal and Cash specific errors when switching back to card
        document.getElementById('paypalNameError').innerText = "";
        document.getElementById('paypalEmailError').innerText = "";
        document.getElementById('cashEmailError').innerText = "";
        document.getElementById('cashNameError').innerText = "";

    });

    paypalTab?.addEventListener('click', () => {
        paypalTab.classList.add('active');
        cardTab?.classList.remove('active');
        if(cashTab){
            cashTab.classList.remove('active');
        }
        
        paypalSection.style.display = 'block';
        cardSection.style.display = 'none';
        if(cashSection){
            cashSection.style.display = 'none';
        }
        // Clear Card and Cash specific errors when switching to PayPal
        document.getElementById('cardNumberError').innerText = "";
        document.getElementById('expireDateError').innerText = "";
        document.getElementById('cvvError').innerText = "";
        document.getElementById('nameError').innerText = ""; 
        document.getElementById('emailError').innerText = "";
        document.getElementById('cashEmailError').innerText = "";
        document.getElementById('cashNameError').innerText = "";
    });

    if(cashTab){
        cashTab.addEventListener('click', () => {
            cashTab.classList.add('active');
            cardTab?.classList.remove('active');
            paypalTab?.classList.remove('active');
            cashSection.style.display = 'block';
            cardSection.style.display = 'none';
            paypalSection.style.display = 'none';
            // Clear Card and PayPal specific errors when switching to Cash
            document.getElementById('cardNumberError').innerText = "";
            document.getElementById('expireDateError').innerText = "";
            document.getElementById('cvvError').innerText = "";
            document.getElementById('nameError').innerText = ""; 
            document.getElementById('emailError').innerText = "";
            document.getElementById('paypalNameError').innerText = "";
            document.getElementById('paypalEmailError').innerText = "";
        });
    }

    const openPaypalModalBtn = document.getElementById('openPaypalModal');
    const paypalOverlay = document.getElementById('paypalOverlay');
    const paypalModal = document.getElementById('paypalModal');
    const approvePaypalBtn = document.getElementById('approvePaypal');
    const cancelPaypalBtn = document.getElementById('cancelPaypal');
    const paypalForm = document.getElementById('paypalForm'); 

    openPaypalModalBtn?.addEventListener('click', () => {
        const name = document.getElementById('paypalName').value.trim();
        const email = document.getElementById('paypalEmail').value.trim();
        let hasPaypalError = false;

        // Reset PayPal errors
        document.getElementById('paypalNameError').innerText = "";
        document.getElementById('paypalEmailError').innerText = "";
        
        if (!name) {
            document.getElementById('paypalNameError').innerText ='Please enter your name for PayPal checkout.';
            hasPaypalError = true;
        }

        if (!email.includes('@') || email.split('@')[1].length < 3 || email.split('.').length < 2) { 
            document.getElementById('paypalEmailError').innerText = "Enter a valid email address.";
            hasPaypalError = true;
        }

        if (!hasPaypalError) {
            paypalOverlay.style.display = 'block';
            paypalModal.style.display = 'block';
        }
    });

    cancelPaypalBtn?.addEventListener('click', () => {
        paypalOverlay.style.display = 'none';
        paypalModal.style.display = 'none';
    });

    approvePaypalBtn?.addEventListener('click', () => {
        paypalForm.submit();
    });

    // Cash payment validation and submission
    const cashPayButton = document.getElementById('cashPayButton');
    const cashPayForm = document.getElementById('cashPayForm');

    cashPayButton?.addEventListener('click', (e) => {
        e.preventDefault();
        
        const cashName = document.getElementById('cashName').value.trim();
        const cashEmail = document.getElementById('cashEmail').value.trim();
        let hasCashError = false;

        // Reset cash errors
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
            cashPayForm.submit();
        }
    });
</script>

@endsection