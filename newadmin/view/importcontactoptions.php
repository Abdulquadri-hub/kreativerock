<div>
    <div class="mx-auto lg:w-4/5 2xl:w-3/5">
        <div class="flex items-center md:justify-between flex-wrap gap-2 mb-5">
            <h4 class="text-default-900 text-2xl font-semibold capitalize">Import contacts for bulk creation or updating</h4>
            <p class="text-default-500 mb-10 mt-1">Create, update, or blocklist contacts in bulk. And manage unlimited contacts in one place. Keep in mind you must have your contacts' consent to send them campaigns. </p>
        </div>
        
        <div class="card p-10 rounded-lg">
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <button onclick="router.navigate('/contact/import/upload')" type="button" data-hs-overlay="#overlay-right" class="rounded-md border p-3 group cursor-pointer hover:border-amber-500 border-gray-200">
                        <span class="w-12 h-12 rounded-full flex items-center justify-center bg-blue-50 group-hover:bg-amber-50">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-folder-plus text-primary group-hover:text-amber-700"><path d="M12 10v6"/><path d="M9 13h6"/><path d="M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2Z"/></svg>
                        </span>
                        <div class="font-bold text-lg text-left mt-5">Import from file</div>
                        <div class="text-left">import your contacts from a csv or txt</div>
                    </button>
                    <button onclick="router.navigate('/contact/import/copy-paste')" type="button" data-hs-overlay="#overlay-right" class="rounded-md border p-3 group cursor-pointer hover:border-amber-500 border-gray-200">
                        <span class="w-12 h-12 rounded-full flex items-center justify-center bg-blue-50 group-hover:bg-amber-50">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-combine  text-primary group-hover:text-amber-700"><path d="M10 18H5a3 3 0 0 1-3-3v-1"/><path d="M14 2a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2"/><path d="M20 2a2 2 0 0 1 2 2v4a2 2 0 0 1-2 2"/><path d="m7 21 3-3-3-3"/><rect x="14" y="14" width="8" height="8" rx="2"/><rect x="2" y="2" width="8" height="8" rx="2"/></svg>
                        </span>
                        <div class="font-bold text-lg mt-5 text-left">Copy-Paste</div>
                        <div class="text-left">Paste the contacts as text from a spreadsheet or a similar list</div>
                    </button>
                </div>
            </div> 
        </div>
    </div>
</div>
