$(document).ready(function(){
    if ($("#progress-bars").length) {
        updateBars();
    }
});



function updateBars()
{
    var ids = $(".progress-bar").map(function(){
        return $(this).data('id');
    }).toArray();

    var pump = setInterval(function(){
        $.ajax({
            url: "/api/status",
            data: {ids: ids.join(',')},
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    ids = [];
                    $.each(data.data, function(key, value) {
                        if (!value) return;
                        var bar = $("#progress-"+key);
                        bar.css({width: value.percent+'%'}).text(value.label);
                        if(value.status == 'success') {
                            bar.addClass('progress-bar-success');
                        }
                        if(value.status == 'error') {
                            ids.splice(ids.indexOf(key), 1);
                            bar.addClass('progress-bar-danger');
                        }
                        if(value.status == 'in_progress') {
                            ids.push(key);
                        }
                    })
                }

                if (ids.length == 0) {
                    console.log("end");
                    clearInterval(pump);
                }
            }
        });
    }, 2000);
}

function runApi(formElem)
{
    var form = $(formElem);
    var button = form.find('button');
    var helpBlock = form.find('.help-block');
    button.button('loading');
    helpBlock.parents('.form-group').removeClass('has-error');

    if (form.find("input:checked").length) {
        $.ajax({
            url: "/api/run",
            method: 'post',
            data: form.serialize(),
            success: function(data){
                location.reload();
            }
        });
    } else {
        helpBlock.text('Выберите хотя бы один 1 элемент');
        helpBlock.parents('.form-group').addClass('has-error');
        button.button('reset');
    }



}

function renderProgressBars(form, pids)
{
    $("#progress-bars").load('/api/bars', function(){
        updateBars();
    });
}