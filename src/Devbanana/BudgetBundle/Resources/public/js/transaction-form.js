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
$('#devbanana_budgetbundle_transaction_date_year').attr(
        'aria-labelledby', 'date-label');
$('#devbanana_budgetbundle_transaction_date_month').attr(
        'aria-labelledby', 'date-label');
$('#devbanana_budgetbundle_transaction_date_day').attr(
        'aria-labelledby', 'date-label');

$('tr.lineitem').each(function()
        {
        subscribeEvents(this);
        createLabelAssociations(this);

        // Append add option to dropdowns
        $('td.account>select', $(this)).append(getAddOption('Add Account'));

        if ($(this).find('td.type>select').val() == 'expense') {
        $('td.payee>select').append(getAddOption('Add Payee'));
        }
        else if ($(this).find('td.type>select').val() == 'income') {
        $('td.payee>select').append(getAddOption('Add Payer'));
        }

        if ($(this).find('td.type>select').val() != 'income') {
        $(this).find('td.category>select').append(getAddOption('Add Category'));
        }

if ($('td.inflow>input').val() == '0.00') {
$(this).find('td.inflow>input').val('');
}
if ($('td.outflow>input').val() == '0.00') {
$(this).find('td.outflow>input').val('');
}
        });

updateBalance();

function createLabelAssociations(row)
{
        $(row).find('td.type>select').attr(
            'aria-labelledby', 'type-label');
        $(row).find('td.account>select').attr(
            'aria-labelledby', 'account-label');
        $(row).find('td.payee>select').attr(
            'aria-labelledby', 'payee-label');
        $(row).find('td.category>select').attr(
            'aria-labelledby', 'category-label');
        $(row).find('td.check-number>input').attr(
            'aria-labelledby', 'check-number-label');
        $(row).find('td.memo>input').attr('aria-labelledby', 'memo-label');
$(row).find('td.inflow>input').attr('aria-labelledby', 'inflow-label');
$(row).find('td.outflow>input').attr('aria-labelledby', 'outflow-label');
}

function updateCategories()
{
    var month = $('#devbanana_budgetbundle_transaction_date_month').val();
    var year = $('#devbanana_budgetbundle_transaction_date_year').val();

    $.ajax({
url: Routing.generate('budgetcategories_list_ajax', { month: month, year: year }),
method: "POST",
success: function (result)
{
$('tr.lineitem').each(function()
    {
if ($(this).find('td.type>select').val() == 'income') {
refreshAssignedMonths(this);
}
else {
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
        refreshAssignedMonths($(this).parents('tr'));
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
    createLabelAssociations($newFormRow);
}

// Update categories dropdown with list of months that income can be applied to
//
// This is only populated when the income type is selected
function refreshAssignedMonths(row)
{
    var year = $('#devbanana_budgetbundle_transaction_date_year').val();
    var month = $('#devbanana_budgetbundle_transaction_date_month').val();

    $.ajax({
url: Routing.generate('budgetcategories_get_assigned_months_ajax',
         {month: month, year: year}),
method: "POST",
success: function (result)
{
populateAssignedMonths(row, result);
}
            });
}

function populateAssignedMonths(row, result)
{
    var $category = $(row).find('td.category>select');
    $category.attr('id',
            $category.attr('id').replace('category', 'assigned_month'));
    $category.attr('name',
            $category.attr('name').replace('category', 'assignedMonth'));

    $category.empty();
    $category.append(getEmptyOption());

    $.each(result.assignedMonths, function (index, assignedMonth)
            {
            $category.append(
                getOption(assignedMonth.id, assignedMonth.month));
            });
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
    if (result.success == true) {
    refreshAllAccounts(result.id);
    $caller.val(result.id);
    $this.dialog("close");
    $this.empty();
    }
    else {
    $this.find('.errors').remove();
var $errorDiv = $('<div class="errors"></div>');
$errorDiv.append($('<p tabindex="0">There were some errors.</p>'));

var $errorList = $('<ul></ul>');
$.each(result.errors, function (index, error)
    {
    $errorList.append($('<li tabindex="0">' + error + '</li>'));
    });

$errorDiv.append($errorList);

$errorDiv.attr('role', 'dialog');
$errorDiv.attr('title', 'Errors');

$this.find('form').before($errorDiv);
$errorDiv.dialog({
buttons: {
OK: function ()
{
$errorDiv.dialog('close');
$('#devbanana_budgetbundle_account_name').focus();
},
close: function ()
{
$('#devbanana_budgetbundle_account_name').focus();
}
}
        });

    }
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
if (payType == 'payee') {
refreshAllPayees();
}
else {
refreshAllPayers();
}
$caller.val(result.id);
$this.dialog('close');
$this.empty();
}
    );
}
}
        });
    }
}

