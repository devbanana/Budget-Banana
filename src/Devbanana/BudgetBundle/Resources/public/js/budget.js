function budgetedListener()
{
    var row = $(this).parents('tr.category');

    // Save the category
    $.ajax({
url: Routing.generate('budgetcategories_save_ajax',
         {id: $(row).data('id'),
budgeted: parseFloat($(row).find('td.budgeted>input').val())}),
method: "POST",
success: function (result)
{
result = JSON.parse(result);
$(row).find('td.balance').html(parseFloat(result.balance).formatMoney());
    updateAvailableToBudget();
}
            });
}

function updateAvailableToBudget()
{
    $.ajax({
url: Routing.generate('budget_available_to_budget',
         {'id': $('tbody#budget').data('id')}),
method: "POST",
success: function (result)
{
result = JSON.parse(result);
$('#available-to-budget').html(parseFloat(result.availableToBudget).formatMoney());
}
            });
}

function updateNotBudgetedLastMonth()
{
    $.ajax({
url: Routing.generate('budget_not_budgeted_last_month_ajax',
         {'id': $('tbody#budget').data('id')}),
method: "POST",
success: function (result)
{
result = JSON.parse(result);
$('#not-budgeted-last-month').html(parseFloat(
        result.notBudgetedLastMonth).formatMoney());
$('#not-budgeted-last-month-month-label').html(result.month);
}
            });
}

function updateOverspentLastMonth()
{
    $.ajax({
url: Routing.generate('budget_overspent_last_month_ajax',
         {'id': $('tbody#budget').data('id')}),
method: "POST",
success: function (result)
{
result = JSON.parse(result);
$('#overspent-last-month').html(parseFloat(
        result.overspentLastMonth).formatMoney());
$('#overspent-last-month-month-label').html(result.month);
}
            });
}

function updateIncomeThisMonth()
{
    $.ajax({
url: Routing.generate('budget_income_this_month_ajax',
         {'id': $('tbody#budget').data('id')}),
method: "POST",
success: function (result)
{
result = JSON.parse(result);
$('#income-this-month').html(parseFloat(
        result.incomeThisMonth).formatMoney());
$('#income-this-month-month-label').html(result.month);
}
            });
}

function updateBudgetedThisMonth()
{
    $.ajax({
url: Routing.generate('budget_budgeted_this_month_ajax',
         {'id': $('tbody#budget').data('id')}),
method: "POST",
success: function (result)
{
result = JSON.parse(result);
$('#budgeted-this-month').html(parseFloat(
        result.budgetedThisMonth).formatMoney());
$('#budgeted-this-month-month-label').html(result.month);
}
            });
}

function updateAllCategories()
{
    $.ajax({
url: Routing.generate('budget_calculate_ajax',
         {id: $('tbody#budget').data('id')}),
method: "POST",
success: function (result)
{
result = JSON.parse(result);
$('tr.category').each(function()
    {
    var row = this;
$.each(result.categories, function (index, category)
    {
    if (category.id == $(row).data('id')) {
    $(row).find('td.outflow').html(
        parseFloat(category.outflow).formatMoney());
    $(row).find('td.balance').html(
        parseFloat(category.balance).formatMoney());
    }
    });
});
}
            });
}

function toggleCarryOver(row)
{
// Toggle carryover value
$(row).find('td.carryover>a.carryover-toggle').on('click', function(e)
        {
        e.preventDefault();

$.ajax({
url: Routing.generate('budgetcategories_toggle_carryover_ajax', {
id: $(row).data('id')
         }),
method: "POST",
success: function (result)
{
result = JSON.parse(result);
$(row).find('td.carryover>span.carryover-value').html(result.carryOver);
}
    });
        });
}

updateAvailableToBudget();
updateNotBudgetedLastMonth();
updateOverspentLastMonth();
updateIncomeThisMonth();
updateBudgetedThisMonth();
updateAllCategories();

$('tr.category').each(function ()
        {
        $(this).find('td.budgeted>input').on('input propertychange paste',
            budgetedListener);
        toggleCarryOver(this);
        });

