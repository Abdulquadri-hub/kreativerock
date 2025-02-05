<section class="animate__animated animate__fadeIn">
    
    <p class="page-title">
        <span>Bulk SMS</span>
    </p>
    
    <form id="smsform">
        <div class="flex flex-col space-y-3 bg-white/90 p-5 xl:p-10 rounded-sm">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
                <div class="form-group">
                    <label class="control-label">SMS package</label>
                    <select type="text" class="form-control" name="packageid" id="packageid">
                        <option value="" selected=""> -- Select Package -- </option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label">Cost per unit</label>
                    <input type="text" readonly="readonly"  id="costperunit" class="form-control">
                </div>
                <div class="form-group">
                    <label class="control-label">Quantity</label>
                    <input type="number" min="1" value="1"  name="qty" id="qty" class="form-control">
                </div>
                <div class="form-group">
                    <label class="control-label">Total</label>
                    <input type="text" readonly="readonly" id="total" class="form-control">
                </div>
            </div>
        </div>
        <div class="flex justify-end mt-5">
            <button type="button" class="btn" id="submit">
                <div class="btnloader" style="display: none;" ></div>
                <span>Proceed</span>
            </button>
        </div>
    </form>
    
</section>