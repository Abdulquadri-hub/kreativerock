
<section class="animate__animated animate__fadeIn">
        <p class="page-title">
            <span>Change Password</span>
        </p>
        <form id="changepasswordform">
            <div class="flex flex-col space-y-3 bg-white p-5 xl:p-10 rounded-sm">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="form-group">
                        <label for="logoname" class="control-label">Current Password</label>
                        <input type="text" name="oldupw" id="oldupw" class="form-control">
                    </div>
                     <div class="form-group">
                        <label for="logoname" class="control-label">New Password</label> 
                        <input type="text" name="newupw" id="newupw" class="form-control">
                    </div>
                     <div class="form-group">
                        <label for="logoname" class="control-label">Confirm Password</label>
                        <input type="text" name="newupw2" id="newupw2" class="form-control">
                    </div>
                </div> 
            </div>
            <div class="flex gap-3 3xl:gap-1 flex-col md:flex-row items-center mt-10">
                <button id="submit" type="button" class="w-full md:w-max rounded-md text-white text-sm capitalize bg-gradient-to-tr from-amber-400 via-amber-500 to-primary-g px-8  py-3 lg:py-2 shadow-md font-medium hover:opacity-75 transition duration-300 ease-in-out flex items-center justify-center gap-3">
                    <div class="btnloader" style="display: none;" ></div>
                    <span>Update</span>
                </button>
                
            </div>
        </form>
        <hr class="my-10">
    
    </section>  