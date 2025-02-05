<div>
    <div class="mx-auto lg:w-4/5 2xl:w-3/5">
        <div class="flex items-center md:justify-between flex-wrap gap-2 mb-5">
            <h4 class="text-default-900 text-2xl font-semibold capitalize">Import contacts from a file</h4>
            <p class="text-default-500 mb-10 mt-1">Upload a file containing all your contacts and their information. This is particularly useful when you have a large number of contacts to import.</p>
        </div>
        
        <div class="card rounded-lg">
            <div class="card-body">
                <div class="lg:p-6">
                	<div>
                		<template data-hs-file-upload-preview="">
                			<div class="p-3 bg-white border border-solid border-default-300 rounded-xl">
                				<div class="mb-1 flex justify-between items-center">
                					<div class="flex items-center gap-x-3">
                						<span class="size-10 flex justify-center items-center border border-default-200 text-default-500 rounded-lg">
                							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-code-2">
                							    <path d="M4 22h14a2 2 0 0 0 2-2V7l-5-5H6a2 2 0 0 0-2 2v4"/>
                							    <path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="m5 12-3 3 3 3"/><path d="m9 18 3-3-3-3"/>
                							</svg>
                						</span>
                						<div>
                							<p class="text-sm font-medium text-default-800">
                								<span class="truncate inline-block max-w-[300px] align-bottom" data-hs-file-upload-file-name=""></span>
                								.<span data-hs-file-upload-file-ext=""></span>
                							</p>
                						</div>
                					</div>
                				</div>
                			</div>
                			<div class="flex justify-end py-2">
                		        <button type="button" onclick="parseImport(event)" class="btn rounded-full bg-primary/25 text-primary hover:bg-primary hover:text-white">Import</button>
                		    </div>
                		</template>
                		
                		<div class="flex flex-col lg:flex-row gap-3 lg:items-end lg:justify-between w-full pb-5">
                		    <div class="flex flex-col gap-2">
                		        <div class="font-bold text-xl">Upload Your File</div>
                		        <div>Select a file containing your contacts to import</div>
                		    </div>
                		    <a href="./assets/files/CSV_contact_sample.csv" dowload class="flex items-center underline hover:text-primary cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-cloud-download"><path d="M12 13v8l-4-4"/><path d="m12 21 4-4"/><path d="M4.393 15.269A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.436 8.284"/></svg>
                		        <span>Download example file (.csv)</span>
                		    </a>
                            <div class="hs-dropdown relative [--placement:left-top]">
                                <button type="button" class="capitalize hs-dropdown-toggle py-2 px-5 inline-block font-medium tracking-wide border align-middle duration-500 text-sm text-center text-default-900 bg-default-100 hover:bg-default-200 border-default-100 hover:border-default-200 rounded-md">
                                    Contact Segment<i class="i-tabler-chevron-down ms-1"></i>
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
                
                		<div class="cursor-pointer p-12 flex justify-center bg-white border border-dashed border-default-300 rounded-xl" data-hs-file-upload-trigger="file">
                			<div class="text-center">
                				<span
                					class="inline-flex justify-center items-center size-16 bg-default-100 text-default-800 rounded-full">
                					<i class="i-tabler-upload size-6 shrink-0"></i>
                				</span>
                
                				<div class="mt-4 flex flex-wrap justify-center text-sm leading-6 text-default-600">
                					<span class="pe-1 font-medium text-default-800">
                						Click 
                						<span class="font-semibold text-primary hover:text-primary-700 decoration-2 hover:underline">browse</span>
                						to choose your file
                					</span>
                				</div>
                
                				<p class="mt-1 text-xs text-default-400">.csv or txt file only</p>
                			</div>
                		</div>
                		
                		<div class="mt-4 space-y-2 empty:mt-0" data-hs-file-upload-previews=""></div>
                		<input type="file" class="hidden" id="file">
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
                    <button type="button" class="text-default-600 cursor-pointer" onclick="alertContactClose()">
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
                <div class="border-t border-default-200 fixed bottom-0 w-full bg-white">
                    <div class="flex flex-col lg:items-center lg:flex-row gap-2 lg:justify-between">
                        <span class="table-status pl-3"></span>
                        <div class="flex justify-end items-center gap-x-2 py-3 px-4 ">
                            <button type="button" class="btn rounded-full bg-danger/25 text-danger hover:bg-danger hover:text-white" onclick="alertContactClose()">
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

<!-- close alert -->
<div class="">
    <button type="button" data-hs-overlay="#modal-three" name="cancel-modal" class="[--overlay-backdrop:static]"></button>
    <div id="modal-three" class="[--overlay-backdrop:static] hs-overlay w-full h-full fixed top-0 left-0 z-70 transition-all duration-500 overflow-y-auto hidden pointer-events-none">
        <div
            class="-translate-y-5 hs-overlay-open:translate-y-0 hs-overlay-open:opacity-100 opacity-0 ease-in-out transition-all duration-500 sm:max-w-lg sm:w-full my-8 sm:mx-auto flex flex-col bg-white shadow-sm rounded">
            <div
                class="flex flex-col border border-default-200 shadow-sm rounded-lg  pointer-events-auto">
                <div
                    class="flex justify-between items-center py-3 px-4 border-b border-default-200">
                    <h3 class="text-lg font-medium text-default-900">
                        Close Import?
                    </h3>
                </div>
                <div class="p-4 overflow-y-auto">
                    <p class="mt-1 text-default-600">You will lose your import progress by closing this notification</p>
                </div>
                <div
                    class="flex justify-end items-center gap-x-2 py-3 px-4 border-t border-default-200">
                    <button type="button" onclick="closeImportProgress()"
                        class="btn rounded-full bg-danger/25 text-danger hover:bg-danger hover:text-white"
                        data-hs-overlay="#modal-three">
                        <i class="i-tabler-x me-1"></i>
                        Yes, Cancel
                    </button>
                    <button class="btn bg-primary text-white rounded-full" onclick="toggleContactsPreview()">
                        No, Don't Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
