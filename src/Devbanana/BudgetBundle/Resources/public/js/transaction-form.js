var $collectionHolder;

// setup an "add a lineitem" link
var $addLineItemLink = $('<a href="#" class="add_lineitem_link">Add Another Lineitem</a>');
var $newLinkRow = $('<tr></tr>');
var $newLinkCell = $('<td colspan="6"></td>');
$newLinkCell.append($addLineItemLink);
$newLinkRow.append($newLinkCell);

// Get the tbody that holds the collection of lineitems
$collectionHolder = $('tbody.lineitems');

// add the "add a lineitem" anchor and li to the lineitems tbody
$collectionHolder.append($newLinkRow);

$collectionHolder.data('index', $collectionHolder.find('tr').length-1);

$addLineItemLink.on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // add a new lineitem form
        addLineItemForm($collectionHolder, $newLinkRow);
        });

$('#devbanana_budgetbundle_transaction_date_year').on('change',
        updateCategories);
$('#devbanana_budgetbundle_transaction_date_month').on('change',
        updateCategories);

for (var i = 0; i < $collectionHolder.data('index'); i++)
{
    $('#devbanana_budgetbundle_transaction_lineitems_' + i + '_type').on(
            'change', updateType);
    $('#devbanana_budgetbundle_transaction_lineitems_' + i + '_outflow').on(
            'input propertychange paste', updateBalance);
    $('#devbanana_budgetbundle_transaction_lineitems_' + i + '_inflow').on(
            'input propertychange paste', updateBalance);
}

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

function updateType()
{
    if ($(this).val() == 'income') {
        // Replace payees with payers
        $.ajax({
url: Routing.generate('payers_get_list_ajax'),
method: "POST",
context: this,
success: function (html)
{
$(this).parents('tr').find('td.payee').find('select').html($(html).html());
}
});

// Replace categories with months
updateIncomeMonths($(this).parents('tr'));
}
else if ($(this).val() != 'income') {
    // Replace payers with payees
    $.ajax({
url: Routing.generate('payees_get_list_ajax'),
method: "POST",
context: this,
success: function (html)
{
$(this).parents('tr').find('td.payee').find('select').html($(html).html());
}
});

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

    $('#devbanana_budgetbundle_transaction_lineitems_' + index + '_type').on(
            'change',
            updateType);
    $('#devbanana_budgetbundle_transaction_lineitems_' + index + '_inflow').on(
            'input propertychange paste',
            updateBalance);
    $('#devbanana_budgetbundle_transaction_lineitems_' + index + '_outflow').on(
            'input propertychange paste',
            updateBalance);

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

// Update categories on load
updateCategories();
