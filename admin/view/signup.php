<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup | KreativeRock</title>

    <link rel="stylesheet" type="text/css" href="./auth/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="./auth/css/fontawesome-all.min.css">
    <link rel="stylesheet" type="text/css" href="./auth/css/iofrm-style.css">
    <link rel="stylesheet" type="text/css" href="./auth/css/iofrm-theme2.css">
    <link rel="stylesheet" href="./css/index.css">
    
    <!-- favicon -->
    <link href="../../assets/images/favicon.ico" rel="shortcut icon">
    <script src="https://cdn.tailwindcss.com"></script>
    
</head>

<body >
    <div class="form-body">
        <div class="iofrm-layout">
            <div class="img-holder">
                <div class="bg" style="background-image: url('./auth/images/img2.jpg')"></div>
            </div>
            <div class="form-holder overflow-auto">
                <div class="form-content !overflow-auto">
                    <div class="-translate-y-[30px] lg:-translate-y-[0px] md:w-4/5 xl:w-5/6 2xl:!w-3/5">
                        <div class="pb-5 flex items-center justify-center">
                            <img class="logo-size" src="../../images/logo.svg" alt="">
                        </div>
                        
                        <h3>Glad you're joining us!</h3>
                        <p>Access to the most powerful tools to thrive in business</p>
                        <div class="page-links">
                            <a href="login">Login</a>
                            <a href="signup" class="active !bg-transparent">Register</a>
                        </div>
                        
                        <form id="signupform" autocomplete="off">
                            <div class="flex flex-col gap-4">
            
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div class="flex flex-col">
                                        <label for="firstname" class="text-gray-300 font-normal capitalize text-2xs">first name</label>
                                        <input name="firstname" id="firstname" type="text" class="form-control bg-white !text-gray-900 !font-medium !-mt-1">
                                    </div>
                                    <div class="flex flex-col">
                                        <label for="lastname" class="text-gray-300 font-normal capitalize text-2xs">last name</label>
                                        <input name="lastname" id="lastname" type="text" class="form-control bg-white !text-gray-900 !font-medium !-mt-1">
                                    </div>
                                    <div class="flex flex-col">
                                        <label for="othernames" class="text-gray-300 font-normal capitalize text-2xs">other names</label>
                                        <input name="othernames" id="othernames" type="text" class="form-control bg-white !text-gray-900 !font-medium !-mt-1">
                                    </div>

                                    <div class="flex flex-col">
                                        <label for="email" class="text-gray-300 font-normal capitalize text-2xs">email</label>
                                        <input name="email" id="email" type="email" class="form-control bg-white !text-gray-900 !font-medium !-mt-1">
                                    </div>
                                    <div class="flex flex-col">
                                        <label for="phone" class="text-gray-300 font-normal capitalize text-2xs">phone</label>
                                        <input name="phone" id="phone" type="tel" class="form-control bg-white !text-gray-900 !font-medium !-mt-1">
                                    </div>
                                    <div class="flex flex-col">
                                        <label for="address" class="text-gray-300 font-normal capitalize text-2xs">address</label>
                                        <input name="address" id="address" type="text" class="form-control bg-white !text-gray-900 !font-medium !-mt-1">
                                    </div>
 
                                    <div class="flex flex-col col-span-2">
                                        <label for="role" class="text-gray-300 font-normal capitalize text-xs">role</label>
                                        <select readonly="readonly" name="role" id="role" class="form-control bg-white !text-gray-900 !font-medium !-mt-1">
                                            <option value="STAFF" selected="selected">STAFF</option>
                                        </select>
                                    </div>
                                    <div class="flex flex-col">
                                        <label for="password" class="text-gray-300 font-normal capitalize text-2xs">password</label>
                                        <input name="password" id="password" type="password" class="form-control bg-white !text-gray-900 !font-medium !-mt-1">
                                    </div>
                                    <div class="flex flex-col">
                                        <label for="confirmpassword" class="text-gray-300 font-normal capitalize text-2xs">confirm password</label>
                                        <input  id="confirmpassword" type="password" class="form-control bg-white !text-gray-900 !font-medium !-mt-1">
                                    </div>
                                </div>
                        
                                <div class="form-button !flex lg:justify-left">
                                    <button id="submit" type="button" class="ibtn">Register</button> 
                                </div>
                                
                            </div>
                        </form>
                    
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="./js/util.js"></script>
    <script src="./js/signup.js"></script>
</body>

</html>