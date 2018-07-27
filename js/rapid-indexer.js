jQuery(document).ready(function(){
    var $ = jQuery;
    

    $("#form_wpindexer").submit(function(e){

        var domain = $("#wpindexer_url").val();

        var btn = $("#wpindexer_submit");

        if (domain == "") {
            return;
        }

        $.ajax({
            url : ajaxurl,
            type : 'post',
            data : {
                'q': 'get_indexes',
                'domain' : domain,
                action: 'wpspy_ajax'
            },
            beforeSend: function () {
                btn.attr("disabled", "disabled");
                btn.addClass("disabled");
                btn.val("Please wait...");
            },
            success: function(data) {
                $("#wpindexer-results-div").html(data);
            
                check_links2();
                $("#wpindexer_results").dataTable();
                btn.removeAttr("disabled");
                btn.removeClass("disabled");
                btn.val("Go");
            },
            error: function (e) {
                console.warn(e);
                alert("Something went wrong while we tried getting the indexes. Please try again later.");
            },
            complete: function () {
                btn.removeAttr("disabled");
                btn.removeClass("disabled");
                btn.val("Go");
            }
        });
    });

    $("#wpindexer_url").change(function(){
        var url = $(this).val();
        if( url.substr(0,7) == "http://" ){
            $(this).val(url.substr(7,url.length-1));
        }else if( url.substr(0,8) == "https://" ){
            $(this).val(url.substr(8, url.length-1));
        }else if( url.substr(0,11) == "http://www."){
            $(this).val(url.substr(11, url.length-1));
        }else if( url.substr(0,12) == "https://www."){
            $(this).val(url.substr(12, url.length-1));
        }
    });


    $(document).on("click", ".paginate_button", function(){
        check_links();
    });


    function check_links2(){
        $("#wpindexer_results tr td div.wpindexer_entry a").each(function(){
            var $this = $(this);
            $.ajax({
                url : ajaxurl,
                type : "post",
                data : {
                    'url': $this.attr("href"),
                    'q': 'check_url',
                    'action': 'wpspy_ajax'
                },
                success: function (data) {
                    $this.addClass(data);
                },
                error: function (e) {
                    console.warn(e);
                }
            });
        });
    }

    function check_links(){
        $(".wpindexer_entry a").each(function(){
            var $this = $(this);
            $.ajax({
                url : ajaxurl,
                type : "post",
                data : {
                    'url': $this.attr("href"),
                    'q': 'check_url',
                    'action': 'wpspy_ajax'
                }
            }).done(function(data){
                console.log(data);
                $this.addClass(data);
            });
        });
    }

});