<?php
session_start();
if(!isset($_SESSION["user_id"]) && !isset($_SESSION["user_id"]))
{
	header('Location: /kreativerock/newadmin/view/login');
}

?>

<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="utf-8">
    <title>KreativeRock | Admin center</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="/kreativerock/newadmin/view/" />
    <link
    href="https://fonts.googleapis.com/icon?family=Material+Icons"
    rel="stylesheet"
  />

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <!-- App favicon -->
    <link rel="shortcut icon" href="./images/favicon.ico">

    <!-- Icons css  (Mandatory in All Pages) -->
    <link href="./assets/css/icons.min.css" rel="stylesheet" type="text/css">

    <!-- App css  (Mandatory in All Pages) -->
    <link href="./assets/css/app.min.css" rel="stylesheet" type="text/css">
    
    <!-- custom css -->
    <link href="./css/custom.css" rel="stylesheet" type="text/css">
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@40,400,0,0" />
    
</head>

<body>

    <div class="wrapper">

        <!-- Start Sidebar -->
        <aside id="app-menu"
            class="hs-overlay fixed inset-y-0 start-0 z-60 hidden w-sidenav min-w-sidenav bg-slate-800 overflow-y-auto -translate-x-full transform transition-all duration-200 hs-overlay-open:translate-x-0 lg:bottom-0 lg:end-auto lg:z-30 lg:block lg:translate-x-0 rtl:translate-x-full rtl:hs-overlay-open:translate-x-0 rtl:lg:translate-x-0 print:hidden [--body-scroll:true] [--overlay-backdrop:true] lg:[--overlay-backdrop:false]">

            <div class="flex flex-col h-full">
                <!-- Sidenav Logo -->
                <div class="sticky top-0 flex h-topbar items-center justify-center px-6">
                    <a href='index.html'>
                        <!--<img src="../../images/logo.svg" alt="logo" class="w-28 grayscale">-->
                        <span class="font-bold text-base py-3 uppercase font-heebo text-amber-500">Kreative<span class="text-gray-400">Rock</span></span>
                    </a>
                </div>

                <div class="p-4 h-[calc(100%-theme('spacing.topbar'))] flex-grow" data-simplebar>
                    <!-- Menu -->
                    <ul class="admin-menu hs-accordion-group flex w-full flex-col gap-1">
                        <li class="menu-item">
                            <a class='group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-400 transition-all hover:bg-default-100/5' data-navigo href="/">
                                <i class="i-lucide-airplay size-5"></i>
                                Home
                            </a>
                        </li>
                        
                        <!-- users-->
                        <li class="menu-item hs-accordion">
                            <a href="javascript:void(0)"
                                class="hs-accordion-toggle group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-400 transition-all hover:bg-default-100/5 hs-accordion-active:bg-default-100/5 hs-accordion-active:text-default-100">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-round-cog"><path d="M2 21a8 8 0 0 1 10.434-7.62"/><circle cx="10" cy="8" r="5"/><circle cx="18" cy="18" r="3"/><path d="m19.5 14.3-.4.9"/><path d="m16.9 20.8-.4.9"/><path d="m21.7 19.5-.9-.4"/><path d="m15.2 16.9-.9-.4"/><path d="m21.7 16.5-.9.4"/><path d="m15.2 19.1-.9.4"/><path d="m19.5 21.7-.4-.9"/><path d="m16.9 15.2-.4-.9"/></svg>
                                <span class="menu-text"> User </span>
                                <span class="menu-arrow"></span>
                            </a>

                            <div class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300">
                                <ul class="mt-1 space-y-1"> 
                                    <li class="menu-item">
                                        <a data-navigo href="/access" data-title="Access Control" class='flex items-center gap-x-3.5 rounded-md px-3 py-1.5 text-sm font-medium text-default-400 transition-all hover:bg-default-100/5'>
                                            <i class="menu-dot"></i>
                                            Access Control
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a data-navigo href="/user/profile" data-title="Account Profile" class='flex items-center gap-x-3.5 rounded-md px-3 py-1.5 text-sm font-medium text-default-400 transition-all hover:bg-default-100/5'>
                                            <i class="menu-dot"></i>
                                            Profile
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a data-navigo href="/password/update" data-title="Password Update" class='flex items-center gap-x-3.5 rounded-md px-3 py-1.5 text-sm font-medium text-default-400 transition-all hover:bg-default-100/5'>
                                            <i class="menu-dot"></i>
                                            Change Password
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a data-navigo href="/user/select" data-title="Select User" class='flex items-center gap-x-3.5 rounded-md px-3 py-1.5 text-sm font-medium text-default-400 transition-all hover:bg-default-100/5'>
                                            <i class="menu-dot"></i>
                                            Select User
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a data-navigo href="/user/deactivate" data-title="Deactivate User" class='flex items-center gap-x-3.5 rounded-md px-3 py-1.5 text-sm font-medium text-default-400 transition-all hover:bg-default-100/5'>
                                            <i class="menu-dot"></i>
                                            Deactivate User
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        <!-- Tools -->
                        <li class="px-3 py-2 text-xs uppercase font-medium text-default-500">Tools</li>
                        <li class="menu-item hs-accordion">
                            <a href="javascript:void(0)"
                                class="hs-accordion-toggle group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-400 transition-all hover:bg-default-100/5 hs-accordion-active:bg-default-100/5 hs-accordion-active:text-default-100">
                                <i class="i-lucide-user-circle size-5"></i>
                                <span class="menu-text"> Contacts </span>
                                <span class="menu-arrow"></span>
                            </a>

                            <div class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300">
                                <ul class="mt-1 space-y-1">
                                    <li class="menu-item">
                                        <a data-navigo href="/contact/list" class='flex items-center gap-x-3.5 rounded-md px-3 py-1.5 text-sm font-medium text-default-400 transition-all hover:bg-default-100/5'>
                                            <i class="menu-dot"></i>
                                            <span class="menu-text">Setup</span>
                                        </a>
                                    </li>
                                    <li class="menu-item"> 
                                        <a data-navigo href="/contact/manage" class='flex items-center gap-x-3.5 rounded-md px-3 py-1.5 text-sm font-medium text-default-400 transition-all hover:bg-default-100/5'>
                                            <i class="menu-dot"></i>
                                            <span class="menu-text">Manage</span>
                                        </a>
                                    </li> 
                                    <li class="menu-item"> 
                                        <a data-navigo href="/contact/segment" class='flex items-center gap-x-3.5 rounded-md px-3 py-1.5 text-sm font-medium text-default-400 transition-all hover:bg-default-100/5'>
                                            <i class="menu-dot"></i>
                                            <span class="menu-text">Segment</span>
                                        </a>
                                    </li> 
                                </ul>
                            </div>
                        </li>
                        <li class="menu-item">
                            <a data-navigo href="/units" class='group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-400 transition-all hover:bg-default-100/5'>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-wifi"><path d="M12 20h.01"/><path d="M2 8.82a15 15 0 0 1 20 0"/><path d="M5 12.859a10 10 0 0 1 14 0"/><path d="M8.5 16.429a5 5 0 0 1 7 0"/></svg>
                                Units
                            </a>
                        </li>
                        
                        <!-- services-->
                        <li class="px-3 py-2 text-xs uppercase font-medium text-default-500">Services</li>
                        <li class="menu-item hs-accordion">
                            <a href="javascript:void(0)"
                                class="hs-accordion-toggle group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-400 transition-all hover:bg-default-100/5 hs-accordion-active:bg-default-100/5 hs-accordion-active:text-default-100">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layers"><path d="M12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83z"/><path d="M2 12a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 12"/><path d="M2 17a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 17"/></svg>
                                <span class="menu-text"> Campaign </span>
                                <span class="menu-arrow"></span>
                            </a>

                            <div class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300">
                                <ul class="mt-1 space-y-1">
                                    <li class="menu-item">
                                        <a data-navigo href="/campaign/option" class='flex items-center gap-x-3.5 rounded-md px-3 py-1.5 text-sm font-medium text-default-400 transition-all hover:bg-default-100/5'>
                                            <i class="menu-dot"></i>
                                            <span class="menu-text">Create Campaign</span>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a class='flex items-center gap-x-3.5 rounded-md px-3 py-1.5 text-sm font-medium text-default-400 transition-all hover:bg-default-100/5'>
                                            <i class="menu-dot"></i>
                                            <span class="menu-text">Manage</span>
                                        </a>
                                    </li>
                                    <li class="menu-item">
                                        <a class='flex items-center gap-x-3.5 rounded-md px-3 py-1.5 text-sm font-medium text-default-400 transition-all hover:bg-default-100/5'>
                                            <i class="menu-dot"></i>
                                            <span class="menu-text">Templates</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="menu-item">
                            <a class='group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-400 transition-all hover:bg-default-100/5' href='app-calendar.html'>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-circle-code"><path d="M10 9.5 8 12l2 2.5"/><path d="m14 9.5 2 2.5-2 2.5"/><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22z"/></svg>
                                Conversations
                            </a>
                        </li>
                        
                        <!-- reports-->
                        <li class="px-3 py-2 text-xs uppercase font-medium text-default-500">Reports</li>
                        <li class="menu-item hs-accordion">
                            <a href="javascript:void(0)"
                                class="hs-accordion-toggle group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-400 transition-all hover:bg-default-100/5 hs-accordion-active:bg-default-100/5 hs-accordion-active:text-default-100">
                                <!--<i class="i-lucide-check-square size-5"></i>-->
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-folder"><path d="M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2Z"/></svg>
                                <span class="menu-text"> Reports </span>
                                <span class="menu-arrow"></span>
                            </a>

                            <div class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300">
                                <ul class="mt-1 space-y-1">
                                    <li class="menu-item">
                                        <a class='flex items-center gap-x-3.5 rounded-md px-3 py-1.5 text-sm font-medium text-default-400 transition-all hover:bg-default-100/5'>
                                            <i class="menu-dot"></i>
                                            <span class="menu-text">Purchase History</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        
                        <!--Others -->
                        <li class="px-3 py-2 text-xs uppercase font-medium text-default-500">Others</li>
                        <li class="menu-item hs-accordion">
                            <a href="javascript:void(0)"
                                class="hs-accordion-toggle group flex items-center gap-x-3.5 rounded-md px-3 py-2 text-sm font-medium text-default-400 transition-all hover:bg-default-100/5 hs-accordion-active:bg-default-100/5 hs-accordion-active:text-default-100">
                                <i class="i-lucide-check-square size-5"></i>
                                <span class="menu-text"> Manager </span>
                                <span class="menu-arrow"></span>
                            </a>

                            <div class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300">
                                <ul class="mt-1 space-y-1">
                                    <li class="menu-item">
                                        <a class='flex items-center gap-x-3.5 rounded-md px-3 py-1.5 text-sm font-medium text-default-400 transition-all hover:bg-default-100/5'>
                                            <i class="menu-dot"></i>
                                            <span class="menu-text">SMS Packages</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>

            </div>
        </aside>
        <!-- End Sidebar -->

        <!-- Start Page Content here -->
        <div class="page-content">

            <!-- Topbar Start -->
            <header class="app-header sticky top-0 z-50 min-h-topbar flex items-center bg-white">
                <div class="px-6 w-full flex items-center justify-between gap-4">
                    <div class="flex items-center gap-5">
                        <!-- Sidenav Menu Toggle Button -->
                        <button
                            class="flex items-center text-default-500 rounded-full cursor-pointer p-2 bg-white border border-default-200 hover:bg-primary/15 hover:text-primary hover:border-primary/5 transition-all"
                            data-hs-overlay="#app-menu" aria-label="Toggle navigation">
                            <i class="i-lucide-align-left text-2xl"></i>
                        </button>

                        <!-- Topbar Brand Logo -->
                        <a class='md:hidden flex' href='index.html'>
                            <!--<img src="assets/images/logo-sm.png" class="h-5" alt="Small logo">-->
                            <span class="font-bold text-base py-3 uppercase font-heebo text-amber-500">Kreative<span class="text-gray-400">Rock</span></span>
                        </a>
                    </div>

                    <div class="flex items-center gap-3">


                        <!-- Notification Dropdown Button -->
                        <div class="hs-dropdown relative inline-flex [--placement:bottom-right]">
                            <button type="button"
                                class="hs-dropdown-toggle inline-flex items-center p-2 rounded-full bg-white border border-default-200 hover:bg-primary/15 hover:text-primary transition-all">
                                <i class="i-lucide-bell text-lg"></i>
                            </button>

                            <!-- Dropdown menu -->
                            <div
                                class="hs-dropdown-menu duration mt-2 w-full max-w-sm rounded-lg border border-default-200 bg-white opacity-0 shadow-md transition-[opacity,margin] hs-dropdown-open:opacity-100 hidden">
                                <div class="block px-4 py-2 font-medium text-center text-default-700 rounded-t-lg bg-default-50">
                                    Notifications
                                </div>
 
                                <div class="divide-y divide-default-100"> 
                                    <div class="p-5 text-gray-400">
                                        Oops! Its clean here!
                                    </div>  
                                </div>
                            </div>
                        </div> 


                        <!-- Profile Dropdown Button -->
                        <div class="relative">
                            <div class="hs-dropdown relative inline-flex [--placement:bottom-right]">
                                <button type="button" class="hs-dropdown-toggle">
                                    <span class="inline-flex items-center justify-center size-8 rounded-full bg-default-500 text-xs font-semibold text-white leading-none">
                                        JO
                                    </span>
                                </button>
                                <div
                                    class="hs-dropdown-menu duration mt-2 min-w-48 rounded-lg border border-default-200 bg-white p-2 opacity-0 shadow-md transition-[opacity,margin] hs-dropdown-open:opacity-100 hidden">
                                    <a class="flex items-center py-2 px-3 rounded-md text-sm text-default-800 hover:bg-default-100"
                                        href="#">
                                        Profile
                                    </a>
                                    <a class="flex items-center py-2 px-3 rounded-md text-sm text-default-800 hover:bg-default-100"
                                        href="#">
                                        Settings
                                    </a>
                                    <a class="flex items-center py-2 px-3 rounded-md text-sm text-default-800 hover:bg-default-100"
                                        href="#">
                                        Support
                                    </a>

                                    <hr class="my-2 -mx-2">

                                    <a class="flex items-center py-2 px-3 rounded-md text-sm text-default-800 hover:bg-default-100 cursor-pointer"
                                        onclick="logoff()">
                                        Log Out
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <!-- Topbar End -->

            <main></main>
            

            <!-- Footer Start -->
            <footer class="footer bg-white flex items-center py-5">
                <div class="px-6 flex md:justify-between justify-center w-full gap-4">
                    <div>
                        <script>document.write(new Date().getFullYear())</script> © KreativeRock | All rights reserved
                    </div>
                </div>
            </footer>
            <!-- Footer End -->

        </div>
        <!-- End Page content -->

    </div>
    
    <input type="hidden" id="user_role" value="<?php echo $_SESSION["role"]?>" readonly>
    <input type="hidden" id="user_permissions" value="<?php echo $_SESSION["permissions"]?>" readonly>
    <input type="hidden" id="your_email" value="<?php echo $_SESSION["elfuseremail"]?>" readonly>
    
    <script src="./js/config.js"></script>
    <script src="./js/util.js"></script>
    <script src="./js/util2.js"></script>
    <script src="./js/dependencies/navigo.js"></script>
    <script src="./js/router.js"></script>
    
    <!-- Plugin Js (Mandatory in All Pages) -->
    <script src="./assets/libs/jquery/jquery.min.js"></script>
    <script src="./assets/libs/preline/preline.js"></script>
    <script src="./assets/libs/simplebar/simplebar.min.js"></script>
    <script src="./assets/libs/iconify-icon/iconify-icon.min.js"></script>
    <script src="./assets/libs/inputmask/inputmask.min.js"></script>
    <script src="./assets/js/pages/form-inputmask.js"></script>

    <!-- App Js (Mandatory in All Pages) -->
    <script src="./assets/js/app.js"></script>
    
</body>


</html>