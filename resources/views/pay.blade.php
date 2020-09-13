<!DOCTYPE html>
<html>

<head>
    <title>Qruz Wallet</title>
    <link rel="shortcut icon" type="image/png" href="/favicon.ico">

    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- INCLUDE SESSION.JS JAVASCRIPT LIBRARY -->
    <script src="https://api.vapulus.com:1338/app/session/script?appId={{config('custom.valulus_app_id')}}"></script>
    <!-- APPLY CLICK-JACKING STYLING AND HIDE CONTENTS OF THE PAGE -->
    <style id="antiClickjack">
        body {
            display: none !important;
        }
    </style>
    <style>
        body {
            background-color: #edeff0 !important;
        }
        .placeholder {
            width: 75px;
            height: 75px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 2px solid transparent;
        }
        .form-control {
            border: 0 !important;
            background-color: transparent !important;
            border-bottom: 1px solid #ddd !important;
            border-radius: 0 !important;
            padding-left: 1px !important;
        }
        .form-control:focus, .btn:focus {
            outline: none !important;
            box-shadow: none !important;
        }
        .btn-primary, .btn-primary:hover, .btn-primary:active, .btn-primary:focus {
            background-color: #000 !important;
            border-color: #000 !important;
            color: #fff !important;
        }
        .btn:disabled {
            background-color: #e8ebed !important;
            border-color: #e8ebed !important;
            color: #bbb !important;
        }
        input[type=text] {
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            appearance: none !important;
        }
        .spinner {
            border-radius: 50%;
            border-width: 3px;
            border-style: solid;
            border-image: initial;
            animation: circleSpinner 1s linear 0s infinite normal none running;
        }
        .loading {
            animation: circleSpinner 1s linear 0s infinite normal none running;
        }
        .spinner-dark {
            margin: 50px auto;
            width: 30px;
            height: 30px;
            border-color: #000 #e1e4e5 #e1e4e5;
        }
        @keyframes circleSpinner {
            0% {
                transform: rotate(0deg);
                -webkit-transform: rotate(0deg);
            }
            100% {
                transform: rotate(359deg);
                -webkit-transform: rotate(359deg);
            }
        }
    </style>
</head>

