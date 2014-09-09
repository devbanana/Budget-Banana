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

$('tr.lineitem').each(function()
        {
        refreshRow(this);
        subscribeEvents(this);

$(this).find('td.inflow>input').val('');
$(this).find('td.outflow>input').val('');
        });

function updateCategories()
{
    var month = $('#devbanana_budgetbundle_transaction_date_month').val();
    var year = $('#devbanana_budgetbundle_transaction_date_year').val();

    $.ajax({
url: Routing.generate('budgetcategories_list_ajax', { month: month, year: year }),
method: "POST",
success: function (result)
{
result = JSON.parse(result);

$('tr.lineitem').each(function()
    {
if ($(this).find('td.type>select').val() != 'income') {
populateCategories(this, result);
}
});

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
    var $lineitems = $('tbody.lineitems>tr.lineitem');
    var inflow = 0.00;
    var outflow = 0.00;

    $lineitems.each(function()
            {
        inflow += parseFloat(
                $(this).find('td.inflow>input').val() || 0);
        outflow += parseFloat(
                $(this).find('td.outflow>input').val() || 0);
    });

    $('#inflow').html(inflow.formatMoney(2));
    $('#outflow').html((outflow*-1).formatMoney(2));
    $('#balance').html((inflow-outflow).formatMoney());
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

    var d = new Date(year, month, 1);

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

    var $categorySelect = $(row).find('td.category>select');
    $categorySelect.attr('id',
            $categorySelect.attr('id').replace('category', 'assigned_month'));
    $categorySelect.attr('name',
            $categorySelect.attr('name').replace('category', 'assignedMonth'));
    $categorySelect.html('');
    $categorySelect.append(getEmptyOption());

    // Loop for 60 months
    for (var i = 0; i < 60; i++)
    {
    $categorySelect.append(
            getOption(d.getFullYear() + '-' + (d.getMonth()+1),
                'Income for ' + months[d.getMonth()] + ' ' + d.getFullYear()));

    d.setMonth(d.getMonth()+1);
    }
}

function accountListener()
{
    if ($(this).val() == 'add') {
        // Record which dropdown called this dialog
        var $caller = $(this);

        if (!$('#account_dialog>form').length) {
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

// Create account dialog
$('#account_dialog').dialog({
modal: true,
buttons: {
Add: function() {
var $this = $(this);
var $form = $this.find('form');
var data = $form.serialize();
$.post(
    Routing.generate("accounts_create_ajax"),
    data,
    function (result)
    {
    result = JSON.parse(result);
    refreshAllAccounts(result.id);
    $caller.val(result.id);
    $this.dialog("close");
    });
}
}
});
}
}

function payeeListener()
{
    if ($(this).val() == 'add') {
    var $caller = $(this);
    var payType = 'payee';
    if ($caller.parents('tr.lineitem').find('td.type>select').val()
            == 'income') {
        payType = 'payer';
    }

    if (!$('#payee_dialog').find('form').length) {
        $.ajax({
url: Routing.generate(
         payType == 'payee' ? 'payees_new_ajax' : 'payers_new_ajax'),
async: false,
method: "POST",
success: function (form)
{
$('#payee_dialog').append($(form));
}
                });
    }

// Create the dialog
$('#payee_dialog').dialog({
modal: true,
buttons: {
Add: function ()
{
var $this = $(this);
var $form = $this.find('form');
var data = $form.serialize();
$.post(
Routing.generate(
    payType == 'payee' ? 'payees_create_ajax' : 'payers_create_ajax'),
data,
function (result)
{
result = JSON.parse(result);
if (payType == 'payee') {
refreshAllPayees();
}
else {
refreshAllPayers();
}
$caller.val(result.id);
$this.dialog('close');
}
    );
}
}
        });
    }
}

function refreshAllPayees()
{
    $.ajax({
url: Routing.generate('payees_list_ajax'),
async: false,
method: "POST",
success: function (result)
{
result = JSON.parse(result);
$('tr.lineitem').each(function()
    {
    if ($(this).find('td.type>select').val() != 'income') {
populatePayees(this, result);
    }
    });
}
            });
}

function refreshAllPayers()
{
    $.ajax({
url: Routing.generate('payers_list_ajax'),
async: false,
method: "POST",
success: function (result)
{
result = JSON.parse(result);
$('tr.lineitem').each(function()
    {
    if ($(this).find('td.type>select').val() == 'income') {
populatePayers(this, result);
    }
    });
}
            });
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
    var $accounts = $(row).find('td.account').find('select');
    $accounts.empty();

    // Add empty element
    $accounts.append(getEmptyOption());

    $.each(result.accounts, function (index, account)
    {
        $accounts.append(
                getOption(account.id,
                    account.name + "&emsp;&emsp;" +
                    parseFloat(account.balance).formatMoney()));
    });

    $accounts.append(getAddOption('Add Account'));
}

function refreshPayees(row)
{
    var $type = $(row).find('td.type>select');
    var route;
    var populateFunction;

    if ($type.val() == 'expense') {
        route = 'payees_list_ajax';
        populateFunction = populatePayees;
    }
    else if ($type.val() == 'income') {
        route = 'payers_list_ajax';
        populateFunction = populatePayers;
    }
    else if ($type.val() == 'transfer') {
        route = 'accounts_list_ajax';
        populateFunction = populateTransferAccounts;
    }

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
    var $payees = $(row).find('td.payee>select');
    $payees.attr('id', $payees.attr('id').replace(
                /payer|transfer_account/, 'payee'));
    $payees.attr('name', $payees.attr('name').replace(
                /payer|transferAccount/, 'payee'));
    $payees.html('');

    $payees.append(getEmptyOption());

    $.each(result.payees, function(index, payee)
            {
            $payees.append(getOption(payee.id, payee.name));
            });

    $payees.append(getAddOption('Add Payee'));
}

function populatePayers(row, result)
{
    var $payers = $(row).find('td.payee>select');

    $payers.attr('id', $payers.attr('id').replace(
                /payee|transfer_account/, 'payer'));
    $payers.attr('name', $payers.attr('name').replace(
                /payee|transferAccount/, 'payer'));

    $payers.html('');

    $payers.append(getEmptyOption());

    $.each(result.payers, function(index, payer)
            {
            $payers.append(getOption(payer.id, payer.name));
            });

    $payers.append(getAddOption('Add Payer'));
}

function populateTransferAccounts(row, result)
{
    var $accounts = $('td.payee>select', $(row));

    $accounts.attr('id', $accounts.attr('id').replace(
                /payee|payer/, 'transfer_account'));
    $accounts.attr('name', $accounts.attr('name').replace(
                /payee|payer/, 'transferAccount'));

    $accounts.empty();

    $accounts.append(getEmptyOption());

    $.each(result.accounts, function (index, account)
            {
            $accounts.append(
                getOption(account.id,
                    account.name + "&emsp;&emsp;" + parseFloat(account.balance).formatMoney()));
            });
}

function refreshCategories(row)
{
    var month = $('#devbanana_budgetbundle_transaction_date_month').val();
    var year = $('#devbanana_budgetbundle_transaction_date_year').val();

    if ($(row).find('td.type>select').val() == 'income') {
        updateIncomeMonths(row);
    }
    else {
    $.ajax({
url: Routing.generate('budgetcategories_list_ajax',
         { month: month, year: year }),
method: "POST",
success: function (result)
{
result = JSON.parse(result);
populateCategories(row, result);
}
            });
}
}

function populateCategories(row, result)
{
    var $categories = $(row).find('td.category>select');

    $categories.attr('id',
            $categories.attr('id').replace('assigned_month', 'category'));

    $categories.attr('name',
            $categories.attr('name').replace('assignedMonth', 'category'));

    $categories.html('');

    $categories.append(getEmptyOption());

    $.each(result.categories, function (index, category)
            {
            $categories.append(getOption(category.id,
                    category.name + "&emsp;&emsp;" +
                    parseFloat(category.balance).formatMoney()));
            });

    $categories.append(getAddOption('Add Category'));
}

function refreshRow(row)
{
    refreshAccounts(row);
    refreshPayees(row);
    refreshCategories(row);
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

function subscribePayee(row)
{
    var $payee = $(row).find('td.payee>select');
$payee.on('change', payeeListener);
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
    subscribePayee(row);
    subscribeInflow(row);
    subscribeOutflow(row);
}

// Update categories on load
updateCategories();
