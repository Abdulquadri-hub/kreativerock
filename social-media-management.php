<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KreactiveRock - | Social Media CRM</title>

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

    <!-- Faqs Start -->
    <section id="FAQs">
        <div class="container">
            <div class="py-20">
                <div class="text-center max-w-xl mx-auto">
                    <div>
                        <span
                            class="text-sm text-primary uppercase font-medium tracking-wider text-default-950 mb-6">FAQs</span>
                    </div>
                    <h2 class="text-3xl md:text-4xl/tight font-semibold mt-4">Any questions left unanswered?</h2>
                </div>

                <div id="accordion-collapse" data-accordion="collapse" class="md:gap-[30px] mt-14 bg-white rounded-xl">
                    <div class="hs-accordion-group divide-y divide-gray-200">
                        <div class="relative overflow-hidden">
                            <h2 class="text-base font-semibold" id="accordion-collapse-heading-1">
                                <button type="button"
                                    class="flex justify-between items-center px-5 py-4 w-full font-semibold text-lg text-start"
                                    data-accordion-target="#accordion-collapse-body-1" aria-expanded="true"
                                    aria-controls="accordion-collapse-body-1">
                                    <span>What is RCS Messaging</span>
                                    <svg data-accordion-icon class="size-4 rotate-180 shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </h2>
                            <div id="accordion-collapse-body-1" class="hidden"
                                aria-labelledby="accordion-collapse-heading-1">
                                <div class="px-5 pb-5">
                                    <p class="text-muted text-base font-normal">RCS (Rich Communication Services) is the next generation of SMS messaging. It offers a more advanced messaging experience, allowing for richer content like high-quality images, videos, and interactive features. RCS messages can be personalized, interactive, and deliver a more engaging experience than traditional SMS.</p>
                                </div>
                            </div>
                        </div>

                        <div class="relative overflow-hidden">
                            <h2 class="text-base font-semibold" id="accordion-collapse-heading-2">
                                <button type="button"
                                    class="flex justify-between items-center px-5 py-4 w-full font-semibold text-lg text-start"
                                    data-accordion-target="#accordion-collapse-body-2" aria-expanded="false"
                                    aria-controls="accordion-collapse-body-2">
                                    <span>How can I use your WhatsApp Business API?</span>
                                    <svg data-accordion-icon class="size-4 shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </h2>
                            <div id="accordion-collapse-body-2" class="hidden"
                                aria-labelledby="accordion-collapse-heading-2">
                                <div class="px-5 pb-5">
                                    <p class="text-muted text-base font-normal">Our WhatsApp Business API allows businesses to automate customer interactions, send personalized messages, and provide 24/7 support. To use our API, you'll need to be approved by WhatsApp and integrate our API into your business systems. We provide comprehensive documentation and support to help you get started.</p>
                                </div>
                            </div>
                        </div>

                        <div class="relative overflow-hidden">
                            <h2 class="text-base font-semibold" id="accordion-collapse-heading-3">
                                <button type="button"
                                    class="flex justify-between items-center px-5 py-4 w-full font-semibold text-lg text-start"
                                    data-accordion-target="#accordion-collapse-body-3" aria-expanded="false"
                                    aria-controls="accordion-collapse-body-3">
                                    <span>What are the benefits of using your SMS services?</span>
                                    <svg data-accordion-icon class="size-4 shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </h2>
                            <div id="accordion-collapse-body-3" class="hidden"
                                aria-labelledby="accordion-collapse-heading-3">
                                <div class="px-5 pb-5">
                                    <p class="text-muted text-base font-normal">Our SMS services offer a reliable and cost-effective way to reach your target audience. With our SMS platform, you can send bulk SMS, SMS marketing campaigns, and SMS verification codes. We offer a variety of features, including SMS scheduling, delivery reports, and international SMS.</p>
                                </div>
                            </div>
                        </div>

                        <div class="relative overflow-hidden">
                            <h2 class="text-base font-semibold" id="accordion-collapse-heading-4">
                                <button type="button"
                                    class="flex justify-between items-center px-5 py-4 w-full font-semibold text-lg text-start"
                                    data-accordion-target="#accordion-collapse-body-4" aria-expanded="false"
                                    aria-controls="accordion-collapse-body-4">
                                    <span>How can I improve my customer engagement with your messaging services?</span>
                                    <svg data-accordion-icon class="size-4 shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </h2>
                            <div id="accordion-collapse-body-4" class="hidden"
                                aria-labelledby="accordion-collapse-heading-4">
                                <div class="px-5 pb-5">
                                    <p class="text-muted text-base font-normal">Our messaging services can help you improve customer engagement by providing a seamless and personalized communication experience. By using RCS, WhatsApp, and SMS, you can deliver timely and relevant messages to your customers. You can also use our platform to gather customer feedback and insights.</p>
                                </div>
                            </div>
                        </div>

                        <div class="relative overflow-hidden">
                            <h2 class="text-base font-semibold" id="accordion-collapse-heading-5">
                                <button type="button"
                                    class="flex justify-between items-center px-5 py-4 w-full font-semibold text-lg text-start"
                                    data-accordion-target="#accordion-collapse-body-5" aria-expanded="false"
                                    aria-controls="accordion-collapse-body-4">
                                    <span>What is the pricing for your messaging services?</span>
                                    <svg data-accordion-icon class="size-4 shrink-0" fill="currentColor"
                                        viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </h2>
                            <div id="accordion-collapse-body-5" class="hidden"
                                aria-labelledby="accordion-collapse-heading-5">
                                <div class="px-5 pb-5">
                                    <p class="text-muted text-base font-normal">Our pricing is competitive and varies depending on the specific services you choose. We offer flexible pricing plans to suit your business needs. Contact our sales team for a customized quote.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Faqs End -->

    <!-- Contact Start -->
    <section id="contact" class="py-20 bg-amber-50">
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
    <script src="assets/libs/swiper/swiper-bundle.min.js"></script>

    <!-- Lucide Js -->
    <script src="assets/libs/lucide/umd/lucide.min.js"></script>

    <script src="assets/js/utils.js"></script>

</body>

</html>