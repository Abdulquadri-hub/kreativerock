<?php
    session_start();
    if(!isset($_SESSION["user_id"]) || !isset($_SESSION["user_id"]))
    {
    	header('Location: /kreativerock/newadmin/view/login');
    }
    if($_SESSION["role"] !== "SUPERADMIN"){
        header('Location: /kreativerock/newadmin/view/login');
    }
?>

<form id="accesscontrolsform">
    <div class="flex flex-col space-y-3 bg-white/90 p-5 xl:p-10 rounded-sm">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="flex flex-col gap-1">
                <label for="logoname" class="text-default-800 text-sm font-medium inline-block">Personel</label>
                <select type="text" list="userslist" id="email" name="email" class="form-input" onchange="accessboard(this)"></select>
            </div>
    
            <div class="flex flex-col gap-1">
                <label for="logoname" class="text-default-800 text-sm font-medium inline-block">Role</label>
                <select name="role" id="role" class="form-input">
                    <option value=''>-- Select Role --</option>
                    <option value="ADMIN">ADMIN</option>
                    <option value="USER">STAFF</option>
                    <option value="MERCHANT">MERCHANT</option>
                    <option value="SUPERADMIN">SUPER ADMIN</option>
                </select>
            </div>
            <div>
                <button id="submit" type="button" class="btn rounded-full border border-primary text-primary hover:bg-primary hover:text-white">Submit</button>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <div></div>
        <div></div>
        <div></div>
        <div class="flex justify-end mt-5">
            <button type="button" id="accesssave" class="btn bg-primary text-white rounded-full">Save</button>
        </div>
    </div>
</form>
<hr class="mt-6 mb-2">

<div class="">
    <div class="table-content bg-white p-4 flex flex-wrap justify-center" id="accessctrl_container"></div>
</div>

<datalist id='departmentlist'></datalist>
<datalist id='userslist'></datalist>
<datalist id="userList">
  <option value="John Doe"></option>
  <option value="Jane Smith"></option>
  <option value="Alice Brown"></option>
</datalist>