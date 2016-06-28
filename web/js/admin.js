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

}

