<div>
    <div class="mx-auto lg:w-4/5 2xl:w-3/5">
        <div class="flex items-center md:justify-between flex-wrap gap-2 mb-5">
            <h4 class="text-default-900 text-2xl font-semibold capitalize">Import contacts with copy/paste</h4>
            <p class="text-default-500 mb-10 mt-1">Copy your contacts and their information from a file and paste them into Brevo or type everything directly in the field. This is particularly useful when you have a small number of contacts to import.</p>
        </div>
        
        <div class="card rounded-lg">
            <div class="card-body">
                <div class="lg:p-6">
                	<div>
                		<div class="flex flex-col lg:flex-row gap-3 lg:items-end lg:justify-between w-full pb-5">
                		    <div class="flex flex-col gap-2">
                		        <div class="font-bold text-xl">Import your data</div>
                		        <div>Copy and paste your contacts and their information from a file.</div>
                		    </div>
                		    <button onclick="document.getElementById('expectedresponse').classList.toggle('hidden')" class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-help"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg>
                		        <span class="underline hover:text-primary cursor-pointer">Show the expected syntax</span>
                		    </button>
                		</div>
                		
                		<div class="bg-white p-4 rounded-md border my-5 hidden" id="expectedresponse">
                            <div class="flex items-center justify-between mb-2">
                                <div class="font-bold text-sm">Example of expected syntax</div>
                                <button onclick="copySyntax()" class="ml-4 text-sm font-medium text-blue-600 bg-blue-50 rounded-full px-4 py-2">Copy</button>
                            </div>
                        
                            <div class="overflow-y-auto max-h-48">
                                <div class="bg-gray-100 p-4 rounded-md" id="syntaxContent">
                                    <span>CONTACT_ID,EMAIL,FIRSTNAME,LASTNAME,SMS,LANDLINE_NUMBER,WHATSAPP</span>
                                    <span>123456,emma@example.com,Emma,Dubois,33612345678,33612345678,33612345678</span>
                                    <span>789123,mickael@example.com,Mickael,Parker,15555551234,15555551234,15555551234</span>
                                    <span>456789,ethan@example.com,Jakob,Müller,4930901820,4930901820,4930901820</span>
                                </div>
                            </div>
                        
                            <div class="mt-4">
                                <ul class="list-disc list-inside">
                                    <li>The header row is compulsory (CONTACT_ID, EMAIL, FIRSTNAME...).</li>
                                    <li>Separate contact attribute with a comma, semicolon, or tab.</li>
                                    <li>
                                        To add several values to a multiple-choice attribute, follow this
                                        format:
                                        <br />
                                    </li>
                                </ul>
                                <div class="ml-5">
                                    ['Option1' 'Option2' 'Option3'].
                                    <br />
                                    Example: to add several values to the multiple-choice attribute
                                    "Interests", write
                                    <br />
                                    ['music' 'sports'].
                                </div>
                            </div>
                        </div>

                        <div class="hs-dropdown relative [--placement:left-top]">
                                <button type="button" class="capitalize hs-dropdown-toggle py-2 px-5 inline-block font-medium tracking-wide border align-middle duration-500 text-sm text-center text-default-900 bg-default-100 hover:bg-default-200 border-default-100 hover:border-default-200 rounded-md">
                                    ContactSegment<i class="i-tabler-chevron-down ms-1"></i>
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

                		<div class="cursor-pointer flex justify-center bg-white border border-default-300 rounded-xl overflow-hidden bg-gray-100 pl-5">
                		    <textarea id="contactTextarea" rows="15" class="!border-transparent !outline-none !ring-0 resize-none h-full w-full placeholder:text-gray-400 text-sm" placeholder="CONTACT_ID,EMAIL,FIRSTNAME,LASTNAME,SMS,LANDLINE_NUMBER,WHATSAPP 123456,emma@example.com,Emma,Dubois,33612345678,33612345678,33612345678 789123,mickael@example.com,Mickael,Parker,15555551234,15555551234,15555551234 456789,ethan@example.com,Jakob,Müller,4930901820,4930901820,4930901820"></textarea>
                		</div>
                		
                		<div class="flex justify-end py-2">
            		        <button id="importButton" disabled type="button" class="btn rounded-full bg-primary/25 text-primary hover:bg-primary hover:text-white">Import</button>
            		    </div>
                	</div>
                </div>
            </div> 
        </div>
    </div>
</div>

<!-- contacts preview -->
<div id="modal">
    <button type="button" data-hs-overlay="#modal-five" name="modal" class="[--overlay-backdrop:static]"></button>
    <div id="modal-five" class="[--overlay-backdrop:static] hs-overlay w-full h-full fixed top-0 left-0 z-70 transition-all duration-500 overflow-y-hidden hidden pointer-events-none">
        <div class="-translate-y-5 hs-overlay-open:translate-y-0 hs-overlay-open:opacity-100 opacity-0 ease-in-out transition-all duration-500 sm:max-w-6xl sm:w-full my-8 sm:mx-auto flex flex-col bg-white shadow-sm rounded">
            <div class="flex flex-col border border-default-200 shadow-sm rounded-lg  pointer-events-auto h-[90vh] relative overflow-y-auto">
                <div class="flex justify-between items-center py-3 px-4 border-b border-default-200">
                    <h3 class="text-lg font-medium text-default-900 flex gap-2 justify-between w-full">
                        <span>Mapped Contacts</span>
                        <span name="delete" class="px-3"></span>
                    </h3>
                    <button type="button" class="text-default-600 cursor-pointer" onclick="closeImportProcess()">
                        <i class="i-tabler-x text-lg"></i>
                    </button>
                </div>
                <div class="p-4 overflow-y-auto">
                    <div class="p-4">
                        <div class="overflow-x-auto">
                            <div class="min-w-full inline-block align-middle">
                                <div class="border rounded-lg overflow-hidden">
                                    <table class="min-w-full divide-y divide-default-200">
                                        <thead class="bg-default-50"></thead>
                                        <tbody class="divide-y divide-default-200"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div name="appender" class="flex items-center hidden mt-5">
                        <button class="bg-gray-800 text-white text-xs px-4 py-2 cursor-pointer">Load More</button>
                    </div>
                </div>
                <div class="border-t border-default-200 fixed bottom-0 w-full">
                    <div class="flex flex-col lg:items-center lg:flex-row gap-2 lg:justify-between">
                        <span class="table-status pl-3"></span>
                        <div class="flex justify-end items-center gap-x-2 py-3 px-4 ">
                            <button class="btn rounded-full bg-danger/25 text-danger hover:bg-danger hover:text-white" data-hs-overlay="#modal-five" onclick="closeImportProcess()">
                                <i class="i-tabler-x me-1"></i>
                                Close
                            </button>
                            <button class="btn bg-primary text-white rounded-full" onclick="createContactSubmitHandler(this)">
                                Create Contact
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>