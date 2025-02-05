<?php
session_start();
if(!isset($_SESSION["user_id"]) && !isset($_SESSION["user_id"]))
{
	header('Location: login');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KreativeRock Admin</title>
    <link rel="stylesheet" href="./css/index.css">
    <link rel="stylesheet" href="./css/styles.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;1,700&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@40,400,0,0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
  />

</head>

<style>
    button:disabled  {
        opacity: 40;
        cursor: not-allowed;
    }
</style>

<body>
    <main class="h-screen bg-primary/10">
        <div class="h-full">

            <!-- header -->
            <header>
                <div class="flex items-center bg-white border-b border-gray-200/50">
                    <span
                        class="xl:w-[250px] font-bold text-base block py-3 pl-5 selection:bg-white uppercase font-heebo text-primary-g">Kreative<span
                            class="text-gray-400">Rock</span></span>
                    <div class="flex-1 flex items-center justify-end xl:justify-between">
                        <button id="toggler"
                            class="flex items-center justify-center h-7 w-7 rounded hover:bg-primary transition ease-linear duration-300 text-gray-400">
                            <span class="material-symbols-outlined">menu</span>
                        </button>
                        <span class="px-1 order-first xl:order-last">
                            <button onclick="logoff()" title="logout" class="flex items-center justify-center h-7 w-7 rounded hover:bg-primary transition ease-linear duration-300 text-gray-500">
                                <span class="material-symbols-outlined" style="font-size:19px">power_settings_new</span>
                            </button>
                        </span>
                    </div>
                </div>
            </header>

            <section>
                <div class="xl:flex h-screen relative">
                    <!-- navigation -->
                    <nav id="navigation" class="fixed top-0 left-0 z-40 lg:relative lg:z-0 w-4/5 xl:w-[250px] h-full bg-white border-r border-gray-200/50 pb-14">
                        <div class="overflow-y-auto overflow-x-hidden h-full">
                            <ul class="font-poppins mt-5">
                                <li class="nav-item">
                                    <span class="navitem-title group" id="home">
                                        <span class="material-symbols-outlined group-hover:text-primary-g"
                                            style="font-size: 20px;">home</span>
                                        <span class="group-hover:text-primary-g">Home</span>
                                    </span>
                                </li>
                                <li class="nav-item">
                                    <span class="navitem-title group" id="dashboard">
                                        <span class="material-symbols-outlined group-hover:text-primary-g"
                                            style="font-size: 20px;">widgets</span>
                                        <span class="group-hover:text-primary-g">Dashboard</span>
                                    </span>
                                </li>
                                <li class="nav-item">
                                    <span class=" navitem-title group">
                                        <span class="material-symbols-outlined group-hover:text-primary-g"
                                            style="font-size: 20px;">person</span>
                                        <span class="group-hover:text-primary-g">
                                            <span>User</span>
                                            <span class="material-symbols-outlined" style="font-size: 15px;">chevron_right</span>
                                        </span>
                                    </span>
                                    <ul class="ml-10 gap-y-4 flex flex-col">
                                        <li class="navitem-child" id="access_control">Access Control</li>
                                        <li class="navitem-child" id="profile">Profile</li>
                                        <li class="navitem-child" id="password">Change Password</li>
                                        <li class="navitem-child" id="user/select">Select User</li>
                                        <li class="navitem-child" id="user/deactivate">Deactivate User</li>
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <span class=" navitem-title group">
                                        <span class="material-symbols-outlined group-hover:text-primary-g"
                                            style="font-size: 20px;">sms</span>
                                        <span class="group-hover:text-primary-g">
                                            <span>SMS</span>
                                            <span class="material-symbols-outlined" style="font-size: 15px;">chevron_right</span>
                                        </span>
                                    </span>
                                    <ul class="ml-10 gap-y-4 flex flex-col">
                                        <li class="navitem-child" id="sms/package">SMS Package</li>
                                        <li class="navitem-child" id="sms/unit">SMS Units History</li>
                                        <li class="navitem-child" id="sms/bulk">Buy Units</li>
                                        <!--<li class="navitem-child" id="sms/campaign">Campaign</li>-->
                                    </ul>
                                </li>
                                <li class="nav-item">
                                    <span class=" navitem-title group">
                                        <span class="material-symbols-outlined group-hover:text-primary-g"
                                            style="font-size: 20px;">contacts</span>
                                        <span class="group-hover:text-primary-g">
                                            <span>CAMPAIGN</span>
                                            <span class="material-symbols-outlined" style="font-size: 15px;">chevron_right</span>
                                        </span>
                                    </span>
                                    <ul class="ml-10 gap-y-4 flex flex-col">
                                        <li class="navitem-child" id="sms/campaign">Create Campaign</li>
                                        <li class="navitem-child" id="campaign/manage">Manage campaigns</li>
                                        <li class="navitem-child" id="campaign/conversations">Conversations</li>
                                    </ul>
                                </li>
  
                            </ul>
                        </div>
                    </nav>
                    <section class="flex-1 flex flex-col justify-between pb-14">
                        <!-- content area -->
                        <div  class="overflow-y-auto overflow-x-hidden h-full">
                            <div class="xl:w-5/6 3xl:w-3/5 w-full mx-auto mt-5 p-5 xl:p-0" id="workspace"></div>
                        </div>
                        <footer class="mt-5 p-5 xl:p-0 invisible">
                            <p class="xl:w-5/6 3xl:w-3/5 mx-auto py-1 border-t border-gray-200 text-xs text-gray-400"> &copy; 2023 Elfrique.com
                            </p>
                        </footer>
                    </section>
                </div>
            </section>

        </div> 
    </main>
    
    <div id="modal" class="hidden z-40 fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
      <div class="relative modal-content bg-white rounded-lg p-6 shadow-lg">
        <div class="mt-2">
            <h2 class="text-lg font-bold mb-4">Purchase Summary</h2>
            <div name="content" class="min-w-[350px]"></div>
        </div>
        <button type="button" name="close" class="hover:bg-gray-100 transition m-2 absolute top-0 right-0 h-10 w-10 flex items-center justify-center rounded-full rounded-md material-symbols-outlined" onclick="closeModal()">Close</button>
      </div>
    </div>
    
    <input type="hidden" id="user_role" value="<?php echo $_SESSION["role"]?>" readonly>
    <input type="hidden" id="user_permissions" value="<?php echo $_SESSION["permissions"]?>" readonly>
    <input type="hidden" id="your_email" value="<?php echo $_SESSION["elfuseremail"]?>" readonly>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="./js/util.js"></script>
    <script src="./js/oreutil.js"></script>
    <script src="./js/router.js"></script>
    <script src="./js/index.js"></script>
</body>

</html>