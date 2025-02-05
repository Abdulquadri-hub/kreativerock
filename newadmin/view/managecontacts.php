<div class="mb-10 flex gap-5 lg:flex-row lg:justify-between flex-col">
    <div>
        <h4 class="text-default-900 text-2xl font-semibold capitalize">Contacts</h4>
        <p class="text-default-500 mt-1">This is your contact database. From here, you can view, organize and manage your contacts individually</p>
    </div>
    <div class="flex items-center gap-2">
        <button type="button" data-hs-overlay="#overlay-right"type="button" class="btn bg-light text-default-900 rounded-full">Create contacts</button>
        <button onclick="router.navigate('/contact/import/options')" type="button" class="btn bg-dark text-white rounded-full">Import contacts</button>
    </div>
</div>

<div class="card rounded-lg">
    <div class="card-body">
        <div>
            <div class="flex justify-between gap-3 pb-5">
                <span class="font-extrabold text-lg" name="totalcontacts">0 Contacts</span>
                <div>
                    <div class="hs-dropdown relative [--placement:left-top]">
                        <button type="button" class="capitalize hs-dropdown-toggle py-2 px-5 inline-block font-medium tracking-wide border align-middle duration-500 text-sm text-center text-default-900 bg-default-100 hover:bg-default-200 border-default-100 hover:border-default-200 rounded-md">
                            Group<i class="i-tabler-chevron-down ms-1"></i>
                        </button>

                        <div name="contact-group" class="hs-dropdown-menu hs-dropdown-open:opacity-100 min-w-48 transition-[opacity,margin] mt-4 opacity-0 z-10 bg-white shadow-lg rounded-lg border border-default-100 p-1.5 hidden">
                            <ul class="flex flex-col gap-1"></ul>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="hs-dropdown relative [--placement:left-top]">
                        <button type="button" class="capitalize hs-dropdown-toggle py-2 px-5 inline-block font-medium tracking-wide border align-middle duration-500 text-sm text-center text-default-900 bg-default-100 hover:bg-default-200 border-default-100 hover:border-default-200 rounded-md">
                            Segment<i class="i-tabler-chevron-down ms-1"></i>
                        </button>

                        <div name="contact-group" class="hs-dropdown-menu hs-dropdown-open:opacity-100 min-w-48 transition-[opacity,margin] mt-4 opacity-0 z-10 bg-white shadow-lg rounded-lg border border-default-100 p-1.5 hidden">
                            <ul class="flex flex-col gap-1">
                                <li class="flex items-center gap-2 py-2 px-4 text-sm text-default-700 transition-colors duration-300 hover:bg-default-50 hover:text-default-900 rounded-md">
                                    <i class="i-tabler-user-circle text-default-500"></i>
                                    <span>All</span>
                                </li>
                                <li class="flex items-center gap-2 py-2 px-4 text-sm text-default-700 transition-colors duration-300 hover:bg-default-50 hover:text-default-900 rounded-md">
                                    <i class="i-tabler-user-circle text-default-500"></i>
                                    <span>Customer</span> 
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="table-content">
                <div class="overflow-x-auto">
                    <div class="min-w-full inline-block align-middle">
                        <div class="border rounded-lg overflow-hidden">
                            <table class="min-w-full divide-y divide-default-200">
                                <thead class="bg-default-50">
                                    <tr>
                                        <th scope="col" class="py-3 ps-4">
                                            <div class="flex items-center h-5">
                                                <input id="table-checkbox-all" type="checkbox"
                                                    class="form-checkbox rounded">
                                                <label for="table-checkbox-all" class="sr-only">Checkbox</label>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-start text-sm text-default-500">First&nbsp;Name</th>
                                        <th scope="col" class="px-6 py-3 text-start text-sm text-default-500">Last&nbsp;Name</th>
                                        <th scope="col" class="px-6 py-3 text-start text-sm text-default-500">Email</th>
                                        <th scope="col" class="px-6 py-3 text-start text-sm text-default-500">SMS</th>
                                        <th scope="col" class="px-6 py-3 text-start text-sm text-default-500">Whatsapp</th>
                                        <th scope="col" class="px-6 py-3 text-start text-sm text-default-500">Landline</th>
                                        <th scope="col" class="px-6 py-3 text-start text-sm text-default-500">Date</th>
                                        <th scope="col" class="px-6 py-3 text-start text-sm text-default-500">Segment</th>
                                    </tr>
                                </thead>
                                <tbody id="tabledata" class="divide-y divide-default-200"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-5 items-center mt-3">
                <span class="text-blue-600 capitalize">select contact and update their segment</span> <div class="hs-dropdown relative [--placement:left-top]">
                                <button type="button" class="capitalize hs-dropdown-toggle py-2 px-5 inline-block font-medium tracking-wide border align-middle duration-500 text-sm text-center text-default-900 bg-default-100 hover:bg-default-200 border-default-100 hover:border-default-200 rounded-md">
                                    Update Segment<i class="i-tabler-chevron-down ms-1"></i>
                                </button>

                                <div name="contact-group" class="hs-dropdown-menu hs-dropdown-open:opacity-100 min-w-48 transition-[opacity,margin] mt-4 opacity-0 z-10 bg-white shadow-lg rounded-lg border border-default-100 p-1.5 hidden">
                                    <ul class="flex flex-col gap-1">
                                        <li class="flex items-center gap-2 py-2 px-4 text-sm text-default-700 transition-colors duration-300 hover:bg-default-50 hover:text-default-900 rounded-md">
                                            <i class="i-tabler-user-circle text-default-500"></i>
                                            <span>All</span>
                                        </li>
                                        <li class="flex items-center gap-2 py-2 px-4 text-sm text-default-700 transition-colors duration-300 hover:bg-default-50 hover:text-default-900 rounded-md">
                                            <i class="i-tabler-user-circle text-default-500"></i>
                                            <span>Customer</span> 
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="form-button">
                                  <button type="button" id="submit" class="btn bg-primary text-white rounded-full">Update</button>        
                            </div>
            </div>
            <div class="table-status flex justify-between lg:items-end mt-5">
                <span class="text-xs text-gray-500" id="pagination-status">Showing 0 - 0 of 0</span>
                <span class="flex justify-between gap-6 border rounded-md p-2 bg-white">
                    <span>
                        <select id="pagination-limit" class="!border-transparent !outline-none !ring-0 cursor-pointer min-w-[70px] bg-gray-50 rounded-md p-2">
                            <option value="20">20</option>
                            <option value="30">30</option>
                            <option value="35">35</option>
                            <option value="40">40</option>
                            <option value="70">70</option>
                            <option value="100">100</option>
                            <option value="150">150</option>
                            <option value="200">200</option>
                            <option value="250">250</option>
                            <option value="500">500</option>
                            <option value="750">750</option>
                            <option value="1000">1000</option>
                            <option value="1500">1500</option>
                        </select>
                    </span>
                    <span class="flex pagination">
                        <button type="button" id="pagination-prev-button" disabled="true">previous</button>
                        <span id="pagination-numbers">
                            <button class="pagination-number" page-index="1" type="button" aria-label="Page 1">1</button>
                        </span>
                        <button type="button" id="pagination-next-button" disabled="true">next</button>
                    </span>
                </span>
            </div>
        </div>
    </div>
</div>

<div>
    <div id="contact-panel"class="hs-overlay hs-overlay-open:translate-x-0 translate-x-full fixed top-0 right-0 transition-all duration-300 transform h-full max-w-xs w-full z-70 bg-white border-s border-default-200 hidden"
        tabindex="-1">
            <div class="relative overflow-y-auto h-full"> 
                <div
                    class="fixed top-0 bg-white w-full flex justify-between items-center py-3 px-4 border-b border-default-200">
                    <h3 class="text-lg font-bold text-default-600">
                        Contacts
                    </h3>
                    <button type="button" class="hover:text-default-900"
                        data-hs-overlay="#contact-panel">
                        <span class="sr-only">Close modal</span>
                        <i class="i-tabler-x text-lg"></i>
                    </button>
                </div>
                <div class="mt-10 p-4 text-default-600 overflow-y-auto">
                    <div class="mt-1 flex flex-col divide-y" name="contacts-list">
                        <div class="flex flex-col items-center justify-center py-5 gap-4">
                            <div class="font-medium">No Contacts.</div>
                            <a data-navigo href="contact/list" type="button" class="btn rounded-full border border-primary text-primary hover:bg-primary hover:text-white">Create Contact</a>
                        </div>
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
