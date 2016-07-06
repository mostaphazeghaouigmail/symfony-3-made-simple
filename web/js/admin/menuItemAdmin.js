
function setSortableMenuItem(){
    $('#nestable').nestable({
        maxDepth : 2
    });

    $('#nestable').on('change',function(a,b,c){
        saveMenuOrder();
    });

}

function saveMenuOrder(){

    var items = $('.menuItemList').children();
    var sort = [];

    $.each(items,function (index) {
        var item = $(this);
        sort.push(item.attr('data-id'));
        if(item.find('ol').length > 0){
            var children  = [];
            var _items = item.find('ol li');
            $.each(_items,function (index) {
                var _item = $(this);
                children.push(_item.attr('data-id'));
            });
            sort.push(children);
        }
    });

    $.post(Routing.generate('save_menu_position'),{'position':sort});
}

function replaceMenuItemTable(){
    $.get('get_menu_orderable',function(data){
        $('#main').append(data);
        setSortableMenuItem();
    });
}

function triggerEditMenuClick(id){
   document.location.href =  $('tr[data-id="'+id+'"').find('td a.action-edit').attr('href');
}

function triggerDeleteMenuClick(id){
    $('tr[data-id="'+id+'"').find('td a.action-delete')[0].click();
}

