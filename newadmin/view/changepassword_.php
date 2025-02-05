<form id="changepasswordform">
    <div class="flex flex-col space-y-3 bg-white p-5 xl:p-10 rounded-sm">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="form-group">
                <label for="logoname" class="text-default-800 text-sm font-medium inline-block">Current Password</label>
                <input type="text" name="oldupw" id="oldupw" class="form-input">
            </div>
             <div class="form-group">
                <label for="logoname" class="text-default-800 text-sm font-medium inline-block">New Password</label> 
                <input type="text" name="newupw" id="newupw" class="form-input">
            </div>
             <div class="form-group">
                <label for="logoname" class="text-default-800 text-sm font-medium inline-block">Confirm Password</label>
                <input type="text" name="newupw2" id="newupw2" class="form-input">
            </div>
        </div> 
    </div>
    <div class="flex gap-3 3xl:gap-1 flex-col md:flex-row items-center mt-10">
        <button  id="submit" type="button" class="btn bg-primary text-white rounded-full">Update</button>
    </div>
</form>