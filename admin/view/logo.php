                    <section class="animate__animated animate__fadeIn">
                            <p class="page-title">
                                <span>logo</span>
                            </p>
                            <form id="logosform">
                                <div class="flex flex-col space-y-3 bg-white/90 p-5 xl:p-10 rounded-sm">
                                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                        <div class="form-group">
                                            <label for="owner" class="control-label">Owner</label>
                                            <select class="form-control" name="owner" id="owner">
                                                <option value="">-- Select Owner --</option>
                                                <option value="TRAVELS AND TOURS">TRAVELS AND TOURS</option>
                                                <option value="EVENT">EVENT</option>
                                                <option value="FORMS">FORMS</option>
                                                <option value="TRIVIA">TRIVIA</option>
                                                <option value="BLOG">BLOG</option>
                                                <option value="VENDOR">VENDOR</option>
                                                <option value="VOTE">VOTE</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="status" class="control-label">status</label>
                                            <select class="form-control" name="status" id="status">
                                                <option value="">-- Select Status --</option>
                                                <option value="DISPLAY">DISPLAY</option>
                                                <option value="HIDE">HIDE</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="logoname" class="control-label">logo name</label>
                                            <input type="text" name="logoname" id="logoname" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label for="url" class="control-label">URL</label>
                                            <div id="imagePreview"></div>
                                            <input type="file" name="url" id="url" class="form-control" onchange="previewImage()">
                                        </div>  
                                        
                                    </div> 
                        
                                </div>
                                <div class="flex justify-end mt-5">
                                    <button type="button" id="submit" class="btn bg-gradient-to-br from-amber-400 via-amber-500 to-amber-600">
                                        <div class="btnloader" style="display: none;" ></div>
                                        <span>Submit</span>
                                    </button>
                                </div>
                            </form>
                            <hr class="my-10">
                            <div >
                                <div class="table-content">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>s/n </th>
                                                <th>owner</th>
                                                <th>logo name</th>
                                                <th>image url</th>
                                                <th>status</th>
                                                <th>action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tabledata">
                                            <tr>
                                                <td>1</td>
                                                <td>VOTE</td>
                                                <td>gos to Abk</td>
                                                <td>https://dummyimageurl.com</td>
                                                <td>Display</td>
                                                <td class="flex items-center gap-3">
                                                    <button title="Edit row entry" class="material-symbols-outlined rounded-full bg-primary-g h-8 w-8 text-white drop-shadow-md text-xs" style="font-size: 18px;">edit</button>
                                                    <button title="Delete row entry" class="material-symbols-outlined rounded-full bg-red-600 h-8 w-8 text-white drop-shadow-md text-xs" style="font-size: 18px;">delete</button>
                                                </td>
                                            </tr>
                                            
                                        </tbody>
                                    </table>
                                </div>
                                <div class="table-status flex justify-between mt-5">
                                    <span class="text-xs text-gray-500" id="pagination-status">Showing 1 - 20 of 40</span>
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