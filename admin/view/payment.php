
<section>
    <div class="lg:w-1/2 mx-auto">
        <div class="mt-10">
            <div class="flex justify-between items-center p-5 border-b bg-white ">
                <span class="font-bold text-base">Payment information</span>
            </div>
            <div class="flex flex-col gap-5" name="content">
                <div class="flex items-center justify-center w-full flex-col gap-1 h-full p-5">
                    <span class="loader"></span>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-5 font-bold py-10">
            <button type="button" onclick="window.history.back()" class="w-full lg:w-max outline-none border transition ease-linear duration-200 hover:opacity-80 gap-3 bg-white rounded-md p-2 px-10">Go back</button>
            <button id="pay" type="button" disabled class="disabled:cursor-not-allowed w-full lg:w-max outline-none border-none transition ease-linear duration-200 hover:opacity-80 gap-3 bg-primary-g rounded-md p-2 px-10 text-white">Pay</button>
        </div>
    </div>
</section>