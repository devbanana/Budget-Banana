if ($('.treeview').length) {
    $('.treeview').each(function() {
            //set reference to the top treeview list
            var $tree = $('.treeview ul:first');
            //add role and class to the list
            $tree.attr({'role': 'tree', 'tabindex': '0', 'id': 'tree' + $('.treeview').index(this)});
            //some regularly updated vars depending on nodes expanded/collapsed
            var $allNodes = $('li:visible', $tree);//object with all visible list item nodes
            var lastNodeIdx = $allNodes.length - 1;//last list item node's index
            var $lastNode = $allNodes.eq(lastNodeIdx);//last list item node visible
            function updateSelectedNode(nodeId) {
            $('.activedescendant', $tree).removeClass('activedescendant').attr('aria-selected', 'false');
            $('#'+nodeId).addClass('activedescendant').attr('aria-selected', 'true');
            $tree.attr('aria-activedescendant', nodeId);

            }
            //for updating toggle and node link
            function toggleGroup($node) {
            $toggle = $('> div', $node);
            $childList = $('> ul', $node);
            $childList.slideToggle('fast', function() {
                //update relevant vars when a node is expanded/collapsed
                $allNodes = $('li:visible', $tree);
                lastNodeIdx = $allNodes.length - 1;
                $lastNode = $allNodes.eq(lastNodeIdx);
                }
                );
            if ($toggle.hasClass('collapsed')) {
                $toggle.removeClass('collapsed').addClass('expanded');
                $node.attr('aria-expanded', 'true');
            } else {
                $toggle.removeClass('expanded').addClass('collapsed');
                $node.attr('aria-expanded', 'false');
            }
            updateSelectedNode($node.attr('id'));
            }
            //get next node's link
            function nextNode($node, dir) {
                var thisNodeIdx = $allNodes.index($node);
                if (dir == 'up' || dir == 'parent') {
                    var endNodeIdx = 0;
                    var operand = -1;
                } else {
                    var endNodeIdx = lastNodeIdx;
                    var operand = 1;
                }
                if (thisNodeIdx == endNodeIdx) {//if currentNode is last node
                    return false; //don't do anything
                }
                if (dir == 'parent') {
                    var parentNodeIdx = $allNodes.index($node.parent().parent());
                    var $nextNode = $allNodes.eq(parentNodeIdx);
                } else {
                    var $nextNode = $allNodes.eq(thisNodeIdx + operand);
                }
                updateSelectedNode($nextNode.attr('id'));
            }

            //for each li in the tree
            $('li', $tree).each(function() {
                    createNode(this);
            }
            );
            $tree.attr('aria-activedescendant', $('> li:first', $tree).attr('id'));
            $('#'+$tree.attr('aria-activedescendant')).addClass('activedescendant').attr('aria-selected', 'true');
            $tree.on('keydown', function(e){
                    var $currentNode = $('#'+$tree.attr('aria-activedescendant'));
                    if (!(e.shiftKey || e.ctrlKey || e.altKey || e.metaKey)) {
                    switch(e.which)
                    {
                    case 38: //up
                    e.preventDefault();
                    e.stopPropagation();
                    nextNode($currentNode, 'up');
                    break;
                    case 40: //down
                    e.preventDefault();
                    e.stopPropagation();
                    nextNode($currentNode, 'down');
                    break;
                    case 37: //left
                    e.preventDefault();
                    e.stopPropagation();
                    if ($currentNode.attr('aria-expanded') == 'false' || $currentNode.is('.noChildren')) {
                    nextNode($currentNode, 'parent');
                    } else {
                    toggleGroup($currentNode);
                    }
                    break;
                    case 39: //right
                    e.preventDefault();
                    e.stopPropagation();
                    if ($currentNode.attr('aria-expanded') == 'true') {
                        nextNode($currentNode, 'down');
                    } else {
                        toggleGroup($currentNode);
                    }
                    break;
                    case 13: //enter
                    case 32: //space
                    if ($('> a', $currentNode).length) {
                        location.href = $('> a', $currentNode).attr('href');
                        e.stopPropagation();
                    }
                    break;
                    case 46: // delete
                    case 8: // backspace
                    e.preventDefault();
                    e.stopPropagation();
                    deleteCategory();
break;
                    case 65: // a
                    e.preventDefault();
                    e.stopPropagation();
                    showNewCategoryDialog();
                    break;
                            case 73: // i
                    e.preventDefault();
                    e.stopPropagation();
                    var $selected = $('.activedescendant', $tree);
                    $.ajax({
url: Routing.generate('categories_reorder_up', {id: $selected.data('id')}),
method: "POST",
success: function (result)
{
if (result.success == true) {
var $previous = $selected.prev();
$selected.detach();
$previous.before($selected);

// Get parent node
var $parent = $selected.parent().parent();
toggleGroup($parent);
toggleGroup($parent);
updateSelectedNode($selected.attr('id'));
}
}
                            });
                                break;
                            case 75: // k
                    e.preventDefault();
                    e.stopPropagation();
                    var $selected = $('.activedescendant', $tree);
                    $.ajax({
url: Routing.generate('categories_reorder_down', {id: $selected.data('id')}),
method: "POST",
success: function (result)
{
if (result.success == true) {
var $next = $selected.next();
$selected.detach();
$next.after($selected);

// Get parent node
var $parent = $selected.parent().parent();
toggleGroup($parent);
toggleGroup($parent);
updateSelectedNode($selected.attr('id'));
}
}
                            });
                                break;
                    }
                    }
                    else if (e.shiftKey) {
                        switch (e.which)
                        {
                    case 56:
                    e.preventDefault();
                    e.stopPropagation();
                    var $selected = $('.activedescendant', $tree);
                    $('>li', $tree).each(function()
                            {
                            toggleGroup($(this));
                            });
                    updateSelectedNode($selected.attr('id'));
                    break;
                        }
                    }
            }
            );
function createNode(li)
{
                    var $node = $(li);
                    var nodeId;
                    if ($node.parent().attr('role') == "tree") {
                    nodeId = $tree.attr('id') + '-node' + $('> li', $node.parent()).index($node);
                    } else {
                    nodeId = $node.closest('li[id]').attr('id') + '-' + $('> li', $node.parent()).index($node);
                    }
                    var $nodeLink = $('> a', $node);
                    $node.attr({'id': nodeId, 'role': 'treeitem', 'aria-selected': 'false', 'tabindex': '-1'});
                    $nodeLink.attr('tabindex', '-1').on('click', function() {
                        updateSelectedNode($(this).parent().attr('id'));
                        });
                    //if children exist
                    if ($node.has('ul').length) {
                    $node.addClass('hasChildren');
                    $childList = $('ul', $node);
                    $childList.attr({'role': 'group'}).hide();
                    //add toggle element and set aria-expanded on the link and aria-hidden on the child node list
                    $('<div aria-hidden="true" class="toggle collapsed">').insertBefore($nodeLink);
                    $node.attr('aria-expanded', 'false');
                    } else {//no children
                        $node.addClass('noChildren');
                    }
}

            //toggle div click and hover
            $('.toggle').on('click', function() {
                    toggleGroup($(this).parent());
                    }
                    ).hover(
                        function() {
                        $(this).toggleClass('hover');
                        }
                        );

                    $('#new-category-button').on('click', function()
                            {
                            showNewCategoryDialog();
                            });
$('#delete-category-button').on('click', function()
        {
        deleteCategory();
        });
function showNewCategoryDialog()
{
                            // Get selected node
                            var $selectedNode = $('#' +
                                $tree.attr('aria-activedescendant'));

                            if ($selectedNode.parent().attr('role') != 'tree') {
                            // We have a child node
                            $selectedNode = $selectedNode.parent().parent();
                            }

                            // Fetch new category form
                            $.ajax({
url: Routing.generate('categories_new_ajax',
         {'id': $selectedNode.data('id')}),
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
var data = $('>form', $('#new-category')).serialize();
$.post(
    Routing.generate('categories_create_ajax'),
    data,
    function (result)
    {
        var $node = $('<li data-id="' + result.id + '"></li>');
            $node.append($('<a href="' + result.url + '">' +
            result.name + '</a>'));
        $selectedNode.find('ul').append($node);
        createNode($node);

        $('#new-category').dialog('close');

        // Update and put focus here
        toggleGroup($selectedNode);
        if ($selectedNode.attr('aria-expanded') == 'false') {
        toggleGroup($selectedNode);
        }
        updateSelectedNode($node.attr('id'));
        $tree.focus();
    }
    );
}
}
                                });
}
function deleteCategory()
{
    $('#category-confirm-dialog').dialog({
modal: true,
buttons: {
Yes: function()
{
                    var $selected = $('.activedescendant', $tree);
                    $.ajax({
url: Routing.generate('categories_delete', {id: $selected.data('id')}),
method: "POST",
success: function (result)
{
if (result.success == true) {
var $newNode = $selected.next();
if ($newNode.length == 0) {
$newNode = $selected.prev();
}
$selected.detach();
toggleGroup($newNode.parent().parent());
toggleGroup($newNode.parent().parent());
updateSelectedNode($newNode.attr('id'));
}
else {
// Display the error
$message = $('<p tabindex="0">' + result.error + '</p>');
$('#category-error-dialog').append($message);
$('#category-error-dialog').dialog({
modal: true,
           buttons: {
OK: function ()
    {
        $(this).dialog('close');
        $(this).empty();
    }
           },
close: function ()
       {
        $(this).dialog('close');
        $(this).empty();
       }
                    });
}
}
                            });
$(this).dialog('close');
},
No: function ()
{
    $(this).dialog('close');
}
}
});
}
    }
    );
}
