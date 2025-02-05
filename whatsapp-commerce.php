<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KreactiveRock - | WhatsApp Conversational Commerce</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <!-- seore -->
    <link href="assets/css/seore/bootstrap.min.css" rel="stylesheet" media="screen">
	<!-- SlickNav Css -->
	<link href="assets/css/seore/slicknav.min.css" rel="stylesheet">
	<!-- Swiper Css -->
	<link rel="stylesheet" href="assets/css/seore/swiper-bundle.min.css">
	<!-- Font Awesome Icon Css-->
	<link href="assets/css/seore/all.css" rel="stylesheet" media="screen">
	<!-- Animated Css -->
	<link href="assets/css/seore/animate.css" rel="stylesheet">
	<!-- Mouse Cursor Css File -->
	<link rel="stylesheet" href="assets/css/seore/mousecursor.css">
	<!-- Main Custom Css -->


    <!-- Icons Css -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css">
    <!-- favicon -->
    <link href="assets/images/favicon.ico" rel="shortcut icon">
    <!-- Swiper Css -->
    <link href="assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet">
    <!-- Main Css -->
    <link href="assets/css/style.css" rel="stylesheet" type="text/css">
    <link href="assets/css/seore/custom.css" rel="stylesheet" media="screen">

</head>

<body>

    <!-- Navbar Start -->
    <nav class="navbar_ fixed top-0 start-0 end-0 z-999 transition-all duration-500 py-5 items-center shadow-md lg:shadow-none [&.is-sticky]:bg-white group [&.is-sticky]:shadow-md bg-white lg:bg-transparent"
        id="navbar">
        <div class="container">

            <div class="flex lg:flex-nowrap flex-wrap items-center">

                <a class='flex items-center' href='./index'>
                    <img src="assets/images/logo.svg" class="h-auto lg:w-[150px] flex">
                </a>

                <div class="lg:hidden flex items-center ms-auto px-2.5">
                    <button class="hs-collapse-toggle" type="button" id="hs-unstyled-collapse"
                        data-hs-collapse="#navbarCollapse">
                        <i data-lucide="menu" class="h-8 w-8 text-black"></i>
                    </button>
                </div>

                <div class="navigation hs-collapse transition-all duration-300 lg:basis-auto basis-full grow hidden items-center justify-center lg:flex mx-auto overflow-hidden mt-6 lg:mt-0 nav-light"
                    id="navbarCollapse">
                    <ul class="navbar-nav flex-col lg:flex-row gap-y-2 flex lg:items-center justify-center" id="navbar-navlist">
                        <li
                            class="nav-item mx-1.5 transition-all text-dark lg:text-black group-[&.is-sticky]:text-dark all duration-300 hover:text-primary [&.active]:!text-primary group-[&.is-sticky]:[&.active]:text-primary">
                            <a class="nav-link inline-flex items-center text-sm lg:text-base font-medium py-0.5 px-2 capitalize"
                                href="./index">Home</a>
                        </li>

                        <div class="group/menu mx-1.5 ">
                            <button type="button" class="transition-all text-dark lg:text-black hover:primary font-medium py-0.5 px-2 rounded-md text-sm lg:text-base inline-flex items-center">
                                Services
                                <svg class="ml-2 -mr-1 w-5 h-5 text-gray-400 group-hover:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
    
                            <div class="!hidden group-hover/menu:!block absolute left-1/2 -translate-x-1/2 mt-0 py-2 w-48 rounded-md shadow-lg bg-gradient-to-br from-gray-800 via-gray-800 to-gray-900">
                                <a href="mobile-texting" class="block px-4 py-3 text-sm text-white hover:text-primary">Mobile Messaging</a>
                                <a href="whatsapp-commerce" class="block px-4 py-3 text-sm text-white hover:text-primary">WhatsApp Commerce</a>
                                <a href="programmatic-marketing" class="block px-4 py-3 text-sm text-white hover:text-primary">Programmatic Marketing</a>
                                <a href="social-media-management" class="block px-4 py-3 text-sm text-white hover:text-primary">Social Media CRM</a>
                            </div>
                        </div>

                        <li
                            class="nav-item mx-1.5 transition-all text-dark lg:text-black group-[&.is-sticky]:text-dark duration-300 hover:text-primary [&.active]:!text-primary group-[&.is-sticky]:[&.active]:text-primary">
                            <a class="nav-link inline-flex items-center text-sm lg:text-base font-medium py-0.5 px-2 capitalize"
                                href="about">About</a>
                        </li>

                        <li
                            class="nav-item mx-1.5 transition-all text-dark lg:text-black group-[&.is-sticky]:text-dark duration-300 hover:text-primary [&.active]:!text-primary group-[&.is-sticky]:[&.active]:text-primary">
                            <a class="nav-link inline-flex items-center text-sm lg:text-base font-medium py-0.5 px-2 capitalize"
                                href="contact">Contact</a>
                        </li>

                        <li
                            class="nav-item mx-1.5 transition-all text-dark lg:text-black group-[&.is-sticky]:text-dark duration-300 hover:text-primary [&.active]:!text-primary group-[&.is-sticky]:[&.active]:text-primary">
                            <a class="nav-link inline-flex items-center text-sm lg:text-base font-medium py-0.5 px-2 capitalize"
                                href="blog">Blog</a>
                        </li>
                    </ul>
                </div>

                <div class="ms-auto shrink hidden lg:inline-flex gap-2">
                    <a href="./admin/view/login"
                        class="py-2 px-6 inline-flex items-center gap-2 rounded-md text-base text-white bg-primary hover:bg-primaryDark transition-all duration-500 font-medium">
                        <span class="hidden sm:block">Get started</span>
                        <i data-lucide="arrow-right" class="h-4 w-4 fill-white/40"></i>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- About Section Start -->
    <div class="about-us pt-40 pb-28">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-5">
                    <!-- About Us Image Start -->
                    <div class="about-image">
                        <div class="about-img rounded-3xl bg-green-700/40">
                            <figure>
                                <img src="assets/images/products/whatsapp-mock.png" alt="">
                            </figure>
                        </div>

                        <!-- Company Experience Box Start -->
                        <!-- <div class="company-experience">
                            <div class="icon-box">
                                <img src="images/icon-experience.svg" alt="">
                            </div>
                            <div class="company-experience-content">
                                <h3><span class="counter">15</span>+</h3>
                                <p>years of experience</p>
                            </div>
                        </div> -->
                        <!-- Company Experience Box End -->
                    </div>
                    <!-- About Us Image End -->
                </div>

                <div class="col-lg-7">
                    <!-- About Content Start -->
                    <div class="about-content">
                        <!-- Section Title Start -->
                        <div class="section-title">
                            <h3 class="wow fadeInUp">WhatsApp Conversational Commerce</h3>
                            <h2 class="text-anime-style-2 font-bold" data-cursor="-opaque">Smart Ways to Connect with Customers: <span>WhatsApp Commerce</span></h2>
                            <p class="wow fadeInUp" data-wow-delay="0.25s">The best solution for WhatsApp Business API integration. Increase customer satisfaction and lower your service costs with automation.</p>
                        </div>
                        <!-- Section Title End -->

                        <!-- About Content Body Start -->
                        <div class="about-content-body wow fadeInUp" data-wow-delay="0.5s">
                            <ul>
                                <li>Instant messaging</li>
                                <li>Mass broadcasting</li>
                                <li>expertise and experience</li>
                                <li>dedicated support</li>
                                <li>transparent reporting</li>
                                <li>continuous improvement</li>
                            </ul>
                        </div>
                        <!-- About Content Body End -->

                        <!-- About Content Footer Start -->
                        <div class="about-content-footer wow fadeInUp" data-wow-delay="0.75s">
                            <a href="./admin/view/login" class="btn-default">get started</a>
                        </div>
                        <!-- About Content Footer End -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- About Section End -->

    <!-- Client Start -->
    <section class="py-20 bg-gray-50">
        <div class="container relative">
            <div class="">
                <div class="text-center max-w-xl mx-auto">
                    <h3 class="text-3xl md:text-4xl/tight font-semibold">Join Host of Business</h3>
                </div>
            </div>

            <div class="grid md:grid-cols-6 grid-cols-2 justify-center gap-[30px] mt-14 grayscale">
                <div class="mx-auto py-4">
                    <img src="assets/images/clients/meta.png" class="h-10 bg-cover" alt="">
                </div>

                <div class="mx-auto py-4">
                    <img src="assets/images/clients/whatsapp.png" class="h-10" alt="">
                </div>

                <div class="mx-auto py-4">
                    <img src="assets/images/clients/gushup.png" class="h-10" alt="">
                </div>

                <div class="mx-auto py-4">
                    <img src="assets/images/clients/flutterwave.png" class="h-10" alt="">
                </div>

                <div class="mx-auto py-4">
                    <img src="assets/images/clients/paystack.png" class="h-10" alt="">
                </div>

            </div>
        </div>
    </section>
    <!-- Client Start -->

    <!-- Services Start -->
    <section id="services" class="py-20">
        <div class="container">
            <div class="max-w-2xl mx-auto text-center">
                <h2 class="text-3xl md:text-4xl/tight font-semibold text-black mt-4">Where you need WhatsApp Commerce</h2>
                <p class="text-base font-medium mt-4 text-muted">Provide an integrated shopping experience with WhatsApp's native catalog and cart function.</p>
            </div>

            <div
                class="grid lg:grid-cols-4 md:grid-cols-2 grid-cols-1 gap-x-3 gap-y-6 md:gap-y-12 lg:gap-y-24 md:pt-20 pt-12">

                <div class="text-center">
                    <div class="flex items-center justify-center">
                        <div
                            class="items-center justify-center flex bg-primary/10 rounded-[49%_80%_40%_90%_/_50%_30%_70%_80%] h-20 w-20 border">
                            <i data-lucide="menu" class="h-10 w-10 text-primary"></i>
                        </div>
                    </div>
                    <h1 class="text-xl font-semibold pt-4">Customer Support</h1>
                    <p class="text-base text-gray-600 mt-2">Provide instant support to your customers through WhatsApp, addressing and resolve issues quickly.</p>
                </div>

                <div class="text-center">
                    <div class="flex items-center justify-center">
                        <div
                            class="items-center justify-center flex bg-primary/10 rounded-[49%_80%_40%_90%_/_50%_30%_70%_80%] h-20 w-20 border">
                            <i data-lucide="lightbulb" class="h-10 w-10 text-primary"></i>
                        </div>
                    </div>
                    <h1 class="text-xl font-semibold pt-4">Mass Messaging</h1>
                    <p class="text-base text-gray-600 mt-2">Broadcast messages to a large audience keeping them informed about new products, and offers.</p>
                </div>

                <div class="text-center">
                    <div class="flex items-center justify-center">
                        <div
                            class="items-center justify-center flex bg-primary/10 rounded-[49%_80%_40%_90%_/_50%_30%_70%_80%] h-20 w-20 border">
                            <i data-lucide="bar-chart-big" class="h-10 w-10 text-primary"></i>
                        </div>
                    </div>
                    <h1 class="text-xl font-semibold pt-4">Digital Marketing</h1>
                    <p class="text-base text-gray-600 mt-2">Benchmark your performance against competitors, identify
                        strengths.</p>
                </div>

                <div class="text-center">
                    <div class="flex items-center justify-center">
                        <div
                            class="items-center justify-center flex bg-primary/10 rounded-[49%_80%_40%_90%_/_50%_30%_70%_80%] h-20 w-20 border">
                            <i data-lucide="codepen" class="h-10 w-10 text-primary"></i>
                        </div>
                    </div>
                    <h1 class="text-xl font-semibold pt-4">Sales & Marketing</h1>
                    <p class="text-base text-gray-600 mt-2">Run targeted marketing campaigns on WhatsApp to drive sales and increase customer engagement.</p>
                </div>

                <div class="text-center">
                    <div class="flex items-center justify-center">
                        <div
                            class="items-center justify-center flex bg-primary/10 rounded-[49%_80%_40%_90%_/_50%_30%_70%_80%] h-20 w-20 border">
                            <i data-lucide="shield" class="h-10 w-10 text-primary"></i>
                        </div>
                    </div>
                    <h1 class="text-xl font-semibold pt-4">Appointment Booking & Confirmation</h1>
                    <p class="text-base text-gray-600 mt-2">Allow customers to book appointments and receive confirmations directly, ensuring they never miss an appointment.</p>
                </div>

                <div class="text-center">
                    <div class="flex items-center justify-center">
                        <div
                            class="items-center justify-center flex bg-primary/10 rounded-[49%_80%_40%_90%_/_50%_30%_70%_80%] h-20 w-20 border">
                            <i data-lucide="rocket" class="h-10 w-10 text-primary"></i>
                        </div>
                    </div>
                    <h1 class="text-xl font-semibold pt-4">Surveys & Questionnaires</h1>
                    <p class="text-base text-gray-600 mt-2">Conduct surveys and collect feedback from customers via WhatsApp to gain valuable insights and improve your services.</p>
                </div>

                <div class="text-center">
                    <div class="flex items-center justify-center">
                        <div
                            class="items-center justify-center flex bg-primary/10 rounded-[49%_80%_40%_90%_/_50%_30%_70%_80%] h-20 w-20 border">
                            <i data-lucide="layers-2" class="h-10 w-10 text-primary"></i>
                        </div>
                    </div>
                    <h1 class="text-xl font-semibold pt-4">Order & Tracking</h1>
                    <p class="text-base text-gray-600 mt-2">Send order confirmations and tracking updates to customers through WhatsApp, keeping them informed about their purchases.</p>
                </div>

                <div class="text-center">
                    <div class="flex items-center justify-center">
                        <div
                            class="items-center justify-center flex bg-primary/10 rounded-[49%_80%_40%_90%_/_50%_30%_70%_80%] h-20 w-20 border">
                            <i data-lucide="webcam" class="h-10 w-10 text-primary"></i>
                        </div>
                    </div>
                    <h1 class="text-xl font-semibold pt-4">Conversational Texting</h1>
                    <p class="text-base text-gray-600 mt-2">Engage in real-time, personalized conversations with customers on WhatsApp to build stronger relationships and enhance customer experience.</p>
                </div>
            </div>

        </div>
    </section>
    <!-- Services End -->

    <!-- Our Pricing Section Start -->
    <div class="our-pricing">
        <div class="container">
            <div class="row section-row">
                <div class="col-lg-12">
                    <!-- Section Title Start -->
                    <div class="section-title">
                        <h2 class="text-anime-style-2 font-bold" data-cursor="-opaque">Find a <span>perfect plan</span></h2>
                    </div>
                    <!-- Section Title End -->
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4">
                    <!-- Pricing Item Start -->
                    <div class="pricing-item wow fadeInUp">
                        <!-- Pricing Header Start -->
                        <div class="pricing-header">
                            <!-- <p class="text-muted">Basic features for up to 10 users.</p> -->
                            <svg class="w-[36px] h-[36px] text-green-500 mx-auto" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path fill="currentColor" fill-rule="evenodd" d="M12 4a8 8 0 0 0-6.895 12.06l.569.718-.697 2.359 2.32-.648.379.243A8 8 0 1 0 12 4ZM2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10a9.96 9.96 0 0 1-5.016-1.347l-4.948 1.382 1.426-4.829-.006-.007-.033-.055A9.958 9.958 0 0 1 2 12Z" clip-rule="evenodd"/>
                                <path fill="currentColor" d="M16.735 13.492c-.038-.018-1.497-.736-1.756-.83a1.008 1.008 0 0 0-.34-.075c-.196 0-.362.098-.49.291-.146.217-.587.732-.723.886-.018.02-.042.045-.057.045-.013 0-.239-.093-.307-.123-1.564-.68-2.751-2.313-2.914-2.589-.023-.04-.024-.057-.024-.057.005-.021.058-.074.085-.101.08-.079.166-.182.249-.283l.117-.14c.121-.14.175-.25.237-.375l.033-.066a.68.68 0 0 0-.02-.64c-.034-.069-.65-1.555-.715-1.711-.158-.377-.366-.552-.655-.552-.027 0 0 0-.112.005-.137.005-.883.104-1.213.311-.35.22-.94.924-.94 2.16 0 1.112.705 2.162 1.008 2.561l.041.06c1.161 1.695 2.608 2.951 4.074 3.537 1.412.564 2.081.63 2.461.63.16 0 .288-.013.4-.024l.072-.007c.488-.043 1.56-.599 1.804-1.276.192-.534.243-1.117.115-1.329-.088-.144-.239-.216-.43-.308Z"/>
                            </svg>                              
                            <h3 class="font-black text-2xl mt-3">Starter</h3>
                            <!-- <h2>$19.99/mo</h2> -->
                        </div>
                        <!-- Pricing Header End -->

                        <!-- Pricing Body Start -->
                        <div class="pricing-body">
                            <ul>
                                <li>WhatsApp Balance: 0<i data-lucide="check" class="inline float-right"></i></li>
                                <li>WhatsApp Masking<i data-lucide="check" class="inline float-right"></i></li>
                                <li>1,000 Monthly Active User <i data-lucide="check" class="inline float-right"></i></li>
                                <li>Broadcast Tools <i data-lucide="check" class="inline float-right"></i></li>
                                <li>Inbox Chat <i data-lucide="check" class="inline float-right"></i></li>
                                <li>Automation Chatbot <i data-lucide="check" class="inline float-right"></i></li>
                                <li>Open API <i data-lucide="check" class="inline float-right"></i></li>
                                <li>3 Users<i data-lucide="check" class="inline float-right"></i></li>
                            </ul>
                        </div>
                        <!-- Pricing Body Start -->

                        <!-- Pricing Footer Start -->
                        <div class="pricing-footer">
                            <a href="./admin/view/login" class="btn-default">get started</a>
                        </div>
                        <!-- Pricing Footer End -->
                    </div>
                    <!-- Pricing Item End -->
                </div>

                <div class="col-lg-4">
                    <!-- Pricing Item Start -->
                    <div class="pricing-item highlighted-box wow fadeInUp" data-wow-delay="0.25s">
                        <!-- Pricing Header Start -->
                        <div class="pricing-header">
                            <svg class="w-[36px] h-[36px] text-green-500 mx-auto" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path fill="currentColor" fill-rule="evenodd" d="M12 4a8 8 0 0 0-6.895 12.06l.569.718-.697 2.359 2.32-.648.379.243A8 8 0 1 0 12 4ZM2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10a9.96 9.96 0 0 1-5.016-1.347l-4.948 1.382 1.426-4.829-.006-.007-.033-.055A9.958 9.958 0 0 1 2 12Z" clip-rule="evenodd"/>
                                <path fill="currentColor" d="M16.735 13.492c-.038-.018-1.497-.736-1.756-.83a1.008 1.008 0 0 0-.34-.075c-.196 0-.362.098-.49.291-.146.217-.587.732-.723.886-.018.02-.042.045-.057.045-.013 0-.239-.093-.307-.123-1.564-.68-2.751-2.313-2.914-2.589-.023-.04-.024-.057-.024-.057.005-.021.058-.074.085-.101.08-.079.166-.182.249-.283l.117-.14c.121-.14.175-.25.237-.375l.033-.066a.68.68 0 0 0-.02-.64c-.034-.069-.65-1.555-.715-1.711-.158-.377-.366-.552-.655-.552-.027 0 0 0-.112.005-.137.005-.883.104-1.213.311-.35.22-.94.924-.94 2.16 0 1.112.705 2.162 1.008 2.561l.041.06c1.161 1.695 2.608 2.951 4.074 3.537 1.412.564 2.081.63 2.461.63.16 0 .288-.013.4-.024l.072-.007c.488-.043 1.56-.599 1.804-1.276.192-.534.243-1.117.115-1.329-.088-.144-.239-.216-.43-.308Z"/>
                            </svg>

                            <h3 class="font-black text-2xl mt-3">Business</h3>
                            <!-- <h2>$19.99/mo</h2> -->
                        </div>
                        <!-- Pricing Header End -->

                        <!-- Pricing Body Start -->
                        <div class="pricing-body">
                            <ul>
                                <li>WhatsApp Balance: 1000.00 <i data-lucide="check" class="inline float-right"></i></li>
                                <li>WhatsApp Masking<i data-lucide="check" class="inline float-right"></i></li>
                                <li>5,000 Monthly Active User <i data-lucide="check" class="inline float-right"></i></li>
                                <li>Broadcast Tools <i data-lucide="check" class="inline float-right"></i></li>
                                <li>Inbox Chat <i data-lucide="check" class="inline float-right"></i></li>
                                <li>Automation Chatbot <i data-lucide="check" class="inline float-right"></i></li>
                                <li>Open API <i data-lucide="check" class="inline float-right"></i></li>
                                <li>10 Users <i data-lucide="check" class="inline float-right"></i></li>
                            </ul>
                        </div>
                        <!-- Pricing Body Start -->

                        <!-- Pricing Footer Start -->
                        <div class="pricing-footer">
                            <a href="./admin/view/login" class="btn-default">get started</a>
                        </div>
                        <!-- Pricing Footer End -->
                    </div>
                    <!-- Pricing Item End -->
                </div>

                <div class="col-lg-4">
                    <!-- Pricing Item Start -->
                    <div class="pricing-item wow fadeInUp" data-wow-delay="0.5s">
                        <!-- Pricing Header Start -->
                        <div class="pricing-header">
                            <svg class="w-[36px] h-[36px] text-green-500 mx-auto" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path fill="currentColor" fill-rule="evenodd" d="M12 4a8 8 0 0 0-6.895 12.06l.569.718-.697 2.359 2.32-.648.379.243A8 8 0 1 0 12 4ZM2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10a9.96 9.96 0 0 1-5.016-1.347l-4.948 1.382 1.426-4.829-.006-.007-.033-.055A9.958 9.958 0 0 1 2 12Z" clip-rule="evenodd"/>
                                <path fill="currentColor" d="M16.735 13.492c-.038-.018-1.497-.736-1.756-.83a1.008 1.008 0 0 0-.34-.075c-.196 0-.362.098-.49.291-.146.217-.587.732-.723.886-.018.02-.042.045-.057.045-.013 0-.239-.093-.307-.123-1.564-.68-2.751-2.313-2.914-2.589-.023-.04-.024-.057-.024-.057.005-.021.058-.074.085-.101.08-.079.166-.182.249-.283l.117-.14c.121-.14.175-.25.237-.375l.033-.066a.68.68 0 0 0-.02-.64c-.034-.069-.65-1.555-.715-1.711-.158-.377-.366-.552-.655-.552-.027 0 0 0-.112.005-.137.005-.883.104-1.213.311-.35.22-.94.924-.94 2.16 0 1.112.705 2.162 1.008 2.561l.041.06c1.161 1.695 2.608 2.951 4.074 3.537 1.412.564 2.081.63 2.461.63.16 0 .288-.013.4-.024l.072-.007c.488-.043 1.56-.599 1.804-1.276.192-.534.243-1.117.115-1.329-.088-.144-.239-.216-.43-.308Z"/>
                            </svg>

                            <h3 class="font-black text-2xl mt-3">Programmatic Marketing</h3>
                            <!-- <h2>$19.99/mo</h2> -->
                        </div>
                        <!-- Pricing Header End -->

                        <!-- Pricing Body Start -->
                        <div class="pricing-body">
                            <ul>
                                <li>WhatsApp Balance: 1.500.00 <i data-lucide="check" class="inline float-right"></i></li>
                                <li>WhatsApp Masking<i data-lucide="check" class="inline float-right"></i></li>
                                <li>10,000 Monthly Active User <i data-lucide="check" class="inline float-right"></i></li>
                                <li>Broadcast Tools <i data-lucide="check" class="inline float-right"></i></li>
                                <li>Inbox Chat <i data-lucide="check" class="inline float-right"></i></li>
                                <li>Automation Chatbot <i data-lucide="check" class="inline float-right"></i></li>
                                <li>Open API <i data-lucide="check" class="inline float-right"></i></li>
                                <li>15 Users<i data-lucide="check" class="inline float-right"></i></li>
                            </ul>
                        </div>
                        <!-- Pricing Body Start -->

                        <!-- Pricing Footer Start -->
                        <div class="pricing-footer">
                            <a href="./admin/view/login" class="btn-default">get started</a>
                        </div>
                        <!-- Pricing Footer End -->
                    </div>
                    <!-- Pricing Item End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Our Pricing Section End -->

    <!-- Contact Start -->
    <section id="contact" class="py-20 bg-gray-50">
        <div class="container">
            <div class="lg:w-4/5 mx-auto">

                <div class="max-w-2xl mx-auto text-center">
                    <h2 class="text-3xl md:text-4xl/tight font-semibold text-black mt-4">Ready? Lets get you started</h2>
                    <p class="text-base font-medium mt-4 text-muted">Start building trust today!</p>
                </div>

                <div class="lg:col-span-2 lg:ms-24 mt-20">
                    <div class="p-6 md:p-12 rounded-md border bg-white">
                        <form>
                            <div class="grid sm:grid-cols-2 gap-6">
                                <div>
                                    <label for="fullname"
                                        class="block text-sm/normal font-semibold text-black mb-2">Full Name</label>
                                    <input type="text"
                                        class="block w-full text-sm rounded-md py-3 px-4 border-gray-200 focus:border-gray-300 focus:ring-transparent"
                                        id="fullname" placeholder="Your first name..." required="" name="firstname">
                                </div>

                                <div>
                                    <label for="companyname"
                                        class="block text-sm/normal font-semibold text-black mb-2">Company Name</label>
                                    <input type="text"
                                        class="block w-full text-sm rounded-md py-3 px-4 border-gray-200 focus:border-gray-300 focus:ring-transparent"
                                        id="companyname" placeholder="Your company name..." required="" name="companyname">
                                </div>

                                <div>
                                    <label for="companyposition"
                                        class="block text-sm/normal font-semibold text-black mb-2">Company Position</label>
                                    <input type="text"
                                        class="block w-full text-sm rounded-md py-3 px-4 border-gray-200 focus:border-gray-300 focus:ring-transparent"
                                        id="companyposition" placeholder="Your company position..." required="" name="companyposition">
                                </div>

                                <div>
                                    <label for="email"
                                        class="block text-sm/normal font-semibold text-black mb-2">Email Address</label>
                                    <input type="email"
                                        class="block w-full text-sm rounded-md py-3 px-4 border-gray-200 focus:border-gray-300 focus:ring-transparent"
                                        id="email" placeholder="Your email..." required="" name="email">
                                </div>

                                <div>
                                    <label for="phone"
                                        class="block text-sm/normal font-semibold text-black mb-2">Phone Number</label>
                                    <input type="text"
                                        class="block w-full text-sm rounded-md py-3 px-4 border-gray-200 focus:border-gray-300 focus:ring-transparent"
                                        id="phone" placeholder="Type phone number..." required="" name="phone">
                                </div>


                                <div>
                                    <label for="website"
                                        class="text-sm/normal font-semibold text-black mb-2">Website URL</label>
                                    <input type="url"
                                        class="block w-full text-sm rounded-md py-3 px-4 border-gray-200 focus:border-gray-300 focus:ring-transparent"
                                        id="phone" placeholder="Type phone number..." required="" name="website">
                                </div>

                                <div class="sm:col-span-2">
                                    <div class="mb-4">
                                        <label for="message"
                                            class="block text-sm/normal font-semibold text-black mb-2">Messages</label>
                                        <textarea class="block w-full text-sm rounded-md py-3 px-4 border-gray-200 focus:border-gray-300 focus:ring-transparent"
                                            id="message" rows="4" placeholder="Type messages..."
                                            required="" name="message"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit"
                                    class="py-2 px-6 rounded-md text-baseitems-center justify-center border border-primary text-white bg-primary hover:bg-primaryDark transition-all duration-500 font-medium">Send
                                    Messages <i class="mdi mdi-send ms-1"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Contact End -->

    <!-- Footer Start -->
    <footer class="bg-[#17243A]">
        <div class="container">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6 pb-16 pt-16">
                <div class="col-span-full lg:col-span-2">
                    <div class="max-w-2xl mx-auto">
                        <div class="flex items-center">
                            <img src="assets/images/logo.svg" alt="" class="h-10 me-5">
                        </div>
                        <p class="text-gray-300 max-w-xs mt-6">No. 77 Ojueleba Road, Surulere, Lagos State, Nigeria</p>
                    </div>

                    <div class="mt-6 grid space-y-3">
                        <a class="inline-flex items-center gap-x-4 text-gray-300 hover:text-gray-400 transition-all duration-300"
                            href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-mail">
                                <rect width="20" height="16" x="2" y="4" rx="2" />
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                            </svg>
                            info@kreativerock.com
                        </a>

                        <a class="inline-flex items-center gap-x-4 text-gray-300 hover:text-gray-400 transition-all duration-300"
                            href="#">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-phone">
                                <path
                                    d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                            </svg>
                            +2348094200003, +2348094200003
                        </a>
                    </div>
                </div>

                <div class="col-span-1">
                    <h4 class="font-semibold text-gray-100 uppercase">Company</h4>

                    <div class="mt-6 grid space-y-3">
                        <p><a class="inline-flex gap-x-2 text-base text-gray-300 hover:text-gray-400 transition-all duration-300"
                                href="about">About</a></p>
                        <p><a class="inline-flex gap-x-2 text-base text-gray-300 hover:text-gray-400 transition-all duration-300"
                                href="products">Products</a></p>
                        <p><a class="inline-flex gap-x-2 text-base text-gray-300 hover:text-gray-400 transition-all duration-300"
                                href="Contact">Contact</a></p>
                        <p><a class="inline-flex gap-x-2 text-base text-gray-300 hover:text-gray-400 transition-all duration-300"
                                href="blog">Blog</a></p>
                    </div>
                </div>

                <div class="col-span-1">
                    <h4 class="font-semibold text-gray-100 uppercase">Product</h4>

                    <div class="mt-6 grid space-y-3">
                        <p><a class="inline-flex gap-x-2 text-base text-gray-300 hover:text-gray-400 transition-all duration-300"
                                href="mobile-texting">Two-Way Messaging</a></p>
                        <p><a class="inline-flex gap-x-2 text-base text-gray-300 hover:text-gray-400 transition-all duration-300"
                                href="whatsapp-commerce">WhatsApp Commerce</a></p>
                        <p><a class="inline-flex gap-x-2 text-base text-gray-300 hover:text-gray-400 transition-all duration-300"
                                href="programmatic-marketing">Programmatic Marketing</a></p>
                        <p><a class="inline-flex gap-x-2 text-base text-gray-300 hover:text-gray-400 transition-all duration-300"
                                href="social-media-management">Social Media Management</a></p>
                    </div>
                </div>

                <div class="col-span-1">
                    <h4 class="font-semibold text-gray-100 uppercase">Important Links</h4>

                    <div class="mt-6 grid space-y-3">
                        <p><a class="inline-flex gap-x-2 text-base text-gray-300 hover:text-gray-400 transition-all duration-300"
                                href="./admin/view/signup">Register</a></p>
                        <p><a class="inline-flex gap-x-2 text-base text-gray-300 hover:text-gray-400 transition-all duration-300"
                                href="./admin/view/login">Login</a></p>
                        <p><a class="inline-flex gap-x-2 text-base text-gray-300 hover:text-gray-400 transition-all duration-300"
                                href="products">Products</a></p>
                        <p><a class="inline-flex gap-x-2 text-base text-gray-300 hover:text-gray-400 transition-all duration-300"
                                href="policy">Privacy Policy</a></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="py-4 bg-[#1C2940]">
            <!-- 1B283F -->
            <div class="container">
                <div class="flex justify-between items-center">
                    <p class="text-base text-white">
                        <script>document.write(new Date().getFullYear())</script>© KreativeRock  - <a href="#">All Right Reserved</a>
                    </p>

                    <div>
                        <a class="size-8 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-md border border-transparent text-white hover:bg-primary transition-all duration-300"
                            href="https://facebook.com/" target="_blank">
                            <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                fill="currentColor" viewBox="0 0 16 16">
                                <path
                                    d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z" />
                            </svg>
                        </a>

                        <a class="size-8 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-md border border-transparent text-white hover:bg-primary transition-all duration-300"
                        href="https://instagram.com/" target="_blank">
                            <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-instagram"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
                        </a>
                        
                        <a class="size-8 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-md border border-transparent text-white hover:bg-primary transition-all duration-300"
                        href="https://x.com/" target="_blank">
                            <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                fill="currentColor" viewBox="0 0 16 16">
                                <path
                                    d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334 0-.14 0-.282-.006-.422A6.685 6.685 0 0 0 16 3.542a6.658 6.658 0 0 1-1.889.518 3.301 3.301 0 0 0 1.447-1.817 6.533 6.533 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.325 9.325 0 0 1-6.767-3.429 3.289 3.289 0 0 0 1.018 4.382A3.323 3.323 0 0 1 .64 6.575v.045a3.288 3.288 0 0 0 2.632 3.218 3.203 3.203 0 0 1-.865.115 3.23 3.23 0 0 1-.614-.057 3.283 3.283 0 0 0 3.067 2.277A6.588 6.588 0 0 1 .78 13.58a6.32 6.32 0 0 1-.78-.045A9.344 9.344 0 0 0 5.026 15z" />
                            </svg>
                        </a>


                        <a class="size-8 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-md border border-transparent text-white hover:bg-primary transition-all duration-300"
                        href="https://linkedin.com/" target="_blank">
                            <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-linkedin"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect width="4" height="12" x="2" y="9"/><circle cx="4" cy="4" r="2"/></svg>
                        </a>
                    </div>
                </div>


            </div>
        </div>

    </footer>
    <!-- Footer End -->


    <!-- Back to top -->
    <a href="javascript: void(0);" onclick="topFunction()" id="back-to-top"
        class="back-to-top fixed text-base rounded-md z-10 bottom-8 right-8 h-8 w-8 text-center bg-primary text-white leading-9 justify-center items-center">
        <i data-lucide="arrow-up" class="h-4 w-4 text-white stroke-2"></i>
    </a>
    <!-- Back to top -->


    <!-- Preline Js -->
    <script src="assets/libs/preline/preline.js"></script>

    <!-- swiper -->
    <script src="assets/libs//swiper/swiper-bundle.min.js"></script>

    <!-- Lucide Js -->
    <script src="assets/libs/lucide/umd/lucide.min.js"></script>

    <script src="assets/js/utils.js"></script>

    <!-- Main App Js -->
    <script src="assets/js/app.js"></script>

</body>

</html>