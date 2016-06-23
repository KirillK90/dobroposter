$(".fileinput-button").each(function(){
    imageupload($(this));
});

$(document).on('click', ".fileinput-remove", function(){
    if ($(this).hasClass('disabled')) return;
    removeImage($(this));
});



$(document).on('click', ".sel-part-button", function(){
    $($(this).data('target')).data('button', $(this));
});

$('body').on('click','[data-img-choose]',function(){
    var self = $(this);
    var modal = self.parents('.modal');
    var button = modal.data('button');
    modal.modal('hide');
    if(self.attr('data-id')){
        $.ajax({
            url: "/images/participant/" + self.attr('data-id'),
            dataType: "json",
            success: function (data) {
                if (data.success) {
                    var r = data.result;

                    setImage(button, data.filename, data.src);
                } else {
                    addUploadError(button, data.error);
                }
            },
            error: function (e, data) {
                alert("Ошибка сервера");
            }
        });
    }
});

$(document).on('keyup','.participant-filter',(function(){
    var buff;
    var delay = 300;
    var blockClass = '.media-list';
    var elemClass = '.media';
    var valClass = '.media-heading';
    var titleClass = 'h3';
    return function(){
        var self = $(this);
        var modal = self.parents('.modal');
        var blocks = modal.find(blockClass);
        clearTimeout(buff);
        buff = setTimeout(function(){
            $.each(blocks,function(iB, block){
                var currBlock = $(block);
                var elements = currBlock.find(elemClass);
                var hidden = true;
                $.each(elements, function(iE, element){
                    var currElement = $(element);
                    var isMatch = currElement.find(valClass).text().match(new RegExp(self.val(),'ig'));
                    if(isMatch){
                        hidden = false;
                        currElement.fadeIn(0);
                    }else{
                        currElement.fadeOut(0);
                    }
                });
                if(hidden){
                    currBlock.fadeOut(0);
                    currBlock.prev(titleClass).fadeOut(0);
                }else{
                    currBlock.fadeIn(0);
                    currBlock.prev(titleClass).fadeIn(0);
                }
            });
        },delay);
    };
})());

function addUploadError(button, error)
{
    var container = button.parents('.form-group');
    container.addClass('has-error');
    container.find(".help-block-error").text(error);
}

function removeImage(button)
{
    var container = button.parents('.form-group');
    container.removeClass('has-error').find(".help-block-error").text('');
    container.find(".uploaded-image").html('');
    container.find('.input-filename').val('');
    container.find(".fileinput-remove").addClass('disabled');
}

function setImage(button, filename, src)
{
    button.parents('.form-group').removeClass('has-error').find(".help-block-error").text('');
    button.find('.input-filename').val(filename);
    if (filename) {
        var img = new Image();
        img.src = src;
        img.onload = function(){addSizes(this);};
        button.parents('.form-group').find(".uploaded-image").html(img);
        button.parents('.form-group').find(".fileinput-remove").removeClass('disabled');
    } else {
        removeImage(button);
    }

}

function imageupload(button)
{
    var type = button.data('type');
    var container = button.parents('.form-group');
    button.fileupload({
        url: "/images/upload/" + type,
        dataType: "json",
        done: function (e, data) {
            if (data.result.success) {
                var r = data.result;
                setImage(button, r.filename, r.src);
            } else {
                addUploadError(button, data.result.error);
            }
        },
        error: function (e, data) {
            alert("Ошибка сервера");
        }
    });
}