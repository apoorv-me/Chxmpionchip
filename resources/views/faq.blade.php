<!DOCTYPE html>
<html>
<head>
<title>FAQ</title>
  <meta charset="UTF-8">
  <meta name="description" content="Free Web tutorials">
  <meta name="keywords" content="HTML, CSS, JavaScript">
  <meta name="author" content="John Doe">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="{{ asset('css/style.css') }}">
</head>

<body>
	<header>
		<nav class="navbar navbar-expand-lg nav-color">
		  <div class="container-fluid">
		    <a class="navbar-brand" href="{{url('/')}}">
            <img class="imglogo" src="{{ asset('images/logo.png') }}">
		    </a>
		    <div class="collapse navbar-collapse" id="navbarSupportedContent">
		      <ul class="navbar-nav flex-right">
		        <li class="nav-item">
		          <a class="nav-link {{$class}}" href="{{url('faq')}}">FAQ</a>
		        </li>
		      </ul>
		    </div>
		  </div>
		</nav>
		<figure class="banner">
        <img src="{{ asset('images/bg.jpg') }}" alt="bg">
		  <figcaption>FAQ</figcaption>
		</figure>
	</header>

	<section class="content-section">
		<div class="container">
		 <div class="terms-privacy">
			@if($data)
			 {!! html_entity_decode($data['content']) !!}
			@endif
		 </div>
		</div>
	</section>

	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
	<script defer src="{{ asset('js/all.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('js/custom.js') }}"></script>
</body>

</html>