<body>
    <nav class="py-3 bg-dark text-center text-white">
        <h4 class="jumbotron-heading mb-1"><span class="font-weight-light">Qruz</span> <span class="font-weight-bold">Wallet</span></h4>
        <p class="mb-0">Thanks for trusting us</p>
    </nav>
    <!-- CREATE THE HTML FOR THE PAYMENT PAGE -->
    <div class="container mt-4 pt-2">
        <div class="row justify-content-center">
            <div class="col-md-4" id="card-content">
                <div id="loader">
                    <div class='spinner spinner-dark'></div>
                    <p class="text-muted text-center" id="loaderStatus">Initializing..</p>
                </div>
                <div class="card border-0" id="cardForm">
                    <div class="card-body">
                        <div id="feedback"></div>
                        <div class="form-row">
                            <input type="hidden" id="token" value="{{ app('request')->input('token') }}">
                            <input type="hidden" id="payable_id" value="{{ app('request')->input('payable_id') }}">
                            <input type="hidden" id="payable_type" value="{{ app('request')->input('payable_type') }}">
                            <div class="form-group col-12">
                                <label for="cardNumber" class="mb-0 font-weight-bold">Card Number</label>
                                <input type="text" id="cardNumber" class="form-control" value="" readonly  />
                            </div>
                            <div class="form-group col-4">
                                <label for="cardMonth" class="mb-0 font-weight-bold">Exp. Month</label>
                                <input type="text" id="cardMonth" class="form-control" placeholder="MM" value="" autocomplete="off" />
                            </div>
                            <div class="form-group col-4">
                                <label for="cardYear" class="mb-0 font-weight-bold">Exp. Year</label>
                                <input type="text" id="cardYear" class="form-control" placeholder="YYYY" value="" autocomplete="off" />
                            </div>
                            <div class="form-group col-4">
                                <label for="cardCVC" class="mb-0 font-weight-bold">CVC</label>
                                <input type="text" id="cardCVC" class="form-control" value="" readonly />
                            </div>
                            <div class="form-group col-12">
                                <label for="amount" class="mb-0 font-weight-bold">Amount</label>
                                <input type="text" placeholder="EGP" id="amount" class="form-control" value="" autocomplete="off" />
                            </div>
                            <button class="btn btn-primary btn-block btn-lg" id="payButton" onclick="pay();">Add to my wallet</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <!-- JAVASCRIPT FRAME-BREAKER CODE TO PROVIDE PROTECTION AGAINST IFRAME CLICK-JACKING -->
    <script type="text/javascript">
        const token = $("#token").val();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Authorization': 'Bearer '+token
            }
        });
        $("#cardForm").hide();
        // $("#amount").on('keyup', function() {
        //     $("#payAmount").text($(this).val());
        // })
        // Your code to run since DOM is loaded and ready
        if(window.PaymentSession){
            PaymentSession.configure({
                fields: {
                    // ATTACH HOSTED FIELDS IDS TO YOUR PAYMENT PAGE FOR A CREDIT CARD
                    card: {
                        cardNumber: "cardNumber",
                        securityCode: "cardCVC",
                        expiryMonth: "cardMonth",
                        expiryYear: "cardYear"
                    }
                },
                callbacks: {
                    initialized: function (err, response) {
                        $("#cardForm").show();
                        $("#loader").hide();
                    },
                    formSessionUpdate: function (err,response) {
                        // HANDLE RESPONSE FOR UPDATE SESSION
                        if (response.statusCode) {
                            if (200 == response.statusCode) {
                                console.log("Session updated with data: " + response.data.sessionId);
                                $("#loaderStatus").text('Payment processing...');
                                $.ajax({
                                    type:'POST',
                                    url:'/api/user/pay',
                                    data: { 
                                        session_id: response.data.sessionId, 
                                        payable_id: $("#payable_id").val(), 
                                        payable_type: $("#payable_type").val(),
                                        amount: $("#amount").val()
                                    },
                                    success: function(data) {
                                        $("#loader").hide();
                                        if (data.statusCode === 200) {
                                            $("#card-content").html('<div class="text-center my-4"><div class="placeholder rounded-circle bg-success"><svg viewBox="0 0 512 512" width="35" height="35" fill="#fff"><path d="M504.502,75.496c-9.997-9.998-26.205-9.998-36.204,0L161.594,382.203L43.702,264.311c-9.997-9.998-26.205-9.997-36.204,0 c-9.998,9.997-9.998,26.205,0,36.203l135.994,135.992c9.994,9.997,26.214,9.99,36.204,0L504.502,111.7 C514.5,101.703,514.499,85.494,504.502,75.496z"/></svg></div><h4 class="text-success font-weight-bold mb-2 mt-4">Successfull Payment</h4><p class="text-success"><span class="font-weight-bold">'+$("#amount").val()+' EGP</span> has been added to your wallet</p></div>')
                                        } else {
                                            $("#feedback").html('<div class="mb-3 alert alert-danger border-0">'+data.message+'</div>');
                                            $('#cardForm').show();
                                        }
                                    }
                                });
                            } else if (201 == response.statusCode) {
                                $("#loader").hide();
                                $("#feedback").html('<div class="mb-3 alert alert-danger border-0">Something went wrong! Please try again</div>');
                                $('#cardForm').show();
                                if (response.message) {
                                    var field = response.message.indexOf('valid')
                                    field = response.message.slice(field + 5, response.message.length);
                                    console.log(field + " is invalid or missing.");
                                    $("#feedback").html('<div class="mb-3 alert alert-danger border-0">'+field + ' is invalid or missing.</div>')
                                }
                            } else {
                                $("#feedback").html('<div class="mb-3 alert alert-danger border-0">Something went wrong! Please try again</div>');
                                $('#cardForm').show();
                            }
                        }
                    }
                }                
            });
        } else {
            alert('Fail to get app/session/script !\n\nPlease check if your appId added in session script tag in head section?')
        }
        function pay() {
            // UPDATE THE SESSION WITH THE INPUT FROM HOSTED FIELDS
            PaymentSession.updateSessionFromForm();
            $("#cardForm").hide();
            $("#loader").show();
            $("#loaderStatus").text('Validating your input...');
        }
    </script>

</body>
</html>