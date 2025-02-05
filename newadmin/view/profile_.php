<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 lg:gap-10">
    <div >
        <div class="rounded-md bg-white border p-5" name="profilecard">
            <img class="inline-block size-[62px] rounded-full" src="./images/default-avatar.png">
            <div class="font-bold text-xl mt-4"></div>
            <div class="text-muted text-xs mb-4"></div>
            <div class="flex gap-1 items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-toggle-right"><rect width="20" height="12" x="2" y="6" rx="6" ry="6"/><circle cx="16" cy="12" r="2"/></svg>
                <span></span>
            </div>
        </div>
    </div>
    <div class="lg:col-span-2">
        <div class="rounded-md bg-white border p-5">
            <form id="profilesform" autocomplete="of">
                <div class="py-3 mb-5 border-b text-sm font-bold uppercase">Personal Information</div>
                <div class="grid grid-col-1 lg:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label for="firstname" class="text-default-800 text-sm font-medium inline-block">first name</label>
                        <input name="firstname" id="firstname" type="text" class="form-input">
                    </div>
                    <div class="form-group">
                        <label for="lastname" class="text-default-800 text-sm font-medium inline-block">last name</label>
                        <input name="lastname" id="lastname" type="text" class="form-input">
                    </div>
                    <div class="form-group">
                        <label for="othernames" class="text-default-800 text-sm font-medium inline-block">other names</label>
                        <input name="othernames" id="othernames" type="text" class="form-input">
                    </div>
        
                    <div class="form-group">
                        <label for="email" class="text-default-800 text-sm font-medium inline-block">email</label>
                        <input name="email" id="email" type="email" readonly class="form-input">
                    </div>
                    <div class="form-group">
                        <label for="phone" class="text-default-800 text-sm font-medium inline-block">phone</label>
                        <input name="phone" id="phone" type="tel" class="form-input">
                    </div>
                    <div class="form-group">
                        <label for="dateofbirth" class="text-default-800 text-sm font-medium inline-block">date of birth</label>
                        <input  id="dateofbirth" type="date" name="dateofbirth" class="form-input">
                    </div>
                    <div class="form-group col-span-full">
                        <label for="address" class="text-default-800 text-sm font-medium inline-block">address</label>
                        <input name="address" id="address" type="text" class="form-input">
                    </div>
                    <div class="form-group hidden">
                        <label for="role" class="text-default-800 text-sm font-medium inline-block">role</label>
                        <select readonly="readonly" name="role" id="role" class="form-input">
                            <option value="STAFF" selected="selected">STAFF</option>
                            <option id="MERCHANT" class="hidden">MERCHANT</option>
                            <option id="SUPERADMIN" class="hidden">SUPERADMIN</option>
                        </select>
                    </div>
                    <div class="form-group hidden">
                        <label for="location_name" class=" text-default-800 text-sm font-medium inline-block">location name</label>
                        <input  id="location_name" type="text" name="location_name" class="form-input">
                    </div>
                </div>
                <div class="py-5 mb-5 border-b text-sm font-bold uppercase">Other Information</div>
                <div class="grid grid-col-1 lg:grid-cols-3 gap-4">
                    <div class="form-group">
                        <label for="country" class="text-default-800 text-sm font-medium inline-block">country</label>
                        <select name="country" id="country" class="form-input">
                            <option value="" selected=""> --Select Country --</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="timezone" class="text-default-800 text-sm font-medium inline-block">Timezone</label>
                        <input name="timezone" id="timezone" type="text" class="form-input">
                    </div>
                    <div class="form-group">
                        <label for="organisation" class="text-default-800 text-sm font-medium inline-block">organisation name</label>
                        <input name="organisation" id="organisation" type="text" class="form-input">
                    </div>
                    <div class="form-group">
                        <label for="industry" class="text-default-800 text-sm font-medium inline-block">Industry</label>
                        <select name="industry" id="industry" type="text" class="form-input">
                            <option value="" selected=""> --Select Industry --</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="positioninthecompany" class="text-default-800 text-sm font-medium inline-block">position</label>
                        <input list="positions" name="positioninthecompany" id="positioninthecompany" type="text" class="form-input">
                    </div>
                    <div class="form-group">
                        <label for="currency" class="text-default-800 text-sm font-medium inline-block">currency</label>
                        <input name="currency" id="currency" type="text" class="form-input">
                    </div>
                </div>
                
                <div class="py-5 mb-5 border-b text-sm font-bold uppercase">Identification Information</div>
                <div class="grid grid-col-1 gap-4">
                    <div class="form-group">
                        <label for="identificationtype" class="text-default-800 text-sm font-medium inline-block">identification type</label>
                        <select name="identificationtype" id="identificationtype" class="form-input">
                            <option value="INTERNATIONAL PASSPORT" selected="selected">INTERNATIONAL PASSPORT</option>
                            <option value="DRIVERS LICENSE">DRIVERS LICENSE</option>
                            <option value="NATIONAL ID">NATIONAL ID</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="imageurl1" class="text-default-800 text-sm font-medium inline-block">Upload Identification</label>
                        <div id="imagePreview"></div>
                        <input type="file" id="imageurl1" class="form-input" onchange="previewImage('imageurl1')">
                    </div>
                </div>
                
                <div class="flex gap-3 3xl:gap-1 flex-col md:flex-row items-center mt-10">
                    <button type="button" id="submit" class="btn bg-primary text-white rounded-full">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
