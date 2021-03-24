//jQuery(document).ready(function($){var BUTTON="#mylist_btn_",uriAjax=gdMyListAjax.ajaxurl,boxList=gdMyListAjax.boxList,loading_icon=gdMyListAjax.loading_icon,button=gdMyListAjax.button,nonce=gdMyListAjax.nonce,buttonHtml="";function createBtn(){0<$(".js-item-mylist").length&&$.get(button,function(source){buttonHtml=source,$(".js-item-mylist").each(function(){var itemId=BUTTON+$(this).data("id"),nameVar="myListButton"+$(this).data("id"),data=eval(nameVar);renderTemplate(itemId,source,data)})})}function showLoading(t){data=$.parseJSON('{"showLoading": {"icon": "'+loading_icon+'"}}'),renderTemplate(t,buttonHtml,data)}function renderTemplate(t,a,n){n=Handlebars.compile(a)(n);$(t).html(n)}"undefined"!=typeof myListData&&$.get(boxList,function(t){renderTemplate("#myList_list",t,myListData)}),createBtn(),$("body").on("click",".js-gd-add-mylist",function(){var t=$(this).data("postid"),a=$(this).data("userid"),n=BUTTON+t;showLoading(n),$.ajax({type:"POST",dataType:"json",url:uriAjax,data:{action:"gd_add_mylist",itemId:t,userId:a,nonce:nonce}}).done(function(t){renderTemplate(n,buttonHtml,t)})}),$("body").on("click",".js-gd-remove-mylist",function(){var a=$(this).data("postid"),t=$(this).data("userid"),n=$(this).data("styletarget"),e=BUTTON+a;showLoading(e),$.ajax({type:"POST",dataType:"json",url:uriAjax,data:{action:"gd_remove_mylist",itemId:a,userId:t,nonce:nonce}}).done(function(t){"mylist"==n?$("#mylist-"+a).closest(".gd-mylist-box").fadeOut(500):renderTemplate(e,buttonHtml,t)})})});
jQuery(document).ready(function($) {
    var BUTTON = "#mylist_btn_",
        uriAjax = gdMyListAjax.ajaxurl,
        boxList = gdMyListAjax.boxList,
        loading_icon = gdMyListAjax.loading_icon,
        button = gdMyListAjax.button,
        nonce = gdMyListAjax.nonce,
        buttonHtml = "";

    function createBtn() {
        $( document ).ready(function() {
            0 < $(".js-item-mylist").length && $.get(button, function(source) {
                buttonHtml = source, $(".js-item-mylist").each(function() {
                    var itemId = BUTTON + $(this).data("id"),
                        nameVar = "myListButton" + $(this).data("id"),
                        data = eval(nameVar);
                    renderTemplate(itemId, source, data)
                })
            });
        });
    }

    function showLoading(t) {
        data = $.parseJSON('{"showLoading": {"icon": "' + loading_icon + '"}}'), renderTemplate(t, buttonHtml, data)
    }

    function renderTemplate(t, a, n) {
        n = Handlebars.compile(a)(n);
        $(t).html(n)
    }
    "undefined" != typeof myListData && $.get(boxList, function(t) {
        renderTemplate("#myList_list", t, myListData)
    }), 
    createBtn(), 
    $("body").on("click", ".js-gd-add-mylist", function() {
        var t = $(this).data("postid"),
            a = $(this).data("userid"),
            n = BUTTON + t;
        showLoading(n), $.ajax({
            type: "POST",
            dataType: "json",
            url: uriAjax,
            data: {
                action: "gd_add_mylist",
                itemId: t,
                userId: a,
                nonce: nonce
            }
        }).done(function(t) {
            renderTemplate(n, buttonHtml, t)
        })
    }), 
    $("body").on("click", ".js-gd-remove-mylist", function() {
        var a = $(this).data("postid"),
            t = $(this).data("userid"),
            n = $(this).data("styletarget"),
            e = BUTTON + a;
        showLoading(e), $.ajax({
            type: "POST",
            dataType: "json",
            url: uriAjax,
            data: {
                action: "gd_remove_mylist",
                itemId: a,
                userId: t,
                nonce: nonce
            }
        }).done(function(t) {
            "mylist" == n ? $("#mylist-" + a).closest(".gd-mylist-box").fadeOut(500) : renderTemplate(e, buttonHtml, t)
        })
    })
});