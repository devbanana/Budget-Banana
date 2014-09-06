var $collectionHolder;

// setup an "add a lineitem" link
var $addLineItemLink = $('<a href="#" class="add_lineitem_link">' +
        'Add Another Lineitem</a>');
var $newLinkRow = $('<tr></tr>');
var $newLinkCell = $('<td colspan="6"></td>');
$newLinkCell.append($addLineItemLink);
$newLinkRow.append($newLinkCell);

// Get the tbody that holds the collection of lineitems
$collectionHolder = $('tbody.lineitems');
$collectionHolder.data('prototype',
        $collectionHolder.find('tr.lineitem:first').html());

// add the "add a lineitem" anchor and row to the lineitems tbody
$collectionHolder.append($newLinkRow);

$collectionHolder.data('index', $collectionHolder.find('tr').length-1);

$addLineItemLink.on('click', function(e)
        {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // add a new lineitem form
        addLineItemForm($collectionHolder, $newLinkRow);
        });

$('#devbanana_budgetbundle_transaction_date_year').on('change',
        updateCategories);
$('#devbanana_budgetbundle_transaction_date_month').on('change',
        updateCategories);

// Create account dialog
$('#account_dialog').dialog({
modal: true,
autoOpen: false,
buttons: {
Add: function() {
var form = $('#account_dialog').find('form');
var data = $(form).serialize();
$.post(
    Routing.generate("accounts_create_ajax"),
    data,
    function (result)
    {
    result = JSON.parse(result);
    refreshAllAccounts(result.id);
    $($('#account_dialog').data('caller')).val(result.id);
    $('#account_dialog').dialog("close");
    });
}
}
});

$('tr.lineitem').each(function()
        {
        refreshRow(this);
        subscribeEvents(this);
        });

Number.prototype.formatMoney = function(c, d, t){
    var n = this, 
        c = isNaN(c = Math.abs(c)) ? 2 : c, 
        d = d == undefined ? "." : d, 
        t = t == undefined ? "," : t, 
        s = n < 0 ? "-" : "", 
        i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
        j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

String.prototype.endsWith = function(suffix) {
    return this.indexOf(suffix, this.length - suffix.length) !== -1;
};

function updateCategories()
{
    var month = $('#devbanana_budgetbundle_transaction_date_month').val();
    var year = $('#devbanana_budgetbundle_transaction_date_year').val();

    $.ajax({
url: Routing.generate('budgetcategories_list_ajax', { month: month, year: year }),
method: "POST",
success: function (html)
{
var lineitems = $('tbody.lineitems').find('tr.lineitem');

for (var i = 0; i < lineitems.length; i++)
{
if ($(lineitems[i]).find('td.type').find('select').val() != 'income') {
$(lineitems[i]).find('td.category').find('select').html($(html).html());
}
}

}
});
}

function typeListener()
{
    refreshPayees($(this).parents('tr.lineitem'));

    if ($(this).val() == 'income') {
        // Replace categories with months
        updateIncomeMonths($(this).parents('tr'));
    }
    else if ($(this).val() != 'income') {
        // Populate with categories
        updateCategories();
    }
}

function updateBalance()
{
    var lineitems = $('tbody.lineitems').find('tr.lineitem');
    var inflow = 0.00;
    var outflow = 0.00;

    for (var i = 0; i < lineitems.length; i++)
    {
        inflow += parseFloat(
                $('#devbanana_budgetbundle_transaction_lineitems_' + i + '_inflow').val() || 0);
        outflow += parseFloat(
                $('#devbanana_budgetbundle_transaction_lineitems_' + i + '_outflow').val() || 0);
    }

    $('#inflow').html('$' + inflow.formatMoney(2));
    $('#outflow').html('-$' + outflow.formatMoney(2));

    var balance = inflow - outflow;
    if (balance < 0) {
        balance = '-$' + Math.abs(balance).formatMoney();
    }
    else {
        balance = '$' + balance.formatMoney();
    }

    $('#balance').html(balance);

}

function addLineItemForm($collectionHolder, $newLinkRow)
{
    // Get the data-prototype
    var prototype = $collectionHolder.data('prototype');

    // get the new index
    var index = $collectionHolder.data('index');

    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    var newForm = prototype.replace(/__name__/g, index);

    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);

    // Display the form in the page in a row,
    // before the "Add a lineitem" link row
    var $newFormRow = $('<tr class="lineitem"></tr>').append(newForm);
    $newLinkRow.before($newFormRow);

    refreshRow($newFormRow);
    subscribeEvents($newFormRow);
}

