<div>
    <div class="mx-auto lg:w-4/5 2xl:w-3/5">
        
        <div class="mb-10 flex gap-5 lg:flex-row lg:justify-between flex-col">
            <div>
                <h4 class="text-default-900 text-2xl font-semibold capitalize">Purchase SMS Units</h4>
                <p class="text-default-500 mt-1">This is your units center. SMS to be spent for your rcs messaging must be purchased first</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" data-hs-overlay="#overlay-right"  class="btn bg-dark text-white rounded-full [--overlay-backdrop:static]">Purchase Units</button>
            </div>
        </div>
        

        <div class="card-body">
            <div class="card group overflow-hidden">
                <div class="card-body">
                    <div>
                        <div class="rounded flex justify-center items-center size-12 bg-primary/10 text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-wallet"><path d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1"/><path d="M3 5v14a2 2 0 0 0 2 2h15a1 1 0 0 0 1-1v-4"/></svg>
                        </div>
                        
                        <div class="flex flex-col divide-y">
                            <div class="py-5">
                                <p class="text-xs tracking-wide font-semibold uppercase text-default-700 mb-1">Unit balance</p>
                                <h4 class="font-semibold text-2xl text-default-700" name="smsunits">50</h4>
                            </div>
                            
                            <div class="py-5">
                                <p class="text-xs tracking-wide font-semibold uppercase text-default-700 mb-1">Unit spent</p>
                                <h4 class="font-semibold text-2xl text-default-700" name="smsunits">3000</h4>
                            </div>
                        </div>
                        
                    </div>
                    
                </div>
            </div>
        </div>
        
   </div>
</div>


<div id="overlay-right" class="[--overlay-backdrop:static] hs-overlay overflow-hidden hs-overlay-open:translate-x-0 translate-x-full fixed top-0 right-0 transition-all duration-300 transform h-full max-w-md w-full z-70 bg-white border-s border-default-200 hidden"
    tabindex="-1">
    <div class="relative overflow-y-auto h-full"> 
        <div class="fixed top-0 bg-white w-full flex justify-between items-center py-3 px-4 border-b border-default-200">
            <h3 class="text-lg font-black text-default-800">
                Purchase Units
            </h3>
            <button type="button" class="hover:text-default-900"
                data-hs-overlay="#overlay-right">
                <span class="sr-only">Close modal</span> 
                <i class="i-tabler-x text-lg"></i>
            </button>
        </div>
        
        <form id="smsform" class="px-5 py-16">
            <div class="flex flex-col gap-3">
                <div class="flex flex-col gap-1">
                    <label class="text-default-800 text-sm font-medium inline-block">SMS package</label>
                    <select type="text" class="form-input" name="packageid" id="packageid">
                        <option value="" selected=""> -- Select Package -- </option>
                    </select>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-default-800 text-sm font-medium inline-block">Cost per unit</label>
                    <input type="text" readonly="readonly"  id="costperunit" class="form-input">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-default-800 text-sm font-medium inline-block">Quantity</label>
                    <input type="number" min="1" value="1"  name="qty" id="qty"  class="form-input">
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-default-800 text-sm font-medium inline-block">Total</label>
                    <input type="text" readonly="readonly" id="total"  class="form-input">
                </div>
            </div>
        </form>
        
        <div class="flex flex-wrap items-center justify-between gap-3 fixed bottom-0 bg-white border-t p-3 w-full">
            <a data-hs-overlay="#overlay-right" class="btn rounded-full bg-primary/25 text-primary hover:bg-primary hover:text-white">Cancel</a>
            <button type="button" id="submit" class="btn rounded-full bg-dark text-white">Proceed</button>
        </div>
    </div>
</div>

<div id="modal">
    <button type="button" data-hs-overlay="#modal-three"></button>
    <div id="modal-three"
        class="hs-overlay w-full h-full fixed top-0 left-0 z-70 transition-all duration-500 overflow-y-auto hidden pointer-events-none">
        <div
            class="-translate-y-5 hs-overlay-open:translate-y-0 hs-overlay-open:opacity-100 opacity-0 ease-in-out transition-all duration-500 sm:max-w-lg sm:w-full my-8 sm:mx-auto flex flex-col bg-white shadow-sm rounded">
            <div class="flex flex-col border border-default-200 shadow-sm rounded-lg  pointer-events-auto">
                <div
                    class="flex justify-between items-center py-3 px-4 border-b border-default-200">
                    <h3 class="text-lg font-bold text-default-900">
                        Purchase Summary
                    </h3>
                    <button type="button" class="text-default-600 cursor-pointer"
                        data-hs-overlay="#modal-three">
                        <i class="i-tabler-x text-lg"></i>
                    </button>
                </div>
                <div class="p-4 overflow-y-auto body"> </div>
            </div>
        </div>
    </div>
</div>