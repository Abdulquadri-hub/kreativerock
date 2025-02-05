<section class="animate__animated animate__fadeIn">
    
    <p class="page-title">
        <span>SMS Packages</span>
    </p>
    
    <ul class="flex flex-wrap text-sm font-medium text-center text-gray-500 border-b border-gray-200 dark:border-gray-700 dark:text-gray-400 mb-5">
        <li id="" class="me-2 cursor-pointer updater optioner !text-blue-600 active" name="smspackageform" onclick="runoptioner(this)">
            <p class="inline-block p-4 rounded-t-lg hover:text-gray-600 hover:bg-gray-50 ">Create Package</p>
        </li>
        <li id="iddviewsmspackages" class="me-2 cursor-pointer viewer  optioner" name="viewsmspackages" onclick="runoptioner(this)">
            <p class="inline-block p-4 rounded-t-lg hover:text-gray-600 hover:bg-gray-50">View Packages </p>
        </li>
    </ul>
    
    <form id="smspackageform">
        <div class="flex flex-col space-y-3 bg-white/90 p-5 xl:p-10 rounded-sm">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-end">
                <div class="form-group col-span-full">
                    <label class="control-label">package name</label>
                    <input type="text" class="form-control" name="packagename" id="packagename">
                </div>
                <div class="form-group">
                    <label class="control-label">number of units</label>
                    <input type="number"  name="numberofunits" id="numberofunits" class="form-control">
                </div>
                <div class="form-group">
                    <label class="control-label">Cost per unit</label>
                    <input type="number" inputmode="numeric"  name="costperunit" id="costperunit" class="form-control">
                </div>
            </div>
        </div>
        <div class="flex justify-end mt-5">
            <button type="button" class="btn" id="submit">
                <div class="btnloader" style="display: none;" ></div>
                <span>Submit</span>
            </button>
        </div>
    </form>
    
    <div class="hidden" id="viewsmspackages">
        <div class="table-content">
            <table>
                <thead>
                    <tr>
                        <th>s/n </th>
                        <th>Package</th>
                        <th>Number of Units</th>
                        <th>Cost/Unit</th>
                        <th>action</th>
                    </tr>
                </thead>
                <tbody id="tabledata"></tbody>
            </table>
        </div>
        <div class="table-status flex justify-between mt-5">
            <span class="text-xs text-gray-500" id="pagination-status">Showing 0 - 0 of 0</span>
            <span class=" flex justify-between gap-6">
                <span>
                    <select id="pagination-limit" class="form-control !bg-white cursor-pointer">
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
    
</section>