// Update categories dropdown with list of months that income can be applied to
//
// This is only populated when the income type is selected
function updateIncomeMonths(row)
{
    var year = $('#devbanana_budgetbundle_transaction_date_year').val();
    var month = $('#devbanana_budgetbundle_transaction_date_month').val() - 1;

    var d1 = new Date(year, month, 1, 0, 0, 0, 0);
    var d2 = new Date(year, month+1, 1, 0, 0, 0, 0);

    var months = new Array();
    months[0] = 'January';
    months[1] = 'February';
    months[2] = 'March';
    months[3] = 'April';
    months[4] = 'May';
    months[5] = 'June';
    months[6] = 'July';
    months[7] = 'August';
    months[8] = 'September';
    months[9] = 'October';
    months[10] = 'November';
    months[11] = 'December';

    var categorySelect = $(row).find('td.category').find('select');
    categorySelect.html('');
    categorySelect.append(
            $('<option value="" selected="selected"></option>'));
    categorySelect.append(
            $('<option value="' + d1.getYear() + '-' + d1.getMonth()+1 +
                '">Income for ' + months[d1.getMonth()] +
                '</option>'));
    categorySelect.append(
            $('<option value="' + d2.getYear() + '-' + d2.getMonth()+1 +
                '">Income for ' + months[d2.getMonth()] +
                '</option>'));

}

function accountListener()
{
    if ($(this).val() == 'add') {
        // Record which dropdown called this dialog
        $('#account_dialog').data('caller', this);

        if (!$('#account_dialog').find('form').length) {
            // Fetch the form from the server
            $.ajax({
url: Routing.generate('accounts_new_ajax'),
async: false,
method: "POST",
success: function (html)
{
$('#account_dialog').append($(html));
}
});
}

$('#account_dialog').dialog('open');
}
}

function refreshAllAccounts(id)
{
    $.ajax({
url: Routing.generate('accounts_list_ajax'),
async: false,
method: "POST",
success: function (result)
{
result = JSON.parse(result);
// Loop through existing accounts
$('tr.lineitem').each(function()
    {
    populateAccounts(this, result);
    });
}
});
}

function refreshAccounts(row)
{
    $.ajax({
url: Routing.generate('accounts_list_ajax'),
method: "POST",
success: function (result)
{
result = JSON.parse(result);
populateAccounts(row, result);
}
});
}

function populateAccounts(row, result)
{
    // Find accounts dropdown
    var account = $(row).find('td.account').find('select');
    $(account).html('');

    // Add empty element
    $(account).append(
            getEmptyOption()
            );

    for (var i = 0; i < result.accounts.length; i++)
    {
        $(account).append(
                getOption(result.accounts[i].id, result.accounts[i].name)
                );
    }

    $(account).append(
            getAddOption('Add Account')
            );
}

function refreshPayees(row)
{
    var type = $(row).find('td.type>select');
    var route;
    var populateFunction;

    if ($(type).val() == 'expense') {
        route = 'payees_list_ajax';
        populateFunction = populatePayees;
    }
    else if ($(type).val() == 'income') {
        route = 'payers_list_ajax';
        populateFunction = populatePayers;
    }
    // TODO: Transfer type

    $.ajax({
url: Routing.generate(route),
method: "POST",
success: function (result)
{
result = JSON.parse(result);
populateFunction(row, result);
}
});
}

function populatePayees(row, result)
{
    var payees = $(row).find('td.payee>select');
    $(payees).html('');

    $(payees).append(getEmptyOption());

    $.each(result.payees, function(index, payee)
            {
            $(payees).append(getOption(payee.id, payee.name));
            });

    $(payees).append(getAddOption('Add Payee'));
}

function populatePayers(row, result)
{
    var payers = $(row).find('td.payee>select');
    $(payers).html('');

    $(payers).append(getEmptyOption());

    $.each(result.payers, function(index, payer)
            {
            $(payers).append(getOption(payer.id, payer.name));
            });

    $(payers).append(getAddOption('Add Payer'));
}

function refreshRow(row)
{
    refreshAccounts(row);
    refreshPayees(row);
}

function getEmptyOption()
{
    return $('<option value="" selected="selected"></option>');
}

function getAddOption(text)
{
    return $('<option value="add">' + text + '</option>');
}

function getOption(value, text)
{
    return $('<option value="' + value + '">' + text + '</option>');
}

function subscribeType(row)
{
    var type = $(row).find('td.type>select');
    $(type).on('change', typeListener);
}

function subscribeAccount(row)
{
    var account = $(row).find('td.account>select');
    $(account).on('change', accountListener);
}

function subscribeInflow(row)
{
    var inflow = $(row).find('td.inflow>input');
    $(inflow).on(
            'input propertychange paste',
            updateBalance);
}

function subscribeOutflow(row)
{
    var outflow = $(row).find('td.outflow>input');
    $(outflow).on(
            'input propertychange paste',
            updateBalance);
}

function subscribeEvents(row)
{
    subscribeType(row);
    subscribeAccount(row);
    subscribeInflow(row);
    subscribeOutflow(row);
}

// Update categories on load
updateCategories();
