<div class="mx-auto lg:w-3/5 2xl:w-2/5 card">
    <div class="card-body lg:p-10">
        <div class="flex flex-col gap-2 mb-10" name="header"></div>
         
        <form id="campaignform">
            <div class="flex flex-col gap-1">
                <label class="text-default-800 text-sm font-medium inline-block">Campaign Name</label>
                <input type="text" class="form-input" name="campaignname" id="campaignname">
            </div>
            <div class="flex items-center lg:justify-end gap-5 mt-10">
                <button type="button" onclick="window.history.back()" type="button" class="btn bg-light text-default-900 rounded-full">Back</button>
                <button id="submit" type="button" class="btn bg-dark text-white rounded-full">Create Campaign</button>
            </div>
        </form>
    </div>
</div>
