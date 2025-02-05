<?php
session_start();
if(!isset($_SESSION["user_id"]) || !isset($_SESSION["user_id"]))
{
	header('Location: login.php');
}
if($_SESSION["role"] !== "SUPERADMIN"){
    header('Location: index.php');
}

?>



       <section class="animate__animated animate__fadeIn">
                            <p class="page-title">
                                <span>Access Control</span>
                            </p>
                            <form id="accesscontrolsform">
                                <div class="flex flex-col space-y-3 bg-white/90 p-5 xl:p-10 rounded-sm">
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                        <div class="form-group">
                                            <label for="logoname" class="control-label">Personel</label>
                                            <input type="text" name="email" id="email" list="userslist" class="form-control" onchange="checkdatalist(this);accessboard(this)">
                                        </div>
                                        <div class="form-group">
                                            <label for="logoname" class="control-label">Role</label>
                                            <select name="role" id="role" class="form-control">
                                                <option value=''>-- Select Role --</option>
                                                <option value="ADMIN">ADMIN</option>
                                                <option value="USER">STAFF</option>
                                                <option value="MERCHANT">MERCHANT</option>   
                                                <option value="SUPERADMIN">SUPER ADMIN</option>
                                            </select>
                                        </div> 
                                        <div>
                                             <button id="submit" type="button" class="w-full md:w-max rounded-md text-white text-sm capitalize bg-gradient-to-tr from-amber-400 via-amber-500 to-primary-g px-8  py-3 lg:py-2 shadow-md font-medium hover:opacity-75 transition duration-300 ease-in-out flex items-center justify-center gap-3">
                                            <div class="btnloader" style="display: none;"></div>
                                            <span>Submit</span>
                                        </button>
                                    </div> 
                                    </div>
                                    </div>
                        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                            <div></div>
                            <div></div>
                            <div></div>
                                    <div class="flex justify-end mt-5">
                                         <button id="accesssave" type="button" class="w-full md:w-max rounded-md text-white text-sm capitalize bg-gradient-to-tr from-amber-400 via-amber-500 to-primary-g px-8  py-3 lg:py-2 shadow-md font-medium hover:opacity-75 transition duration-300 ease-in-out flex items-center justify-center gap-3">
                                        <div class="btnloader" style="display: none;"></div>
                                        <span>Save</span>
                                    </button>
                                </div>
                                </div>
                            </form>
                            <hr class="mt-6 mb-2">
                            
                             <div class="">
                                <div class="table-content bg-white p-4 flex flex-wrap justify-center" id="accessctrl_container"></div>
                            </div>
                            
                            <datalist id='departmentlist'></datalist>
                            <datalist id='userslist'></datalist>
                        
                        </section>  