<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<title>{{env('APP_NAME')}}</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
<style>

.login-form form {border: 3px solid #fff;padding: 40px;display: inline-block;width: 100%;min-width: 540px;-webkit-border-radius: 10px;-moz-border-radius: 10px;border-radius: 10px;text-align: left;-webkit-transition: all 0.4s ease;transition: all 0.4s ease;position: relative;}
.login-form h2 {color: #fff;text-align: left !important;font-size: 28px;font-weight: 600;margin-bottom: 0px;margin-top: 0px;}
.form-control, .btn {min-height: 38px;border-radius: 2px;}
.btn {max-width: 160px;width: 100%;height: 50px;margin-top: 17px;background-color: #f6e54e;font-size: 16px;font-weight: 700;border-radius: 6px;font-family: 'Poppins', sans-serif;color: #000000;border: none;}
.login-form{font-size: 15px;display: flex;flex-direction: column;justify-content: center;align-items: center;text-align: center;min-height: 100vh;background-position: center;background-size: cover;background-image: url(../assets/img/login_back.jpg);}
 .form-content {position: relative;text-align: center;display: -webkit-box;display: -moz-box;display: -ms-flexbox;display: -webkit-flex;display: flex;-webkit-justify-content: center;justify-content: center;-webkit-align-items: center;align-items: center;padding: 60px;flex-direction: column;}
.form-control{box-sizing: border-box;height: 47px;width: 100%;padding: 9px 20px;text-align: left;border: 0;outline: 0;border-radius: 6px;background-color: #fff;font-size: 15px;font-weight: 300;color: #8D8D8D;-webkit-transition: all 0.3s ease;transition: all 0.3s ease;margin-top: 16px;}
.form-check-label {margin-bottom: 0;font-size: 15px;font-weight: 300;color: #8D8D8D;}
input#remember {margin-left: 1px;position: relative;}
.logo-section a img{width: 130px;}
@media screen and (max-width:767px){
    .login-form form{width: 100%;min-width: auto;padding:15px;}
    .form-content {padding: 0px;width: 90%;}
}
</style>
</head>
<body>
<div class="login-form">
    <div class="form-content">
        <div class="logo-section">
            <a href="#"><img src="../images/logo.png" alt=""></a>
        </div>
        <form action="{{ route('adminLoginPost') }}" method="post">
            {!! csrf_field() !!}
            <h2 class="text-center">{{env('APP_NAME')}}</h2>
            @if(\Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ \Session::get('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
            </div>
            @endif
            {{ \Session::forget('success') }}
            @if(\Session::get('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ \Session::get('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
            </div>
            @endif       
            <div class="form-group">
                <input type="email" class="form-control" name="email" placeholder="Email" required="required">
                @if ($errors->has('email'))
                <span class="help-block font-red-mint">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
                @endif
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password" required="required">
                @if ($errors->has('password'))
                <span class="help-block font-red-mint">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
                @endif
            </div>
        
            <div class="form-group">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                    <label class="form-check-label" for="remember">
                                            {{ __('Remember Me') }}
                    </label>
            </div>
                                
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block">Log in</button>
            </div>
        </form>
    </div>
    
</div>
</body>
</html>