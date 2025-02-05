<div>
    <div class="mx-auto lg:w-4/5 2xl:w-3/5">
        <div class="flex items-center md:justify-between flex-wrap gap-2 mb-10">
            <h4 class="text-default-900 text-2xl font-semibold capitalize">Contacts</h4>
        </div>
        
        <div class="card mx-auto px-5 lg:px-10 py-5">
            <div class="card-body">
                <h3 class="text-2xl font-bold text-default-800">Create your contact list</h3>
                <p class="text-default-500 mb-10 mt-4">
                    This is your contact database. From here, you can create new contacts either individually or uploading a contacts file
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 lg:w-2/3">
                    <button type="button" data-hs-overlay="#overlay-right" class="flex flex-col gap-2 rounded-md border p-5 group cursor-pointer hover:border-amber-500 border-gray-200">
                        <span class="w-12 h-12 rounded-full flex items-center justify-center bg-blue-50 group-hover:bg-amber-50">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-folder-plus text-primary group-hover:text-amber-700"><path d="M12 10v6"/><path d="M9 13h6"/><path d="M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2Z"/></svg>
                        </span>
                        <div class="font-bold text-base text-left">Create a contact</div>
                    </button>
                    <button onclick="router.navigate('/contact/import/options')" class="flex flex-col gap-2 rounded-md border p-5 group cursor-pointer hover:border-amber-500 border-gray-200">
                        <span class="w-12 h-12 rounded-full flex items-center justify-center bg-blue-50 group-hover:bg-amber-50">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-cloud-upload text-green-700 group-hover:text-amber-700"><path d="M12 13v8"/><path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"/><path d="m8 17 4-4 4 4"/></svg>
                        </span>
                        <div class="font-bold text-base text-left">Import contacts</div>
                    </button>
                </div>
            </div> 
        </div>
    </div>
</div>

<div id="overlay-right" class="hs-overlay overflow-hidden hs-overlay-open:translate-x-0 translate-x-full fixed top-0 right-0 transition-all duration-300 transform h-full max-w-md w-full z-70 bg-white border-s border-default-200 hidden"
    tabindex="-1">
    <div class="relative overflow-y-auto h-full"> 
        <div class="fixed top-0 bg-white w-full flex justify-between items-center py-3 px-4 border-b border-default-200">
            <h3 class="text-lg font-black text-default-800">
                Create a contact
            </h3>
            <button type="button" class="hover:text-default-900"
                data-hs-overlay="#overlay-right">
                <span class="sr-only">Close modal</span> 
                <i class="i-tabler-x text-lg"></i>
            </button>
        </div>

        <div class="px-5 py-16">
            <p class="text-default-600 my-1">Carefully enter contact information</p>
            <div class="my-5">
                <form id="contactform" class="flex flex-col gap-3">
                    <div class="flex flex-col gap-1">
                        <label class="text-default-800 text-sm font-medium inline-block">First name</label>
                        <input type="text" class="form-input" name="firstname" id="firstname" placeholder="Enter First name">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-default-800 text-sm font-medium inline-block">Last name</label>
                        <input type="text" class="form-input" name="lastname" id="lastname" placeholder="Enter Last name">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-default-800 text-sm font-medium inline-block">Email Address</label>
                        <input type="email" class="form-input" name="email" id="email" placeholder="Enter email">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-default-800 text-sm font-medium inline-block">SMS</label>
                        <input type="text" class="form-input" data-toggle="input-mask" data-mask-format="(000) 000-000 0000" placeholder="(234) xxx-xxx-xxxx" name="sms" id="sms">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-default-800 text-sm font-medium inline-block">WhatsApp</label>
                        <input type="text" class="form-input" data-toggle="input-mask" data-mask-format="(000) 000-000 0000" placeholder="(234) xxx-xxx-xxxx" name="whatsapp" id="whatsapp">
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-default-800 text-sm font-medium inline-block">Segment</label>
                        <select type="text" class="form-input" data-toggle="input-mask" placeholder="(234) xxx-xxx-xxxx" name="segment" id="segment">
                            <option value="">Select Segment</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="flex flex-wrap items-center justify-between gap-3 fixed bottom-0 bg-white border-t p-3 w-full">
            <button data-hs-overlay="#overlay-right" type="button" class="btn rounded-full bg-primary/25 text-primary hover:bg-primary hover:text-white">Cancel</button>
            <button id="submit" type="button" class="btn rounded-full bg-dark text-white">Create</button>
        </div>
    </div>
</div>