<!DOCTYPE html>
<html dir="rtl">

<head>
  <!-- Basic -->
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <!-- Mobile Metas -->
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <!-- Site Metas -->
  <meta name="keywords" content="" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <link rel="shortcut icon" href="{{asset('layouts/'.$resturant->tamplate_number.'/images/favicon.png')}}" >

  <title> {{$resturant->name}} </title>

  <!-- bootstrap core css -->
  <link rel="stylesheet" type="text/css" href="{{asset('layouts/'.$resturant->tamplate_number.'/css/bootstrap.css')}}" />

  <!--owl slider stylesheet -->
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
  <!-- nice select  -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-nice-select/1.1.0/css/nice-select.min.css" integrity="sha512-CruCP+TD3yXzlvvijET8wV5WxxEh5H8P4cmz0RFbKK6FlZ2sYl3AEsKlLPHbniXKSrDdFewhbmBK5skbdsASbQ==" crossorigin="anonymous" />
  <!-- font awesome style -->
  <link href="{{asset('layouts/'.$resturant->tamplate_number.'/css/font-awesome.min.css')}}" rel="stylesheet" />

  <!-- Custom styles for this template -->
  <link href="{{asset('layouts/'.$resturant->tamplate_number.'/css/style.css')}}" rel="stylesheet" />

  <!-- responsive style -->
  <link href="{{asset('layouts/'.$resturant->tamplate_number.'/css/responsive.css')}}" rel="stylesheet" />

  <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

  @vite('resources/css/app.css' , 'resources/js/app.js')

</head>

<body class="sub_page">

  <script>
    window.resturant = @json($resturant);
    window.categories = @json($categories);
    window.food = @json($food);
    window.tableNumber = {{$tableNumber}} ; 
  </script>

  <div id="app" ></div>
  <!-- jQery -->
  <script src="http://127.0.0.1:8000/layouts/1/js/jquery-3.4.1.min.js"></script>

  <!-- isotope js -->
  <script src="https://unpkg.com/isotope-layout@3.0.4/dist/isotope.pkgd.min.js"></script>


  <!-- custom js -->
  <script src="http://127.0.0.1:8000/layouts/1/js/custom.js"></script>

  @vite('resources/js/app.js')
</body>
</html>


