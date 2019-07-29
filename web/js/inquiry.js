$(".multi-delete").on("click", function () {
    var keys = $('.grid-view').yiiGridView("getSelectedRows");
    if (keys.length  == 0) {
        return false;
    }
    var href = $(this).attr('href');
    if (href.indexOf("?") != -1) {
        href = href+'&id='+keys.join(',');
        $(this).attr('href', href);
    }
    else {
        href = href+'?id='+keys.join(',');
        $(this).attr('href', href);
    }
});

$(".multi-sort").on("click", function () {
    var inputs = $('.grid-view').find('input[name^="sort"]');

    if (inputs.length  == 0) {
        return false;
    }

    inputs.each(function() {
        $(this).clone().attr('type', 'hidden').appendTo('form[name="sort"]')
    })

    $('form[name=sort]').submit();

    return false;
});

$("form").on("beforeValidate", function (e) {
    $(":submit").attr("disabled", true).addClass("disabled");
});
$("form").on("afterValidate", function (e) {
    if (cheched = $(this).data("yiiActiveForm").validated == false) {
        $(":submit").removeAttr("disabled").removeClass("disabled");
    }
});
$("form").on("beforeSubmit", function (e) {
    $(":submit").attr("disabled", true).addClass("disabled");
});

function selectRadio(obj){
    $(obj).find('input').attr("checked",'true');
}
　
window.onload = run;
function run(){

}　