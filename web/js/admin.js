var isImageable = document.getElementsByClassName("imageable").length > 0;
var isCodable =   document.getElementsByClassName("codeable").length > 0 ;
$(document).ready(init);

function init() {
    if (isImageable){
        uploader.init();
        $.post(Routing.generate('set_current'),{'parentClass':uploader.parentClass,'parentId':uploader.parentId});
    }

    if(isCodable){
        var container = document.getElementsByClassName("codeable")[0];
        var editor = CodeMirror.fromTextArea(container.getElementsByTagName('textarea')[0], {
            lineNumbers: true
        });
    }


    //MENU ITEM

    if($("body").is('#easyadmin-list-MenuItem')){
        $('.table tbody').sortable();
        $('.table tbody').on('sortupdate',function(a,b,c){
            var items = $('.table tbody').children();
            var sort = [];

            $.each(items,function (index) {
                sort.push($(this).attr('data-id'));
                $(this).find('td[data-label="Position"]').html(index);
            });
            $.post(Routing.generate('save_menu_position'),{'position':sort});
        })
    }
    $('#menuitem_route').on('focus',function(){
        eModal.ajax({
            title   : "Menu Url",
            url     : Routing.generate('get_route'),
            buttons : []
        });
    });

    //Template selection
    $('#page_template').on('focus',function(){
        eModal.ajax({
            title   : "Page Template Selection",
            url     : Routing.generate('get_templates',{'model':'page'}),
            buttons : []
        });
    });
    
    $('#article_template').on('focus',function(){
        eModal.ajax({
            title   : "Article Template Selection",
            url     : Routing.generate('get_templates',{'model':'article'}),
            buttons : []
        });
    });

}

