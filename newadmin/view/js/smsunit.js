async function smsUnitActive() {
    datasource = [];
    await fetchSmsUnitsHistory()
}

async function fetchSmsUnitsHistory() {
    let request = await httpRequest2('../controllers/sms/smstransactionhistory', null, null, 'json')
    if(request.status) {
        datasource = request.data
        datasource.length && resolvePagination(datasource, onSmsUnitHistoryTableDataSignal, addFFooterTableDataSignal)

    } else return notification('No records retrieved', 0)
}

async function onSmsUnitHistoryTableDataSignal() {
    let rows = getSignaledDatasource().map((item, index) => {
        return `
            <tr>
                <td>${ item.index + 1 }</td>
                <td>${ item.qtyin }</td>
                <td>${ item.qtyout }</td>
                <td>${ formatCurrency(item.amount) }</td>
                <td> ${ formatDate(item.transactiondate) } </td>
                <td> ${ item.paymentmethod } </td>
                <td> ${ item.reference } </td>
            </tr>
        `
    }) .join('')
    injectPaginatatedTable(rows)
}


function addFFooterTableDataSignal() {
    let totalQtyIn = datasource.reduce((acc, item) => acc + (+item.qtyin), 0);
    let totalQtyOut = datasource.reduce((acc, item) => acc + (+item.qtyout), 0);
    let totalAmount = datasource.reduce((acc, item) => acc + (+item.amount), 0);

    let footerRow = `
        <tr colspan="1">
            <td colspan="1" class="!uppercase !text-sm font-bold">Total</td>
            <td class="!font-bold">${totalQtyIn}</td>
            <td class="!font-bold">${totalQtyOut}</td>
            <td class="!font-bold">${formatCurrency(totalAmount)}</td>
            <td colspan="3"></td>
        </tr>
    `;

    return footerRow;
}
