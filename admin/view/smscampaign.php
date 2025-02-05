<section class="animate__animated animate__fadeIn">
    
    <p class="page-title">
        <span>Create Campaign</span>
    </p>
    
    <div class="py-3 pl-5 bg-green-50 border-l-green-600 border-l-4 mb-5">Campaign only supports Andriod Platform!</div>
    
    <form id="smscampaignform">
        
        <div class="flex flex-col gap-5 rounded-sm mb-5 p-5 bg-white divide-y">
            
            <div class="form-group">
                <h4 class="font-bold text-sm mb-5">Campaign Name</h4>
                <div class="form-group">
                    <label class="control-label"></label>
                    <input type="text" id="campaignname" name="campaignname" class="form-control bg-white" placeholder="Give this campaign a name">
                </div>
            </div>
            
            <div class="form-group py-3">
                <h4 class="font-bold text-sm mb-5">Campaign Type</h4>
                <label class="control-label mb-2 lowercase first-letter:uppercase">Select the type of campain you wish run</label>
                <div class="grid grid-cols-2 lg:grid-cols-6 gap-2">
                    <div class="campaign-option rounded-md bg-white text-center font-medium p-5 border hover:border-green-600 transition duration-300 cursor-pointer" data-campaign="promotional">Promotional</div>
                    <div class="campaign-option rounded-md bg-white text-center font-medium p-5 border hover:border-green-600 transition duration-300 cursor-pointer" data-campaign="transactional">Transactional</div>
                    <div class="campaign-option rounded-md bg-white text-center font-medium p-5 border hover:border-green-600 transition duration-300 cursor-pointer" data-campaign="keyword">Keyword</div>
                </div>
            </div>
            
            <div id="responseHandling" class="form-group py-3">
                <h4 class="font-bold text-sm mb-5">Promotional Campaign Response</h4>
                <label class="control-label mb-2 lowercase first-letter:uppercase">How will you Handle Responses/Feedback from end users</label>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-2">
                    <div class="promotion-option rounded-md bg-white text-center font-medium p-5 border hover:border-green-600 transition duration-300 cursor-pointer" data-promotion="manual">Manually</div>
                    <div class="promotion-option rounded-md bg-white text-center font-medium p-5 border hover:border-green-600 transition duration-300 cursor-pointer" data-promotion="automated">Automated</div>
                </div>
            </div>
            
            <div id="responsePrompts" class="form-group py-3">
                <h4 class="font-bold text-sm mb-5">Create Prompts</h4>
                <label class="control-label mb-2 lowercase first-letter:uppercase">Enter prompts and expected responses</label>
                <div class="flex flex-col gap-1" id="prompts"></div>
                
            </div>
        </div>
        
        <div class="flex flex-col space-y-5 rounded-sm mb-5 p-5 bg-white">
            <div class="form-group col-span-full">
                <div class="flex justify-between items-center">
                    <h4 class="font-bold text-sm mb-5">Campaign Contacts</h4>
                    <div id="phoneFeedback" class="hidden"></div>
                </div>
                <textarea class="form-control" id="phoneNumbers" placeholder="Enter one or more phone numbers, separated by commas"type="number"rows="5"></textarea>
                <div class="flex justify-end mt-3">
                    <button id="importButton" type="button" title="Import your CSV contact" class="flex gap-1 justify-center items-center bg-gray-900 text-white px-2 py-2 rounded-md border">
                        <span style="font-size: 16px" class="material-symbols-outlined">file_open</span>
                        <span>Import CSV</span>
                    </button>
                    <input type="file" id="file" accept=".csv" class="hidden">
                </div>
            </div>
        </div>
        
        <div class="flex flex-col space-y-5 rounded-sm mb-5 p-5 bg-white">
            <div class="form-group col-span-full">
                <div class="flex justify-between items-center">
                    <h4 class="font-bold text-sm mb-5">Campaign Message</h4>
                    <p id="smsCounter" class="text-gray-600 text-xs mt-2  font-semibold">Characters: 0 | SMS Pages: 0</p>
                </div>
                <div class="form-group col-span-full">
                    <label class="control-label">What do you want to send?</label>
                    <textarea class="form-control" name="campaignmessage" id="smsMessage" placeholder="Enter your message"rows="5"></textarea>
                    <small class="text-gray-500 font-medium text-xs">1 SMS = 160 characters</small>
                </div>
                <div class="form-group">
                    <p id="offensiveWordsCheck" class="text-red-600 hidden">Warning: Offensive or illegal words detected!</p>
                </div>
            </div>
        </div>
        
        <div class="text-sm flex flex-col bg-white p-5">
            <h4 class="font-bold mb-5">Campaign Action</h4>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
                <div class="form-group">
                    <label class="control-label">Schedule Date</label>
                    <input type="datetime-local" id="scheduleDate" name="scheduledate" class="form-control">
                    <small class="text-gray-500">Leave blank if you want to send immediately</small>
                </div>
                
                <div class="form-group">
                    <label class="control-label">Repeat Campaign</label>
                    <select id="repeatInterval" name="repeatinterval" class="form-control">
                        <option value="NO REPEAT">No Repeat</option>
                        <option value="DAILY">Daily</option>
                        <option value="WEEKLY">Weekly</option>
                        <option value="MONTHLY">Monthly</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="flex justify-end mt-10 border-t pt-5 gap-2">
            <button title="Save this campaign to draft and launch later" type="button" class="btn !bg-none bg-primary-g" id="draft" data-action="draft">
                <div class="btnloader" style="display: none;"></div>
                <span>Save Draft</span>
            </button>
            <button title="Launch campaign now" type="button" class="btn" id="submit" data-action="launch">
                <div class="btnloader" style="display: none;"></div>
                <span>Launch Campaign</span>
            </button>
        </div>
    </form>

</section>
