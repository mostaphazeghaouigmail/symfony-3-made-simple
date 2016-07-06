

if($(".easyadmin-vich-image").length > 0){
    //clone lightbox image to edit the big format
    var img = $("#easyadmin-lightbox-image_imageFile img").clone();
    img.attr('id','image').css({'max-width':'100%','margin-top':50});

//add it to the dom
    $(".easyadmin-vich-image").before(img);

}

$('body').on("edit_image_script_loaded",initDarkroom);
getEditImageFiles();

//load darkroom scripts for image editing
function getEditImageFiles(){
    $("head").append("<link rel='stylesheet' type='text/css' href='/css/admin/darkroom.css' />");
    $.getScript("/js/admin/darkroom/fabric.js",function(){
        $.getScript("/js/admin/darkroom/darkroom.js",function(){
            $('body').trigger("edit_image_script_loaded");
        })
    })
}

//init darkroom c   nvas
function initDarkroom(){
    $('body').off("edit_image_script_loaded");

    if($("#image").length > 0){
        new Darkroom('#image',{
            minWidth: 100,
            minHeight: 100,
            maxWidth: 500,
            maxHeight: 500,
            plugins : {
                save: {
                    callback: function() {
                        postEditedImage(this.darkroom.canvas.toDataURL());
                    }
                }
            }
        });
    }

}

//post edited image on ajax
function postEditedImage(newImage){
    $.ajax({
        url:  Routing.generate('image_post_edit',{'id':$('form[data-entity="Image"]').attr('data-entity-id')}),
        type: "POST",
        contentType: false,
        processData: false,
        dataType: 'json',
        data: JSON.stringify({imageFile:newImage}),
        success: function (response) {
            if($(".easyadmin-thumbnail").length > 0){
                $(".easyadmin-thumbnail img").attr('src', $(".easyadmin-thumbnail img").attr('src')+"?v="+Math.random())
            }
        }
    });
}
