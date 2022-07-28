<!DOCTYPE html>
<html>
<head>
<title>Contact Us</title>
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
		    <!-- <button class="navbar-toggler" type="button" onclick="openNav()">
		      <i class="fa-solid fa-align-justify"></i>
		    </button> -->
		    <div class="collapse navbar-collapse" id="navbarSupportedContent">
		      <ul class="navbar-nav flex-right">
		        <!-- <li class="nav-item">
		          <a class="nav-link" href="{{url('/')}}">Home</a>
		        </li>
		        <li class="nav-item">
		          <a class="nav-link" aria-current="page" href="{{url('about-us')}}">About Us</a>
		        </li>
		        <li class="nav-item">
		          <a class="nav-link" href="{{url('privacy-policy')}}">Privacy Policy</a>
		        </li>
		        <li class="nav-item">
		          <a class="nav-link" href="{{url('term-of-use')}}">Terms of use</a>
		        </li> -->
		        <li class="nav-item">
		          <a class="nav-link {{$class}}" href="{{url('contact-us')}}">Contact Us</a>
		        </li>
		      </ul>
		    </div>

		    <!-- <div id="mySidenav" class="sidenav">
		    	<div class="sidebardiv">
				<a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
				  <a href="{{url('about-us')}}">About Us</a>
				  <a href="{{url('privacy-policy')}}">Privacy Policy</a>
				  <a href="{{url('term-of-use')}}">Term of Use</a>
				  <a href="{{url('contact-us')}}" class="{{$class}}">Contact Us</a>
		    	</div>			  
			</div> -->
		  </div>
		</nav>
		<figure class="banner">
		<img src="{{ asset('images/bg.jpg') }}" alt="bg">
		  <figcaption>Contact Us</figcaption>
		</figure>
	</header>

	<section class="content-section">
		<div class="container">
			<div class="row">
				<div class="col-lg-10 mx-auto">
					
						<div class="getin-main-sec">
							<div class="row">
							<div class="col-lg-5 col-sm-12">
								<div class="getintouch-sec">
									<h1>Get In Touch</h1>
									<p>Feel free to get in touch with us, We are always open to discussing new creative ideas to be part of your vision.</p>
									<div class="mail-sec">
										<ul>
											<li>
												<a href="mailto:info@chxmpionchip.com">
													<span><i class="fa-solid fa-envelope"></i></span>
													<b>Mail us</b>
													<small>Info@chxmpionchip.com</small>
												</a>
											</li>
											<!-- <li>
												<a href="tel:1212121212">
													<span><i class="fa-solid fa-phone"></i></span>
													<b>Call us</b>
													<small>1212121212</small>
												</a>
											</li> -->
										</ul>
									</div>
								</div>
							</div>
							<div class="col-lg-7 col-sm-12">
									<div class="row">
											<h4>We'd love to hear from you!</h4>
									</div>
									<div class="row input-container">
												@if(session()->has('message'))
													<div class="alert alert-success">
														{{ session()->get('message') }}
													</div>
												@endif
										<form method="post" action="{{route('contactUsProcess')}}">
											@csrf
											<div class="col-xs-12">
												<div class="styled-input wide">
													<input type="text" name="name" required />
													<label>Name</label> 
												</div>
											</div>
											<div class="col-xs-12">
												<div class="styled-input wide">
													<input type="email" name="email" required />
													<label>Email</label> 
												</div>
											</div>
											<div class="col-xs-12">
												<div class="styled-input wide">
													<textarea required name="description"></textarea>
													<label>Message</label>
												</div>
											</div>
											<div class="col-xs-12">
												<button class="btn-lrg submit-btn" type="submit">Send Message</button>
											</div>
											
										</form>

												
									</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"></script>
	<script defer src="{{ asset('js/all.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('js/custom.js') }}"></script>

</body>

</html>