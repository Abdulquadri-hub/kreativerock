
    <section class="h-full overflow-y-auto">
        <div class="text-primary-g font-heebo font-bold text-base uppercase md:w-2/3 xl:w-1/3 3xl:w-2/5 mx-auto text-center mt-10 lg:mb-10"> 
            <!--He<span class="text-gray-400">ms</span>-->
        </div> 
        <div class="bg-white xl:border w-[90%] mx-auto rounded py-14 px-12 drop-shadow-sm pt-4">
            <h1 class="font-bold text-2xl text-center">Profile</h1>
            <!--<p class="mt-5 text-xs text-gray-400 tracking-wider leading-relaxed font-sans text-center">Provide the information below to register a new account</p>-->
            <div class="flex flex-col w-5/6 m-auto items-center py-5 sticky top-0 bg-white border-b border-gray-200/50">
                <span class="w-[50px] h-auto lg:w-[60px] rounded-full overflow-hidden">
                    <img src="./images/default-avatar.png" alt="user Avater" class="w-full h-auto object-center">
                </span>
                <span class="font-extrabold text-normal font-mont capitalize mt-2">John  I. Doe</span>
                <span class="rounded-full text-white text-3xs font-bold capitalize bg-amber-500 px-2 py-0.5 text-center">Admin</span>
            </div>
            <form class="mt-10" id="profilesform" autocomplete="of">
                <div class="py-3 mb-5 border-b text-sm font-bold uppercase">Personal Information</div>
                <div class="grid grid-col-1 lg:grid-cols-3 gap-4">
                    <div class="form-group">
                        <label for="firstname" class="control-label">first name</label>
                        <input name="firstname" id="firstname" type="text" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="lastname" class="control-label">last name</label>
                        <input name="lastname" id="lastname" type="text" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="othernames" class="control-label">other names</label>
                        <input name="othernames" id="othernames" type="text" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="email" class="control-label">email</label>
                        <input name="email" id="email" type="email" readonly class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="phone" class="control-label">phone</label>
                        <input name="phone" id="phone" type="tel" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="dateofbirth" class="control-label">date of birth</label>
                        <input  id="dateofbirth" type="date" name="dateofbirth" class="form-control">
                    </div>
                    <div class="form-group col-span-full">
                        <label for="address" class="control-label">address</label>
                        <input name="address" id="address" type="text" class="form-control">
                    </div>
                    <div class="form-group hidden">
                        <label for="role" class="control-label">role</label>
                        <select readonly="readonly" name="role" id="role" class="form-control">
                            <option value="STAFF" selected="selected">STAFF</option>
                            <option id="MERCHANT" class="hidden">MERCHANT</option>
                            <option id="SUPERADMIN" class="hidden">SUPERADMIN</option>
                        </select>
                    </div>
                    <div class="form-group hidden">
                        <label for="location_name" class=" control-label">location name</label>
                        <input  id="location_name" type="text" name="location_name" class="form-control">
                    </div>
                </div>
                <div class="py-5 mb-5 border-b text-sm font-bold uppercase">Other Information</div>
                <div class="grid grid-col-1 lg:grid-cols-3 gap-4">
                    <div class="form-group">
                        <label for="country" class="control-label">country</label>
                        <select name="country" id="country" class="form-control">
                            <option value="" selected=""> --Select Country --</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="timezone" class="control-label">Timezone</label>
                        <input name="timezone" id="timezone" type="text" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="organisation" class="control-label">organisation name</label>
                        <input name="organisation" id="organisation" type="text" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="industry" class="control-label">Industry</label>
                        <select name="industry" id="industry" type="text" class="form-control">
                            <option value="" selected=""> --Select Industry --</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="positioninthecompany" class="control-label">position</label>
                        <input list="positions" name="positioninthecompany" id="positioninthecompany" type="text" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="currency" class="control-label">currency</label>
                        <input name="currency" id="currency" type="text" class="form-control">
                    </div>
                </div>
                
                <div class="py-5 mb-5 border-b text-sm font-bold uppercase">Identification Information</div>
                <div class="grid grid-col-1 gap-4">
                    <div class="form-group">
                        <label for="identificationtype" class="control-label">identification type</label>
                        <select name="identificationtype" id="identificationtype" class="form-control">
                            <option value="INTERNATIONAL PASSPORT" selected="selected">INTERNATIONAL PASSPORT</option>
                            <option value="DRIVERS LICENSE">DRIVERS LICENSE</option>
                            <option value="NATIONAL ID">NATIONAL ID</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="imageurl1" class="control-label">Upload Identification</label>
                        <div id="imagePreview"></div>
                        <input type="file" id="imageurl1" class="form-control" onchange="previewImage('imageurl1')">
                    </div>
                </div>
                
                <div class="flex gap-3 3xl:gap-1 flex-col md:flex-row items-center mt-10">
                    <button id="submit" type="button" class="w-full md:w-max rounded-md text-white text-sm capitalize bg-gradient-to-tr from-amber-400 via-amber-500 to-primary-g px-8  py-3 lg:py-2 shadow-md font-medium hover:opacity-75 transition duration-300 ease-in-out flex items-center justify-center gap-3">
                        <div class="btnloader" style="display: none;" ></div>
                        <span>Update</span>
                    </button>
                </div>
            </form>
        </div>
    </section>
