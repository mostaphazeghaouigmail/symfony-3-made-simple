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
    $('#menuitem_route').on('focus',function(){
        eModal.ajax({
            title   : "Menu Url",
            url     : Routing.generate('get_route'),
            buttons : []
        });
    });

    //Template article selection
    $('#page_template').on('focus',function(){
        eModal.ajax({
            title   : "Page Template Selection",
            url     : Routing.generate('get_templates',{'model':'page'}),
            buttons : []
        });
    });

    //Template page selection
    $('#article_template').on('focus',function(){
        eModal.ajax({
            title   : "Article Template Selection",
            url     : Routing.generate('get_templates',{'model':'article'}),
            buttons : []
        });
    });

    //sortable menu
    if($("body").is("#easyadmin-list-MenuItem")){
        createLoader();
        $.getScript("/js/admin/menuItemAdmin.js",function(){
            replaceMenuItemTable();
        });
    }

    //image editing
    if($(".easyadmin-vich-image") || isImageable){
        if(typeof(Darkroom) == "undefined")
        initEditImage();
    }

}

function initEditImage(){
    $.getScript("/js/admin/editImageScript.js");
}

function createLoader(){
    if($('.loading').length == 0)
        $("body").append('<div class="loading"></div>');
}

function removeLoader(){
    $('.loading').remove();
}