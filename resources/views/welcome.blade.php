<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>ChxmpionChip</title>
    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> -->

    <script src="https://kit.fontawesome.com/947677835f.js" crossorigin="anonymous"></script>

    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css?ver=5.5.7' media='all' />

    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">

    <!-- <link href="{{ asset('css/mobiscroll.jquery.min.css') }}" rel="stylesheet"> -->
    <link href="{{ asset('css/picker.css') }}" rel="stylesheet">
    <!-- <script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script> -->


    <style type="text/css">
        @import url('https://fonts.googleapis.com/css2?family=Barlow+Semi+Condensed:wght@800&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700;900&display=swap');

        body {
            margin: 0;
        }

        @font-face {
            font-family: 'Barlow Semi Condensed';
            src: url('../fonts/BarlowSemiCondensed-ExtraBold.eot');
            src: url('../fonts/BarlowSemiCondensed-ExtraBold.eot?#iefix') format('embedded-opentype'),
                url('../fonts/BarlowSemiCondensed-ExtraBold.woff2') format('woff2'),
                url('../fonts/BarlowSemiCondensed-ExtraBold.woff') format('woff'),
                url('../fonts/BarlowSemiCondensed-ExtraBold.ttf') format('truetype'),
                url('../fonts/BarlowSemiCondensed-ExtraBold.svg#BarlowSemiCondensed-ExtraBold') format('svg');
            font-weight: bold;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: 'SF UI Display';
            src: url('../fonts/SFUIDisplay-Regular.eot');
            src: url('../fonts/SFUIDisplay-Regular.eot?#iefix') format('embedded-opentype'),
                url('../fonts/SFUIDisplay-Regular.woff2') format('woff2'),
                url('../fonts/SFUIDisplay-Regular.woff') format('woff'),
                url('../fonts/SFUIDisplay-Regular.ttf') format('truetype'),
                url('../fonts/SFUIDisplay-Regular.svg#SFUIDisplay-Regular') format('svg');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: 'sf_ui_displayregular';
            src: url('../fonts/sf-ui-display-regular-webfont.woff2') format('woff2'),
                url('../fonts/sf-ui-display-regular-webfont.woff') format('woff');
            font-weight: normal;
            font-style: normal;

        }
        body{
            background:#000;
        }
        p {
            color: #ffffff;
            margin: 0;
            padding: 5px 0px 0px;
        }

        .login-form form {
            padding:0px 0px 0px 0px;
            display: inline-block;
            width: 100%;
            min-width: 520px;
            -webkit-border-radius: 10px;
            -moz-border-radius: 10px;
            border-radius: 10px;
            text-align: left;
            -webkit-transition: all 0.4s ease;
            transition: all 0.4s ease;
            position: relative;
        }

        .login-form h2 {
            color: #fff;
            text-align: left !important;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 0px;
            margin-top: 0px;
        }

        .form-control,
        .btn {
            min-height: 38px;
            border-radius: 2px;
        }

        .btn {
            max-width: 160px;
            width: 100%;
            height: 50px;
            margin-top: 17px;
            background-color: #f6e54e;
            font-size: 16px;
            font-weight: 700;
            border-radius: 6px;
            font-family: 'Poppins', sans-serif;
            color: #000000;
            border: none;
        }

        /* .login-form {
            font-size: 15px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            background-position: center;
            background-size: cover;
            background-image: url(assets/img/login_new_back.png);
            background-attachment: fixed;
            position: absolute;
            min-height: 100%;
            width:100%;
            top:0px;
            left:0px;
        } */
        .blur-bg {
            background-position: top;
    background-size: 100% 70% !important;
    background-attachment: fixed;
    position: absolute;
    width: 100%;
    top: 0px;
    left: 0px;
    position: fixed;
    inset: 0px;
    z-index: -1;
    filter: blur(6px);
    opacity: 0.35;
    background-repeat: no-repeat;
    background-image: url(assets/img/login_new_back.png), -webkit-gradient(linear, left top, left bottom, from(#1c2e3a), to(#2a4046));
}
        .login-form {
    font-size: 15px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    background-position: center;
    background-size: 100% 100%;
    /* background-image: url(assets/img/login_new_back.png); */
    background-attachment: fixed;
    position: absolute;
    min-height: 100%;
    width: 100%;
    top: 0px;
    left: 0px;
    position: fixed;
    inset: 0px;
    z-index: -1;
}

        .form-content {
            position: relative;
            text-align: center;
            display: -webkit-flex;
            display: flex;
            -webkit-justify-content: center;
            justify-content: center;
            -webkit-align-items: center;
            align-items: center;
            padding:20px;
            flex-direction: column;
            width:37%;
        }

        .form-control {
            box-sizing: border-box;
            height: 47px;
            width: 100%;
            padding: 9px 20px;
            text-align: left;
            border: 0;
            outline: 0;
            border-radius: 6px;
            background-color: #fff;
            font-size: 15px;
            font-weight: 300;
            color: #8D8D8D;
            -webkit-transition: all 0.3s ease;
            transition: all 0.3s ease;
            margin-top:16px;
        }

        .form-check-label {
            margin-bottom: 0;
            font-size: 15px;
            font-weight: 300;
            color: #8D8D8D;
        }

        input#remember {
            margin-left: 1px;
            position: relative;
        }

        .logo-section {
            margin-bottom:3px;
        }

        .logo-section a img {
            width: 62px;
        }

        .mv-up {
            margin-top: -9px !important;
            margin-bottom: 8px !important;
        }

        .invalid-feedback {
            color: #ff606e;
        }

        label {
            color: #ffffff;
        }

        .valid-feedback {
            color: #2acc80;
        }

        .date {
            width: 100%;
            padding: 9px 20px;
            text-align: left;
            border: 0;
            outline: 0;
            border-radius: 6px;
            background-color: #fff;
            font-size: 15px;
            font-weight: 300;
            color: #8D8D8D;
            -webkit-transition: all 0.3s ease;
            transition: all 0.3s ease;
            margin-top: 16px;
        }

        form.requires-validation input,
        form.requires-validation select {
            max-width: 100%;
            width: 100%;
            font-family: 'Poppins', sans-serif;
            font-weight: 300;
            background-color:#343434 !important;
            box-sizing: border-box;
            height: 40px;
            padding: 0px 15px;
            border: none;
            color: #a1a5a8;
            border-radius: 6px;
            border: 0.3px solid #A6A9B8;
        }

        form.requires-validation .mt-3 {
            margin-top: 0px;
        }
        form.requires-validation .form-button.mt-3 button {
            max-width: 100%;
            width: 100%;
            transition: 0.5s linear;
            -webkit-transition: 0.5s linear;
            cursor: pointer;
            height:40px;
            margin-top:16px;
            /* background-color: #7d5e01; */
            background-color:#F6E54E;
            font-size: 20px;
            font-weight: 700;
            border-radius: 6px;
            color: #000;
            text-transform: uppercase;
        }


        

        .requires-validation .alert.alert-warning.alert-block {
            margin-top: 13px;
        }

        form.requires-validation .form-button.mt-3 button:hover {
            background-color: #000;
        }

        .requires-validation button.close {
            width: 25px;
            height: 25px;
            font-size: 20px;
            line-height: 25px;
            padding: 0px;
            border: none;
            border-radius: 3px;
            background-color: #f2ba18;
            color: #000;
            font-weight: 700;
        }

        .requires-validation .alert.alert-warning.alert-block strong {
            color: #f2ba18;
            font-weight: 400;
            padding-left: 3px;
        }

        .footer_social ul {
            text-align: center;
            margin: 30px 0px 0px 0px;
        }

        .footer_social ul li {
            display: inline-block;
            padding: 0px 10px;
        }

        .footer_social ul li a {
            color: #f6e54e;
            font-size: 22px;
        }

        .footer_social a i {
            color: #f6e54e;
        }

        .ul-footer-nav li a {
            text-decoration: none;
            font-size: 14px !important;
            color: #f6e54e !important;
            transition: 0.3s ease-in-out;
        }

        .ul-footer-nav li a:hover,
        .ul-footer-nav li a:focus {
            color: #ffffff !important;
        }

        .login-form.blur {
            filter: blur(4px);
        }

        .congrats_popup {
            width: 100%;
            height: 100%;
            position: fixed;
            top: 0px;
            left: 0px;
            z-index: 5;
        }

        .congrats_popup_inner {
            width: 410px;
            padding: 110px 20px 20px 20px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            -webkit-transform: translate(-50%, -50%);
            background-color: rgba(22, 14, 12, 0.98);
            text-align: center;
        }

        .congrats_popup_inner:before {
            content: '';
            display: block;
            position: absolute;
            top: 31px;
            left: 50%;
            width: 17px;
            height: 45px;
            border: solid #95783e;
            border-width: 0 3px 3px 0px;
            transform: rotate(45deg);
            -webkit-transform: rotate(45deg);
        }

        .congrats_popup_inner h3 {
            font-size: 46px;
            line-height: 1.1;
            color: #f9fffb;
            text-transform: uppercase;
            font-family: 'Barlow Semi Condensed';
            font-weight: 900;
            margin: 0px;
            text-shadow: 1px 2px 27px #f6e54e;
        }

        .congrats_popup_inner h4 {
            font-size: 26px;
            color: #fff;
            font-family: 'Barlow Semi Condensed';
            font-weight: 900;
            margin: 0px 0px;
            padding: 10px 0px;
            text-transform: uppercase;
            font-style: italic;
        }

        .congrats_popup_inner h4 span {
            color: #b99d58;
        }

        .congrats_popup_inner p {
            font-size: 14px;
            color: #fff;
            font-weight: 300;
            font-family: 'Poppins', sans-serif;
        }

        .logo-section .logotext {
            font-size: 24px;
            font-family: 'Barlow Semi Condensed';
            font-weight: 900;
            letter-spacing: 0.09em;
            text-shadow: 0px 0px 40px #dcaa19;
            padding: 6px 0px 5px 0px;
        }
        .mbsc-scroller-wheel-item.mbsc-ios.mbsc-ltr.mbsc-scroller-wheel-item-2d  > div > div {
            font-size: 0px !important;
            text-indent: -999px;
            color:transparent;
        }

        .logo-section p,
        .requires-validation .alert.alert-warning.alert-block {
            font-weight: 300;
            font-family: 'Poppins', sans-serif;
        }
        .logo-section p {
            font-size: 11px;
        }
        .form-group.checkbox_clm {
            margin: 16px 0px 0px 0px;
        }

        
        .form-group.checkbox_clm input {
            padding: 0;
            height: initial;
            width: initial;
            margin-bottom: 0;
            display: none;
            cursor: pointer;
        }

        .form-group.checkbox_clm label {
            position: relative;
            cursor: pointer;
            font-weight: 300;
            font-family: 'Poppins', sans-serif;
            font-size: 11px;
            display: flex;
            align-items: flex-start;
        }

        .form-group.checkbox_clm label:before {
            content: '';
            -webkit-appearance: none;
            background-color: transparent;
            border: 2px solid #fff;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05), inset 0px -15px 10px -12px rgba(0, 0, 0, 0.05);
            padding: 8px;
            display: inline-block;
            position: relative;
            vertical-align: middle;
            cursor: pointer;
            margin-right: 5px;
        }

        .form-group.checkbox_clm input:checked+label:after {
            content: '';
            display: block;
            position: absolute;
            top: 0px;
            left: 10px;
            width: 4px;
            height: 13px;
            border: solid #fff;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
            -webkit-transform: rotate(45deg);
        }

        .privacy_chmp {
            margin:0px 0px 5px 0px;
            text-align: left;
        }

        .privacy_chmp p,
        .privacy_chmp p a {
            font-size: 11px;
            color: #fff;
            font-weight: 300;
            font-family: 'Poppins', sans-serif;
        }

        .privacy_chmp p a {
            text-decoration: underline;
            transition: 0.5s linear;
            -webkit-transition: 0.5s linear;
        }

        .privacy_chmp p a:hover {
            color: #000;
        }

        form.requires-validation select option {
            background-color: #000;
        }

        a.close_popup i {
            color: #fff;
            font-size: 22px;
            position: absolute;
            top: 0px;
            right: 6px;
        }




        span.logotext {
            display: block;
            font-family: 'Barlow Semi Condensed', sans-serif;
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
            font-weight: 800;
        }

        .privacy_chmp p a:hover {
            color:#7d5e01;
        }

        .requires-validation .alert.alert-warning.alert-block {
            margin-top: 0px;
            font-size: 14px;
            margin: 3px 0px -5px 0px;
        }
        .mbsc-scroller-wheel-item.mbsc-ios.mbsc-ltr.mbsc-scroller-wheel-item-3d > div > div {
            font-size: 0px !important;
            text-indent: -999px;
            color: transparent;
        }
        

        @media screen and (max-width:767px) {
            .form-group.checkbox_clm label, .privacy_chmp p, .privacy_chmp p a{font-size:10px;}
            .blur-bg {
    background-size: 100% 50% !important;}
            .login-form form {
                width: 95%;
                min-width: auto;
                padding: 0px;
            }
            .privacy_chmp{
                text-align: center;
            }
            .form-content {
                padding: 0px;
                width: 90%;
            }

            .login-form {
                padding-top: 50px;
            }

            .logo-section .logotext {
                font-size: 24px;
                padding: 10px 0px 10px 0px;
            }

            .logo-section a img {
                width: 75px;
            }

            .form-group.checkbox_clm label {
                display: flex;
                align-items: flex-start;
            }

            form.requires-validation input,
            form.requires-validation select {
                height: 38px;
            }

            .form-group.checkbox_clm {
                margin: 15px 0px 0px 0px;
            }

            .login-form {
                padding: 0px 0px;
                height: initial;
            }

            .privacy_chmp {
                margin: 5px 0px 5px 0px;
            }

            form.requires-validation .form-button.mt-3 button {
                margin-top: 14px;
                font-size: 18px;
            }

            .congrats_popup_inner h3 {
                font-size: 24px;
            }

            .congrats_popup_inner h4 {
                font-size: 18px;
                padding: 5px 0px;
            }

            .congrats_popup_inner {
                width: 81%;
            }
            



        }
    </style>
</head>

<body>

    @if(session()->has('message'))<div class="login-form blur">@else<div class="blur-bg"></div><div class="login-form">@endif
            <div class="form-content">
                <div class="logo-section">
                    <a href="#">
                        <img src="assets/img/tyophy.png" alt="">
                    </a>
                    <span class="logotext">Become A Chxmpion.</span>
                    <p>Create your ChxmpionChip Profile and get <br /> early bird access to the new standard for sports betting.</p>
                    <!-- <p>Registered User :{{$users}}</p> -->
                </div>
                <form class="requires-validation" action="{{route('emailNotification')}}" method="POST" novalidate>
                    <!-- <h2>Register Today</h2> -->

                    @csrf
                    <div class="form-group">
                        <input class="form-control" type="text" name="name" placeholder="Name" value="{{ old('name') }}" required>
                    </div>
                    @if ($errors->has('name'))
                    <div class="alert alert-warning alert-block">
                        <strong>{{ $errors->first('name') }}</strong>
                    </div>
                    @endif

                    {{-- <div class="form-group">
                        <input class="form-control" type="text" name="last_name" placeholder="Last Name" value="{{ old('last_name') }}" required>
                    </div>
                    @if ($errors->has('last_name'))
                    <div class="alert alert-warning alert-block">
                        <strong>{{ $errors->first('last_name') }}</strong>
                    </div>
                    @endif --}}

                    {{-- <!-- <div class="form-group">
                               <input class="form-control" type="text" name="username" placeholder="Username" value="{{ old('username') }}" required>
                            </div>
                            @if ($errors->has('username'))
                            <div class="alert alert-warning alert-block"> 
                                <strong>{{ $errors->first('username') }}</strong>
                            </div>
                            @endif --> --}}


                    <div class="form-group">
                        <input class="form-control" type="email" name="email" placeholder="E-mail Address" value="{{ old('email') }}" required>
                    </div>
                    @if ($errors->has('email'))
                    <div class="alert alert-warning alert-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </div>
                    @endif


                    <!-- <div class="form-group">
                                <select class="form-control" name="gender" placeholder="gender" required>
                                    <option value="">Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Non Binary">Non Binary</option>
                                    <option value="Prefer not to answer">Prefer not to answer</option>
                                </select>
                            
                          </div>
                            @if ($errors->has('gender'))
                            <div class="alert alert-warning alert-block">
                                <strong>{{ $errors->first('gender') }}</strong>
                            </div>
                            @endif -->

                    <div class="col-md-12 mt-3">
                        <!-- <input class="form-control" type="date" name="Birth_date" placeholder="Birth date" value="{{ old('Birth_date') }}" required> -->
                        
                        <!-- <input class="form-control"  name="Birth_date" value="{{ old('Birth_date') }}" id="demo-mobile-picker-input" class="md-mobile-picker-input" placeholder="Birth date" /> -->
                        <input type="text"  name="Birth_date" class="form-control js-date-picker disableFuturedate" value="{{ old('Birth_date') }}" placeholder="Birth date">
                    </div>
                    @if ($errors->has('Birth_date'))
                    <div class="alert alert-warning alert-block">
                        <strong>{{ $errors->first('Birth_date') }}</strong>
                    </div>
                    @endif

                    <div class="form-group">
                        <input class="form-control" type="password" name="password" placeholder="Password" value="" required>
                    </div>
                    @if ($errors->has('password'))
                    <div class="alert alert-warning alert-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </div>
                    @endif


                    <div class="form-group">
                        <input class="form-control" type="password" name="confirm_password" placeholder="Confirm Password" value="" required>
                    </div>
                    @if ($errors->has('confirm_password'))
                    <div class="alert alert-warning alert-block">
                        <strong>{{ $errors->first('confirm_password') }}</strong>
                    </div>
                    @endif

                    <div class="form-group checkbox_clm">
                        <input type="checkbox" id="html">
                        <label for="html">Sign up for emails to get updates from ChxmpionChip, promotions and member benefits.</label>
                    </div>
                    <div class="form-group privacy_chmp">
                        <p>By signing up, you agree to ChxmpionChip's <a href="{{route('termOfUse')}}" target="_blank"> Terms of use </a> and <a href="{{route('privacyPolicy')}}" target="_blank"> Privacy Policy. </a> </p>
                    </div>
                    <div class="form-button mt-3">
                        <button id="submit" type="submit" class="btn btn-primary">Join Us</button>
                    </div>
                </form>


            </div>
        </div>


        @if(session()->has('message'))

        <div class="congrats_popup">
            <div class="congrats_popup_inner">
                <a href="javascript:void(0)" class="close_popup"><i class="fa fa-times" aria-hidden="true"></i></a>
                <h3>Congrats, <br> {{session()->get('message')}}! </h3>
                <h4> You are now a <span> #Chxmpion </span> </h4>
                <p>Thanks for signing up! You'll <br /> be hearing from us soon via email. In the <br /> meantime, stay
                    connected <br /> with us on social media. </p>

                <div class="footer_social">
                    <ul>

                        <li><a href="https://www.facebook.com/chxmpionchip" target="_blank"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
                        <li><a href="https://twitter.com/chxmpionchip" target="_blank"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
                        <li><a href="https://www.instagram.com/chxmpionchip/?hl=en" target="_blank"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
                        <li><a href="https://www.linkedin.com/company/chxmpionchip" target="_blank"><i class="fa fa-linkedin" aria-hidden="true"></i></a></li>
                        <li><a href="https://vm.tiktok.com/TTPdmb3U1c/" target="_blank"><i class="fab fa-tiktok"></i></a></li>
                    </ul>


                </div>
            </div>
        </div>

        @endif


        

<script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
<!-- <script type="text/javascript" src="{{ asset('js/mobiscroll.jquery.min.js') }}"></script> -->

<script type="text/javascript" src="{{ asset('js/picker.js') }}"></script>

<script>
    // $(function() {
    //     $("#datepicker").datepicker({
    //         dateFormat: "yy-mm-dd",
    //         maxDate: new Date,
    //         changeMonth: true,
    //         changeYear: true,
    //         yearRange: "1900:2021"
    //     });
    // });
    new Picker(document.querySelector('.js-date-picker'), {
  format: 'MMMM D, YYYY',
  headers: false,
  maxDate: new Date,


});
    $(document).ready(function() {
        $(".disableFuturedate").on("change", function(e){
            var now = new Date();
            var myDate = new Date( $(this).val() );
            if(myDate > now){
                alert("Future dates are not allowed!");
                return false;
            }
        });
        $(".close_popup").click(function() {
            $(".congrats_popup").fadeOut();
        });

        $('.close_popup').click(function() {
            $(".login-form").removeClass('blur');
        });

    });
</script>



<script>

    mobiscroll.setOptions({
        locale: mobiscroll.localeEn,  
        theme: 'ios',                
            themeVariant: 'light'
    });
    
    $(function () {
    
       
        $('#demo-mobile-picker-input').mobiscroll().datepicker({
            
            controls: ['date'],
            dateFormat: 'MMMM D, YYYY',
            touchUi: true             
        });
    
        var instance = $('#demo-mobile-picker-button').mobiscroll().datepicker({
            
            controls: ['date'],
            touchUi: true,           
            showOnClick: false,       
            showOnFocus: false,
        }).mobiscroll('getInst');
        
        instance.setVal(new Date(), true);
    
        $('#demo-mobile-picker-mobiscroll').mobiscroll().datepicker({
            
            controls: ['date'],
            touchUi: true
        });
    
        // Mobiscroll Date & Time initialization
        $('#demo-mobile-picker-inline').mobiscroll().datepicker({
            
            controls: ['date'],
            touchUi: true,           
            display: 'inline'
        });
    
        $('#show-mobile-date-picker').click(function () {
            instance.open();
            return false;
        });
    
    });
</script>





        <script>
            // $(function() {
            //     $("#datepicker").datepicker({
            //         dateFormat: "yy-mm-dd",
            //         maxDate: new Date,
            //         changeMonth: true,
            //         changeYear: true,
            //         yearRange: "1900:2021"
            //     });
            // });

            $(document).ready(function() {
                $(".close_popup").click(function() {
                    $(".congrats_popup").fadeOut();
                });

                $('.close_popup').click(function() {
                    $(".login-form").removeClass('blur');
                });

            });
        </script>




</body>

</html>