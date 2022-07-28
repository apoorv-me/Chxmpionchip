<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel Starter</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

    <!-- Styles -->
    <style>
        html, body {
            background-color: #f1f1f1;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }
        .full-height {
            height: 100vh;
        }
        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }
        .position-ref {
            position: relative;
        }
        input {
            padding: 10pt;
            width: 60%;
            font-size: 15pt;
            border-radius: 5pt;
            border: 1px solid lightgray;
            margin: 10pt;
        }
        .form-container {
            display: flex;
            flex-direction: column;
            width: 60%;
            align-items: center;
            margin: 20pt;
            border: 1px solid lightgray;
            padding: 20pt;
            border-radius: 5pt;
            background: white;
        }
        button {
            border-radius: 5pt;
            padding: 10pt 14pt;
            background: white;
            border: 1px solid gray;
            font-size: 14pt;
            margin: 20pt;
        }
        button:hover {
            background: lightgray;
        }
    </style>
</head>
<body>
<div class="flex-center position-ref full-height">
    <form class="form-container" action="{{ route('password.reset')}}" method="POST">
        <h2>Reset Password?</h2>
        @csrf
        <input type="email" name="email" placeholder="Enter email" value="{{request()->get('email')}}">
        @if ($errors->has('email'))
          <span class="invalid-feedback" role="alert">
           <strong>{{ $errors->first('email') }}</strong>
          </span>
        @endif
        <input type="password" name="password" placeholder="Enter new password">
        @if ($errors->has('password'))
          <span class="invalid-feedback" role="alert">
           <strong>{{ $errors->first('password') }}</strong>
          </span>
        @endif
        <input type="password" name="password_confirmation" placeholder="Confirm new password">
        @if ($errors->has('password_confirmation'))
          <span class="invalid-feedback" role="alert">
           <strong>{{ $errors->first('password_confirmation') }}</strong>
          </span>
        @endif

        <input hidden name="passwordToken" placeholder="token" value="{{request()->get('token')}}">

        <button type="submit">Submit</button>

        @if(session()->has('msg'))
          <div class="alert alert-success">
            {{ session()->get('msg') }}
        </div>
        @endif
    </form>
</div>
</body>
</html>