function categoryListener()
{
    if ($(this).val() == 'add') {
        var $caller = $(this);

                            // Fetch new category form
                            $.ajax({
url: Routing.generate('categories_new_ajax'),
async: false,
method: "POST",
success: function (result)
{
$('#new-category').empty();
$('#new-category').html($(result));
}
                                });

                            // Open the dialog
                            $('#new-category').dialog({
modal: true,
buttons: {
Add: function ()
{
var $this = $(this);
var data = $('>form', $('#new-category')).serialize();
$.post(
    Routing.generate('categories_create_ajax'),
    data,
    function (result)
    {
    refreshAllCategories();

    // We have to get the BudgetCategories id to set the dropdown
    var month = $('#devbanana_budgetbundle_transaction_date_month').val();
    var year = $('#devbanana_budgetbundle_transaction_date_year').val();

    $.ajax({
url: Routing.generate('budgetcategories_get_by_category_ajax',
         {month: month,
year: year,
id: result.id}),
method: "POST",
success: function (result)
{
$caller.val(result.id);
        $this.dialog('close');
        $this.empty();
}
        });
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
// Loop through existing accounts
$('tr.lineitem').each(function()
    {
    populateAccounts(this, result);
    });
}
});
}

function refreshAllCategories()
{
    var month = $('#devbanana_budgetbundle_transaction_date_month').val();
    var year = $('#devbanana_budgetbundle_transaction_date_year').val();

    $.ajax({
url: Routing.generate('budgetcategories_list_ajax',
         { month: month, year: year }),
method: "POST",
success: function (result)
{
$('tr.lineitem').each(function()
    {
populateCategories(this, result);
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

    $accounts.trigger('refresh');
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
        refreshAssignedMonths(row);
    }
    else {
    $.ajax({
url: Routing.generate('budgetcategories_list_ajax',
         { month: month, year: year }),
method: "POST",
success: function (result)
{
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

function subscribeCategory(row)
{
    var $category = $(row).find('td.category>select');
$category.on('change', categoryListener);
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
    subscribeCategory(row);
    subscribeInflow(row);
    subscribeOutflow(row);
}

$('#devbanana_budgetbundle_transaction_date_year').focus();

$('#submit').on('click', function(e)
        {
        e.preventDefault();
        var data = $('#transaction-form>form').serialize();
        var url = Routing.generate('transactions_create_ajax');
        if ($('tbody.lineitems').data('id')) {
        var url = Routing.generate('transactions_update_ajax',
            {id: $('tbody.lineitems').data('id')});
        }
        $.post(
            url,
            data,
            function (result)
            {
            if (result.success == true) {
            if ($('tbody.lineitems').data('id')) {
            location.href = result.redirect;
            }
            else {
            // Set new CSRF token
            $('#devbanana_budgetbundle__token').val(result.csrf);
            $('tr.lineitem').each(function(index)
                {
                if (index == 0) {
                var account = $(this).find('td.account>select').val();
                $(this).find('td.account>select').on('refresh', function ()
                    {
                    $(this).val(account);
                    });
                refreshRow(this);
                $(this).find('td.check-number>input').val('');
                $(this).find('td.memo>input').val('');
                $(this).find('td.inflow>input').val('');
                $(this).find('td.outflow>input').val('');
                }
                else {
                $(this).remove();
                $collectionHolder.data('index', 1);
                }
                });
            $('#devbanana_budgetbundle_transaction_date_year').focus();

            var timeout;

            // Set alert message
$('#alert').append($('<p role="alert" tabindex="0">' +
            'A transaction has been created for ' +
(parseFloat(result.inflow)-parseFloat(result.outflow)).formatMoney() + '.</p>'));
$('#alert').attr('title', 'Transaction Created');
$('#alert').dialog({
open: function ()
{
timeout = setTimeout(dismissAlert, 5000);
},
close: function ()
{
clearTimeout(timeout);
dismissAlert();
},
buttons: {
OK: function ()
{
clearTimeout(timeout);
dismissAlert();
}
}
        });

}
            }
            else {
                var $errors = $('<ul></ul>');
                $.each(result.errors, function (index, error)
                        {
                        var $errorItem = $('<li>' + error + '</li>');
                        $errors.append($errorItem);
                        });

                $('#errors').empty();
                $('#errors').append($('<p>There were some errors. ' +
                            'Please check your input and try again.</p>'));
                $('#errors').append($errors);
                $('#errors').show();
                $('#errors').attr('tabindex', 0);
$('#errors').focus();
            }
            });
        });

function dismissAlert()
        {
            $('#alert').empty();
$('#alert').dialog('close');
            $('#devbanana_budgetbundle_transaction_date_year').focus();
        }
