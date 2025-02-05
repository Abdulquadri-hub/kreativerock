<form id="segmentform">
    <div class="flex flex-col space-y-3 bg-white p-5 xl:p-10 rounded-sm">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <div class="form-group col-span-3">
                <label for="logoname" class="text-default-800 text-sm font-medium inline-block">Segment</label>
                <input type="text" name="name" id="segment" class="form-input" placeholder="Enter Segment">
            </div>
            <div class="flex justify-end mt-5">
                  <button id="submit" type="button" class="btn bg-primary text-white rounded-full rounded-sm">
                      <div class="btnloader" style="display: none;"></div>
                      <span>Search</span>
                  </button>
              </div>
        </div> 
    </div>
</form> 

<div class="card rounded-lg mt-10">
    <div class="card-body">
        <div>
            
            
            
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
                                        <th scope="col" class="px-6 py-3 text-start text-sm text-default-500">Segment</th>
                                        <th scope="col" class="px-6 py-3 text-start text-sm text-default-500 !text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="tabledata" class="divide-y divide-default-200">
                                    <!-- <tr>
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                            <div class="flex items-center h-5">
                                                <input id="table-checkbox-1" type="checkbox"
                                                    class="form-checkbox rounded">
                                                <label for="table-checkbox-1" class="sr-only">Checkbox</label>
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                            Leads
                                        </td>
                                        <td class="flex items-center gap-3 justify-center whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                            <button title="Edit row entry" class="material-symbols-outlined rounded-full bg-blue-600 h-8 w-8 text-white drop-shadow-md text-xs" style="font-size: 18px;">edit</button>
                                            <button title="Delete row entry" class="material-symbols-outlined rounded-full bg-red-600 h-8 w-8 text-white drop-shadow-md text-xs" style="font-size: 18px;">delete</button>
                                        </td>
                                    </tr> -->
                                </tbody>
                            </table>
                        </div>
                    </div>
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