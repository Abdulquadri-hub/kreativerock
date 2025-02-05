<section class="animate__animated animate__fadeIn">
    
    <p class="page-title">
        <span>SMS Units</span>
    </p>
    
    <div id="smsunitstable">
        <div class="table-content">
            <table>
                <thead>
                    <tr>
                        <th>s/n </th>
                        <th>Qty Bought</th>
                        <th>Qty Used</th>
                        <th>Amount</th>
                        <th>Transaction Date</th>
                        <th>Method</th>
                        <th>Reference</th>
                        <!--<th>action</th>-->
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