var wordID = false;
$(document).on('pageshow', function () {
    $('#words').tablesorter({widgets: ['zebra']});

});
$(document).on('click','tr:gt(0)', function (e) {
    $('#english').val($(this).find('.english').text());
    $('#russian').val($(this).find('.russian').text());
    $('#armenian').val($(this).find('.armenian').text());
    $('#editBackground').fadeIn(300);
    wordID = parseInt($(this).attr('id'));
});


$(document).on('click','#closeEditPopup',function () {
    $('#editBackground').hide();
});


$(document).on('click','#update',function () {
    $('#wait_dialog').activity({
        segments: 8,
        steps: 3,
        opacity: 0.1,
        width: 8,
        space: 0,
        length: 12,
        color: 'white',
        speed: 1.5
    }).find('p').text('Saving...');
    $('#wait_dialog').fadeIn('500');

    $.ajax({
        url: '/translate/update-word',
        method: 'post',
        data: {
            updateAjax: 1,
            english: $('#english').val(),
            russian: $('#russian').val(),
            armenian: $('#armenian').val(),
            wordID: wordID
        },
        success: function (data) {
            $('#wait_dialog p').text('Saving successfuly');
            $('#wait_dialog').fadeOut(1500, function () {
                $(this).find('p').text('');
                $(this).activity('false');
                $('#editBackground').hide();
            })
            $('#' + wordID + ' td.english').text($('#english').val());
            $('#' + wordID + ' td.russian').text($('#russian').val());
            $('#' + wordID + ' td.armenian').text($('#armenian').val());
        }
    });
});


$(document).on('click','#remove',function () {
    if (confirm('Are you sure?')) {
        $('#wait_dialog').activity({
            segments: 8,
            steps: 3,
            opacity: 0.1,
            width: 8,
            space: 0,
            length: 12,
            color: 'white',
            speed: 1.5
        }).find('p').text('Deleting...');
        $('#wait_dialog').fadeIn('500');
        $.ajax({
            url: '/translate/delete-word',
            method: 'post',
            data: {
                deleteAjax: 1,
                wordID: wordID
            },
            success: function (data) {
                $('#wait_dialog p').text('Deleting successfuly');
                $('#wait_dialog').fadeOut(1000, function () {
                    $(this).find('p').text('');
                    $('#editBackground').hide();
                    $('#' + wordID).hide(1000, function () {
                        $(this).remove();
                        $('#words').trigger('update').trigger("applyWidgets")
                    }).find('td').css('background-color', 'rgb(248, 195, 138)');
                })
            }
        });
    }
});


$(document).on('slidestop', "#flip-mini",function () {
    alert($(this).val());
    if ($(this).val() == 'off') {
        $('table tr').each(function (index, element) {
            $(this).find('td:gt(0)').css('opacity', 0)
        })
    } else {
        $('table tr').each(function (index, element) {
            $(this).find('td:gt(0)').css('opacity', 10)
        })
    }
})
$(function () {

    $('body').keydown(function (e) {
        if (e.which == 27)
            $('#editBackground').hide();
    });

})

var autocomplete_selected = false;

function getWords() {
    var words = [];
    $.ajax({
        url: '/translate/autocomplete',
        type: "post",
        dataType: "json",
        async: false,
        data: {
            word: $('#wordfield').val()
        },
        success: function (data) {
            words = data.words;
        }
    })
}

$(document).on('pageshow', "[data-role=page]", function () {
    setTimeout(function () {
        $('#wordfield').trigger('click').focus();
    });
});



$(document).on("listviewbeforefilter","#autocomplete2", function (e, data) {
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

$(document).on('tap','#get',function () {
     $(this).addClass('ajax-load').prop('disabled', 'disabled');
     var thisElement = $(this);
     $.ajax({
         url: "/translate/translate",
         dataType: "json",
         data: {word: $('.ui-input-search input').val()},
         type: "post",
         dataType: "json",
         success: function (data) {
             thisElement.removeClass('ajax-load').removeAttr('disabled');
             if (Object.keys(data.ru).length > 0) {
                 $('#russian').html('');
                 $('#armenian').html('');
                 $.each(data.ru, function (index, element) {
                     if (index == 0) {
                         $('#russian_selector').append('<option selected="selected">' + element + '</option>');
                     } else {
                         $('#russian_selector').append('<option>' + element + '</option>');
                     }
                 });
                 $('#russian_selector').selectmenu('refresh', true);
                 $.each(data.hy, function (index, element) {
                     if (index == 0) {
                         $('#armenian_selector').append('<option selected="selected" >' + element + '</option>');
                     }
                     $('#armenian_selector').append('<option>' + element + '</option>');
                 });
                 $('#armenian_selector').selectmenu('refresh', true);

                 $('#save').prop('disabled',false);
             }

         }
     })
 });

$(function () {

    $(document).on('keyup paste change input propertychange','.ui-input-search input', function () {
        $('#save').prop('disabled',true);
        if ($('#russian_selector').data("mobile-selectmenu") === undefined) {
            $('#russian_selector').selectmenu();
        }
        if ($('#armenian_selector').data("mobile-selectmenu") === undefined) {
            $('#armenian_selector').selectmenu();
        }
        $('#russian_selector').html('').selectmenu('refresh', true);
        $('#armenian_selector').html('').selectmenu('refresh', true);
        if (isNaN($(this).val()) && $(this).val() != '') {
            $('#get').prop('disabled',false);
        } else {
            $('#get').prop('disabled',true);
        }
    });

    $('#wordfield').keydown(function (e) {
        if (e.keyCode == '13') {
            $('#get').click();
        }
    })




})

$(document).on('click','#save',(function () {
    $(this).addClass('ajax-load');
    var thisElement = $(this);
    $.ajax({
        url: "/translate/add-word",
        data: {
            addAjax: 1,
            word: $('.ui-input-search input').val(),
            in_russian: $('#russian_selector option:selected').text(),
            in_armenian: $('#armenian_selector option:selected').text()
        },
        type: "post",
        success: function (data) {
            thisElement.removeClass('ajax-load').prop('disabled',true)
        }
    })
}))

$(document).on('click', '#autocomplete2 li a', function () {
    autocomplete_selected = true;
    $('.ui-input-search input').val($(this).text());
    $('#autocomplete2 li').each(function () {
        $(this).remove();
    })
})

$(document).on('pagecontainerchange',function(){
    $('input').focus();
});