<section class="animate__animated animate__fadeIn">
    <div id="content" class="h-screen flex overflow-hidden lg:h-[89vh] 2xl:h-[92vh] relative">
        
        <div class="w-full h-max p-20 flex flex-col items-center justify-center mt-20">
            <div class="loader m-auto"></div>
            <div class="text-xs">Loading conversations...</div>
        </div>
        
    </div>
</section>

<template id="template">
    <!-- converstion list -->
    <div class="h-full overflow-hidden w-full lg:w-[25%] 2xl:w-[20%]">
        <div class="h-full bg-white flex flex-col relative pt-[80px] pb-[75px]">
             <!--conversations-->
             <div class="max-h-full overflow-y-auto">
                <div name="conversation-list" class="flex flex-col divide-y divide-gray-100">
                    <div class="w-full h-max p-20 flex flex-col items-center justify-center mt-20">
                        <div class="loader m-auto"></div>
                    </div>
                </div>
             </div>
        
             <!-- header -->
             <div class="px-3 py-4 absolute top-0 flex gap-x-2 w-full bg-white border-b">
                 <div class="rounded-full h-8 w-8 bg-primary-g text-white font-black text-lg flex items-center justify-center">
                     <span name="campaigntype"></span>
                 </div>
                 <div class="flex-1 overflow-hidden -mt-1">
                    <div class="flex gap-1 pr-2">
                        <select name="campaigns" class="font-semibold text-lg capitalize truncate outline-none bg-transparent flex-1"></select>
                    </div>
                    <p class="-mt-1" name="campaignscounter" class="text-xs"></p>
                 </div>
             </div>
            
            <!-- paginations -->
            <div class="absolute bottom-0 w-full bg-white border-t-2 border-orange-500">
                <div class="px-5 py-3">
                    <form class="flex gap-2 items-center">
                        <div class="form-group">
                            <input type="date" class="border p-1 bg-transparent outline-none font-semibold text-xs" value="2024-08-22">
                        </div>
                        <div class="form-group">
                            <input type="date" class="border p-1 bg-transparent outline-none font-semibold text-xs" value="2024-08-22">
                        </div>
                    </form>   
                </div>
            </div> 
              
        </div>
    </div>  
    
    <!-- converstion view -->
    <div class="h-full overflow-hidden lg:w-[75%] 2xl:w-[80%] absolute lg:relative -z-10 lg:z-0 bg-gray-100 lg:bg-transparent w-full">
        <div class="pb-[200px] px-1 h-full overflow-y-auto">
            <div name="conversation" class="xl:w-4/5 2xl:w-3/5 mx-auto flex flex-col gap-y-2"></div>
        </div>
        <div class="w-full xl:w-4/5 2xl:w-3/5 fixed  lg:absolute z-10 bottom-0 left-1/2 -translate-x-1/2">
            <div onclick = "hideConversationThread()" class="bg-gray-600 lg:bg-gray-100 p-3">
                <span class="text-white flex items-center gap-2 lg:hidden">
                    <span class="material-symbols-outlined">arrow_back_ios</span>
                    <span>Conversations</span>
                </span>
            </div>
            <form id="messageform" class="bg-white p-5 drop-shadow-sm">
                <div class="form-group">
                    <textarea placeholder="Type Message here" class="form-control resize-none" rows="3"></textarea>
                </div>
                <div class="flex mt-5">
                    <button type="button" class="btn" id="submit">
                        <div class="btnloader" style="display: none;"></div>
                        <span>Reply</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>