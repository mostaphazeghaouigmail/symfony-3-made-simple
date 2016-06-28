var uploader = {

    active       : false,
    parentId     : false,
    parentClass  : false,
    element      : false,

    show : function(){
        var _this = this;
        eModal.ajax({
            title :  "Uploader",
            url   :  Routing.generate('uploader',{parentId:_this.parentId,parentClass:_this.parentClass})
        });

    },

    init : function(){
        if($('.imageable').length > 0){
            if($('.imageable').find("input[type=text]").length > 0){
                var data = $('.imageable').find("input[type=text]").val();
            } else {
                var data = $('.imageable').find(".form-control").html();
            }
            data = $.parseJSON(data);
            this.parentId = data.id;
            this.parentClass = data.name;

            if(this.parentId){
                this.createUi();
            }
        }

    },

    createUi : function(){
        var _button = "<button onclick='uploader.show()' class='btn btn-primary' style='position:absolute;right:10px;top:10px'>Medias</button>";
        $('.content-header').append(_button);
    },

    saveOrder : function(){
        var items = $('#image-list').children();
        var sort = [];
        $.each(items,function () {
            sort.push($(this).attr('data-id'));
        });
        $.post(Routing.generate('save_image_position'),{'position':sort});
    },

    editImage : function(id){
        eModal.ajax({
            title   :   "Edition",
            url     :  Routing.generate('image_edit',{'id':id}),
            buttons :  [
                {text:'Save', close:false, click:function(){
                    var form = $('#edit-image-form-container form');
                    $.post(form.attr('action'),form.serialize(),function(data){
                        if(data.success){
                            uploader.show();
                        }
                    });
                    return false;
                }},
                {text:'Delete', close:false, style:'danger' ,click:function(){
                    uploader.deleteImage(id);
                    uploader.show();
                    return false;
                }},
                {text:'Close', close:false, click:function(){
                    uploader.show();
                    return false;
                }},
            ]
        });
    },

    deleteImage : function(id){
        $.get(Routing.generate('image_delete',{'id':id}));
        $('#image'+id).fadeOut();
        return false;
    }
};