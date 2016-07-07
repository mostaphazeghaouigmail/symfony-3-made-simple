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
        $.getScript("/js/admin/menuItemAdmin.js",function(){
            replaceMenuItemTable();
        });
    }

    //image editing
    if($("body").is('.edit-image') || isImageable){
        if(typeof(Darkroom) == "undefined")
        initEditImage();
    }

    //prevent to edit template folder
    if($("body").is('.edit-theme')){
        $('#theme_folder').addClass('disabled');
        handleCreateThemeStructure();
    }

    if($('body').is('#easyadmin-list-Theme'))
        initMasonrytheme();

    //disable an input
    $('.disabled').on('keydown focus keypress',function(){
        $('.disabled').trigger('blur');
        return false;
    });

    $(".action-link_assets").click(linkAssets);

    $( document ).ajaxSend(createLoader);
    $( document ).ajaxStop(removeLoader);

}

function initEditImage(){
    $.getScript("/js/admin/editImageScript.js");
}

function createLoader(){
    if($('.loading').length == 0 && !$('.modal').is(':visible'))
        $("body").append('<div class="loading-background"></div><div class="loading"></div>');
}

function removeLoader(){
    $('.loading,.loading-background').remove();
}

function handleCreateThemeStructure(){
    var id = $("form").attr('data-entity-id');
    $.get(Routing.generate('check_theme_strucutre',{'id':id}),function(data){
       if(data.success === false){
           eModal
               .confirm({
                   'title'   : "The theme file strutcture doesn't exist",
                   'message' : "Do you want to create it ?"
               }).then(
               createThemeStructure,
               function(){eModal.close();}
           )
       }
    });

}

function createThemeStructure(){
    var id = $("form").attr('data-entity-id');
    $.get(Routing.generate("create_theme_structure",{'id':id}),function(){
        eModal.alert("Done !");
    });
}

function linkAssets(){
    var href = $(this).attr("href");
    eModal.confirm({
        title   :"Assets Link",
        message :"Your are about to create assets symlink in web/themes folder, it is without danger..."
    }).then(
        function(){
            $.get(href,function(){
             eModal.alert('done !');
            });
        },
        function(){
            eModal.close;
        }
    );

    return false;
}

function initMasonrytheme(){
    $(".themes-wrapper").imagesLoaded(function(){
        $('.themes-inner').masonry({
            itemSelector: '.theme-item', // use a separate class for itemSelector, other than .col-
            percentPosition: true
        });
    })
}
