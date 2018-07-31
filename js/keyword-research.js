jQuery(document).ready(function(){
    var $ = jQuery;
    
    var d = new Date();
    var m = d.getMinutes();

    sessionStorage.setItem("request_count", 0); 
    sessionStorage.setItem("request_minute", m);

    $("#form_wpkm").submit(function(){
        
        var keyword = $("#wpkm_keyword").val();
        disableButton($("#wpkm_submit"), "Please wait...");

        $.ajax({
            url: ajaxurl,
            type: 'post',
            data: {
                keyword: keyword,
                q: 'getkeywords',
                action: 'wpspy_ajax'
            },
            dataType: 'json',
            beforeSend: function (){
                disableButton($("#wpkm_submit"), "Please wait...");
            }, 
            success: function (json){
                if (json !== null && typeof(json) == 'object' && json != "" && json.hasOwnProperty('count')) {
                    $("#wpkm_results_count").html("Found <strong>"+json.count+"</strong> keywords phrases");

                    var x = 0;

                    $("#wpkm_results_div").html("<table id='table_keywords'><thead><tr><td>We found "+x+" keywords phrases for  "+
                        keyword+".</td></tr></thead><tbody></tbody></table><div>"+
                        "<a href='javascript:void(0);' class='wpkm_btn' id='export_keywords' data-keyword='"+keyword+"'>Export</a>"+
                    "</div>");

                    $("#wpkm_results_div").html("");
                    $("#wpkm_results_div").append(json.data);
                    $("#exportable_table").html("");
                    $("#table_keywords").clone().attr("id", "export_csv").appendTo('#exportable_table');

                    $("#table_keywords").dataTable();
                } else {
                    $("#wpkm_submit").after("<span class='error red'>Something went wrong while trying your keyword. Please try again later.</span>");
                    setTimeout(function() {
                        $("#wpkm_submit").next('span.error.red').fadeOut(function () {
                            $(this).remove();
                        });
                    }, 4000);
                }
            },
            complete: function () {
                enableButton($("#wpkm_submit"), "Go");
            },
            error: function (data){
                $("#wpkm_submit").after("<span class='error red'>Something went wrong while trying your keyword. Please try again later.</span>");
                setTimeout(function() {
                    $("#wpkm_submit").next('span.error.red').fadeOut(function () {
                        $(this).remove();
                    });
                }, 4000);
                enableButton($("#wpkm_submit"), "Go");
            }
        });
    });

    $(document).on("click", "#export_keywords", function(){
        exportTableToCSV.apply(this, [$("#export_csv"), 'Keyword Master '+$("#export_keywords").attr("data-keyword")+'.csv']);
    });


    $(document).on("click", "#import_list", function(){
        $("#table_domains").html("");
        $("#table_domains").html('<table id="generate_domains"></table>');
        var patterns = getPatterns();
        var extension = $("#tdl_extension").val();
        $("#generate_domains").append("<thead><tr><td>Generated Domains</td></tr></thead><tbody></tbody>");

        $("#export_csv tbody tr td").each(function(){
            var key = $(this).text();
            var name = key.replace(/\s+/g, "");
            name = name.replace(/[^a-zA-Z ]/g, "");
            
            for(var x=0; x<patterns.length; x++){       
                $("#generate_domains tbody").append("<tr><td class='fresh'>"+ patterns[x]+name+extension+"</td></tr>");
            }
                    
        });

        $("#generate_domains").dataTable();
        $("#export_domains").removeClass('hidden').show();
    });


    var previous; // the previous  extension

    $("#tdl_extension").on('focus', function () {
        // Store the current value on focus and on change
        previous = this.value;

    }).change(function() {
        // Do something with the previous value after the change
        var current = this.value;

        $("#generate_domains tbody tr td").each(function(){
            var text = $(this).text();
            text = text.replace(previous, current); 
            $(this).html(text);         
        });

        // Make sure the previous value is updated
        previous = current;
    });



    $("#wpkm_check_availability").click(function(){
        if( $("#generate_domains").html() !== "" ){
            disableButton($(this), "Please wait...");
            var table = $('#generate_domains').DataTable();
     
            var data = table.rows().data();

            var count = $("#generate_domains tbody tr td.fresh").length;

            if(checkNumberofRequests()){
            // if( count < 50 ){
                $("#generate_domains tbody tr td.fresh").each(function(){
                    var $this = $(this);
                    var domain = $this.text();
                    $.ajax({
                        url : ajaxurl,
                        type : "post",
                        dataType : "json",
                        data : {
                            "domain": domain,
                            "q": "checkdomain",
                            action: 'wpspy_ajax'
                        },
                        success: function (response) {
                            addCount();
                            console.log("count: "+sessionStorage.getItem("request_count")+", minute: "+sessionStorage.getItem("request_minute"));
                            
                            var availability = '<span class="pull-right red">Request Failed</span>';

                            if( response.domainAvailability ){
                                availability = (response.domainAvailability == "AVAILABLE") ? '<a href="http://'+domain+'" target="_blank" title="Click here">Available</a>' : '<a href="http://'+domain+'" target="_blank" class="red">Unavailable</a>';
                                // $this.html(domain+'<span class="pull-right">'+availability+'</span>');

                                availability = '<span class="pull-right">'+availability+'</span>';


                            }else{
                                alert("Opps, our server is busy yet... Please try again in a minute.");
                                // $this.html(domain+'<span class="pull-right red">Request Failed</span>');
                            }

                            if( $this.children('span.pull-right').length ){
                                $this.children('span.pull-right').replaceWith(availability);
                            }else{
                                $this.html(domain+availability);
                            }

                            $this.addClass("done");
                            enableButton($("#wpkm_check_availability"), "Check Availability");
                        },
                        error: function (data) {
                            console.log("Error: ");
                            console.log(data);
                        },
                        complete: function () {
                            $this.addClass("done");
                            enableButton($("#wpkm_check_availability"), "Check Availability");
                        }
                    });
                });
            // }
            }else{
                alert("Sorry, we've run on the request limit per minute. Please try again in a minute.");
            }
        }else{
            return false;
        }
        
    });

    $("#wpkm_create_domains").click( function(){
    
        disableButton($("#wpkm_create_domains"), "Please wait...");

        $("#table_domains").html("");
        $("#table_domains").html('<table id="generate_domains"></table>');
        $("#generate_domains").append("<thead><tr><td>Generated Domains</td></tr></thead><tbody></tbody>");

        var patterns = getPatterns();
        var extensions = getExtensions();
        var name = $("#domain_name").val();
        name = name.replace(/\s+/g, "");
        name = name.replace(/[^a-zA-Z ]/g, "");
        if(name.indexOf('.') === -1){
            //alert("no dash found.");
        }else{
            name = name.substring(0, name.indexOf('.'));
        }
        
            
        for(var x=0; x<patterns.length; x++){   
            for(var y=0; y<extensions.length; y++){
                $("#generate_domains tbody").append("<tr><td class='fresh'>"+ patterns[x]+name+extensions[y]+"</td></tr>");
            }   
        }

        $("#generate_domains").dataTable();
        enableButton($("#wpkm_create_domains"), "Create Domains");
        $("#export_domains").removeClass('hidden').show();
        
    }); 



    $(document).on("click", "#export_domains", function(){
        var table = $('#generate_domains').DataTable();
 
        var data = table
            .rows()
            .data();
 
        var tbody = "";
        for (var i = 0; i < data.length; i++) {
            tbody+= "<tr><td>"+data[i]+"</td></tr>";
        }
        
        $(".export").append('<table id="exportable_domains" class="hidden"><thead><tr><td>Generated Domains</td></tr></thead><tbody>'+tbody+'</tbody></table>');

        exportTableToCSV.apply(this, [$("#exportable_domains"), 'Keyword Master - Generated Domains.csv']);
    });


    function checkNumberofRequests(){
        var count = sessionStorage.getItem("request_count");
        var minute = sessionStorage.getItem("request_minute");

        var d = new Date();
        var n = d.getMinutes();
        
        if( minute == n ){
            if( count == 49 ){
                return false; // stop sending request for a while
            }else{              
                return true; // continue sending request
            }   
        }else{
            sessionStorage.setItem("request_minute", minute);
            sessionStorage.setItem("request_count", 0);
            return true;
        }       
    }

    function addCount(){
        var count = parseInt(sessionStorage.getItem("request_count"));
        count++;
        sessionStorage.setItem("request_count", count);
    }

    function getPatterns(){
        return ["buy", "get", "order"];
    }

    function getExtensions(){
        return [".com", ".org", ".net", ".info"];
    }

    function enableButton(btn, text){
        btn.removeClass("disabled");
        btn.removeAttr("disabled", "disabled");
        btn.val(text);
    }

    function disableButton(btn, text){
        btn.addClass("disabled");
        btn.attr("disabled", "disabled");
        btn.val(text);
    }




    function exportTableToCSV($table, filename) {

        var $rows = $table.find('tr:has(td)'),

            // Temporary delimiter characters unlikely to be typed by keyboard
            // This is to avoid accidentally splitting the actual contents
            tmpColDelim = String.fromCharCode(11), // vertical tab character
            tmpRowDelim = String.fromCharCode(0), // null character

            // actual delimiter characters for CSV format
            colDelim = '","',
            rowDelim = '"\r\n"',

            // Grab text from table into CSV formatted string
            csv = '"' + $rows.map(function (i, row) {
                var $row = $(row),
                    $cols = $row.find('td');

                return $cols.map(function (j, col) {
                    var $col = $(col),
                        text = $col.text();

                    return text.replace('"', '""'); // escape double quotes

                }).get().join(tmpColDelim);

            }).get().join(tmpRowDelim)
                .split(tmpRowDelim).join(rowDelim)
                .split(tmpColDelim).join(colDelim) + '"',

            // Data URI
            csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csv);

        $(this).attr({
            'download': filename,
            'href': csvData,
            'target': '_blank'
        });
    }


});









