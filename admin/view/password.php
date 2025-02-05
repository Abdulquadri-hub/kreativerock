<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | KreativeRock</title>

    <link rel="stylesheet" type="text/css" href="./auth/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="./auth/css/fontawesome-all.min.css">
    <link rel="stylesheet" type="text/css" href="./auth/css/iofrm-style.css">
    <link rel="stylesheet" type="text/css" href="./auth/css/iofrm-theme2.css">
    <link rel="stylesheet" href="./css/index.css">
    
    <!-- favicon -->
    <link href="../../assets/images/favicon.ico" rel="shortcut icon">
    <script src="https://cdn.tailwindcss.com"></script>
    
</head>

<body>
    <div class="form-body">
        <div class="iofrm-layout">
            <div class="img-holder">
                <div class="bg" style="background-image: url('./auth/images/img2.jpg')"></div>
            </div>
            <div class="form-holder">
                <div class="form-content oveflow-scoll">
                    <div class="form-items">
                        <h3>Password Reset!</h3>
                        <p>To reset your password, enter the email address you use to sign in to KreativeRock</p>
                        <div class="page-links">
                            <a href="login">Back to Login</a>
                        </div>
                        
                        <form id="passwordform" autocomplete="off">
                            <div class="flex flex-col gap-3">
                                <div class="flex flex-col gap-1">
                                    <input class="form-control bg-white !text-gray-900 !font-medium"  autofocus="on" name="email" id="email" type="email" placeholder="doe@example.com" required>
                                </div>
                                
                                <<div class="form-button full-width">
                                    <button id="submit" type="submit" class="ibtn btn-forget">Send Reset Link</button>
                                </div>
                            
                            </div>
                        </form>
                    
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="./js/util.js"></script>
    <script src="./js/password.js"></script>
</body>

</html>