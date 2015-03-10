<!--<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />-->
<?php
//Yii::app()->clientScript->registerCoreScript('jquery.ui');
//?>
<style>
    .ui-menu-item{
        font-size: 20px;
        border: 1px solid silver;
        border-radius: 6px;
        height: 50px;
        
    }
    .labels{
        font-size: 20px;
        font-weight: bolder;
        color: blue;
    }
    .results{
        font-size: 25px;
        font-weight: bolder;
        color: red;
        font-family: Tahoma;
    }
    
    /*input[type="button"]{*/
        /*width: 250px;*/
        /*font-size: 20px;*/
        /*border: 1px solid blue;*/
        /*background: silver;*/
        /*border-radius: 5px;*/
    /*}*/
    
    .ajax-load{
        background-image: url("/images/ajax-loader.gif")!important;
        background-repeat: no-repeat!important;
        background-position: 212px 8px!important;
    }

    .ui-menu-item{
        height: 35px;
    }
    .ui-autocomplete{
        z-index: 9999999!important;
    }
    .ui-autocomplete { height: 200px; overflow-y: scroll; overflow-x: hidden;}
</style>
<script>
    var autocomplete_selected = false;

    function getWords(){
        var words = [];
        $.ajax({
            url:'/translate/autocomplete',
            type:"post",
            dataType:"json",
            async:false,
            data:{
                word:$('#wordfield').val()
            },
            success:function(data){
                words =  data.words ;
            }
        })
    }

    $(function(){
        $(document).on('pageshow', "[data-role=page]", function () {
            setTimeout(function () {
                $('#wordfield').trigger('click').focus();
            });
        });

//        $('#wordfield').autocomplete(
//            {
//                source: function(request, response) {
//                    $.ajax({
//                        url: "?r=translate/Autocomplete",
//                        dataType: "json",
//                        data: {word:$('#wordfield').val()},
//                        success: function(data) {
//                            response(data);
//                        }
//                    });
//                },
//                delay:0,
//                minLength: 3
//            }
//        );
//        $('#wordfield').die();
        $('.ui-input-search input').on('keyup paste change input propertychange',function(){
            $('#save').button('disable');
            $('#russian').html('').selectmenu('refresh', true);
            $('#armenian').html('').selectmenu('refresh', true);
            if(isNaN($(this).val()) &&  $(this).val() != ''){
                $('#get').button('enable');
            }else{
                $('#get').button('disable');
            }
        })
        $('#wordfield').keydown(function(e){
            if(e.keyCode == '13'){
                $('#get').click();
            }
        })
        $('#get').click(function(){
            $(this).addClass('ajax-load').prop('disabled','disabled');
            var thisElement = $(this); 
        $.ajax({
            url: "?r=translate/Translate",
                        dataType: "json",
                        data: {word:$('.ui-input-search input').val()},
                        type:"post",
                        dataType:"json",
                        success: function(data) {
                            thisElement.removeClass('ajax-load').removeAttr('disabled');
                            console.log(data);
                            if(Object.keys(data.ru).length > 0){
                                $('#russian').html('');
                                $('#armenian').html('');
                                $.each(data.ru,function(index,element){
                                    if(index ==0){
                                        $('#russian').append('<option selected="selected">'+element+'</option>');
                                    }else{
                                        $('#russian').append('<option>'+element+'</option>');
                                    }
                                });
                                $('#russian').selectmenu('refresh', true);
                                $.each(data.hy,function(index,element){
                                    if(index ==0){
                                        $('#armenian').append('<option selected="selected" >'+element+'</option>');
                                    }
                                    $('#armenian').append('<option>'+element+'</option>');
                                });
                                $('#armenian').selectmenu('refresh', true);

                                $('#save').button('enable');
                            }

                        }
            })
        })
        $('#save').off();
        $('#save').click(function(){
            $(this).addClass('ajax-load');
            var thisElement = $(this); 
            $.ajax({
                url: "/translate/add-word",
                data: {
                    addAjax:1,
                    english:$('.ui-input-search input').val(),
                    russian:$('#russian option:selected').text(),
                    armenian:$('#armenian option:selected').text()
                },
                type:"post",
                success: function(data) {
                    thisElement.removeClass('ajax-load').button('disable');
                    console.log(data);
                }
            })
        })

        $("#autocomplete2").on("listviewbeforefilter", function (e, data) {
            autocomplete_selected = false;
            var $ul = $(this),
                $input = $(data.input),
                value = $input.val(),
                html = "";
            $ul.html("");
            if (value && value.length > 2) {
                $ul.html("<li><div class='ui-loader'><span class='ui-icon ui-icon-loading'></span></div></li>");
                $ul.listview("refresh");
                $.ajax({
                    url: "/translate/autocomplete",
                    dataType: "json",
                    crossDomain: true,
                    data: {
                        word: $input.val()
                    }
                })
                    .then(function (response) {
                        $.each(response, function (i, val) {
                            html += "<li><a href='#' class='autocomplete-result'>" + val + "</a></li>";
                        });
                        $ul.html(html);
                        $ul.listview("refresh");
                        $ul.trigger("updatelayout");
                    });
            }
        });

    })

    $(document).on('click','#autocomplete2 li',function(){
        autocomplete_selected = true;
        $('.ui-input-search input').val($(this).text());
        $('#autocomplete2 li').each(function(){
            $(this).remove();
        })
    })
</script>

<!--<input type="text" id="autocomplete" autofocus="" style="font-size: 23px;" placeholder="Type your word" />-->
<ul id="autocomplete2" data-role="listview" data-inset="true" data-filter="true" data-filter-reveal="false" data-filter-placeholder="Type here..." data-filter-theme="a"></ul>
<br />
<input type="button" value="Get translate" id="get" disabled="disabled"/>
<br /><br />
<label for="russian" class="labels">Russian: </label>
<select id="russian" class="results">
</select>
<br />
<label for="armenian" class="labels">Armenian: </label>
<select id="armenian" class="results">
</select>
<br />
<input type="button" id="save" value="Save word" disabled="disabled">
