<style>
    td {
        font-size: 15px;
        cursor: pointer;
        font-family: Tahoma;
    }

    tr {
        background-color: rgba(224, 224, 224, 1) !important;
        background-color: rgba(224, 224, 224, 1) !important;
    }

    tr:nth-child(even) {
        background-color: rgba(247, 251, 252, 1) !important;
    }

    .tablesorter {
        font-family: arial;
        background-color: #CDCDCD;
        margin: 10px 0pt 15px;
        font-size: 8pt;
        width: 100%;
        text-align: left;
    }

    .tablesorter thead tr th, table.tablesorter tfoot tr th {
        background-color: rgb(82, 82, 82);
        border: 1px solid #FFF;
        font-size: 8pt;
        padding: 4px;
    }

    .tablesorter thead tr .header {
        background-image: url('images/bg.gif');
        background-repeat: no-repeat;
        background-position: center right;
        cursor: pointer;
    }

    .tablesorter tbody td {
        color: #3D3D3D;
        padding: 4px;
        background-color: #FFF;
        vertical-align: top;
    }

    .tablesorter tbody tr.odd td {
        background-color: rgb(224, 224, 224);
    }

    .tablesorter thead tr .headerSortUp {
        background-image: url('images/asc.gif');
    }

    .tablesorter thead tr .headerSortDown {
        background-image: url('images/desc.gif');
    }

    .tablesorter thead tr .headerSortDown, table.tablesorter thead tr .headerSortUp {
        background-color: #ADC8E8;
        color: black;
    }

    input {
        font-size: 25px;
        width: 90%;
        font-family: Tahoma;
    }

    #editBackground {
        display: none;
        position: fixed;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.67);
        left: 0;
        top: 0;
        z-index: 999;
    }

    #edit {
        margin: 0 auto;
        width: 90%;
        border: solid 1px silver;
        background-color: steelblue;
        overflow: hidden;
        margin-top: 5%;
        padding: 5px;
        text-align: center;
        border-radius: 15px;
        position: relative;
    }

    #edit_buttons_list {
        margin: 0;
        padding: 0;
    }

    #edit_buttons_list li {
        display: inline;
        float: left;
        /*margin-left: 20px;*/
    }

    #edit_buttons_list li button {
        border: 1px solid transparent;
        background: none;
        border-radius: 5px;
    }

    #edit_buttons_list li button:hover {
        cursor: pointer;
        border-color: #ffffff;
    }

    #wait_dialog {
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.76);
        z-index: 55;
        position: absolute;
        left: 0;
        top: 0;
        border-radius: 15px;
        display: none;
    }

    #toggle_container {
        position: fixed;
        bottom: 10px;
        right: 30px;
        background-color: rgba(0, 0, 0, 0.66);
        padding: 5px;
        border-radius: 7px;
        z-index: 99;
    }

    #flip-mini-label {
        text-shadow: none !important;
        color: white !important;
    }
</style>

<script>
    var wordID = false;
    $(document).on('pageshow', function () {
        $('#words').tablesorter({widgets: ['zebra']});

    })
    $(function () {
        $('tr:gt(0)').on('click', function (e) {
//            wordID = $(this).attr('id');
//            $.ajax({
//                url:'?r=translate/getWord',
//                method:'post',
//                data:{id:wordID},
//                dataType:"json",
//                success:function(data){
//                    console.log(data);
//                    $('#english').text(data[0].word);
//                    $('#russian').text(data[0].in_russian);
//                    $('#armenian').text(data[0].in_armenian);
//                }
//            });
            $('#english').val($(this).find('.english').text());
            $('#russian').val($(this).find('.russian').text());
            $('#armenian').val($(this).find('.armenian').text());
            $('#editBackground').fadeIn(300);
            wordID = parseInt($(this).attr('id'));
        })

        $('#closeEditPopup').click(function () {
            $('#editBackground').hide();
        })

        $('body').keydown(function (e) {
            if (e.which == 27)
                $('#editBackground').hide();
        });

        $('#save').click(function () {
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
                url: '?r=translate/updateWord',
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
        })

        $('#remove').click(function () {
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
                    url: '?r=translate/deleteWord',
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
        })

        $("#flip-mini").on('slidestop', function () {
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
    })
</script>
<div id="editBackground">

    <div id="edit">
        <div id="wait_dialog">
            <p style="color: white;font-size: 20px;margin-top: 18px;"></p>
        </div>
        <input type="text" id="english"/>
        <input type="text" id="russian"/>
        <input type="text" id="armenian"/>
        <ul id="edit_buttons_list">
            <li>
                <button id="remove"><img src="/images/trash.png"/></button>
            </li>
            <li>
                <button id="save"><img src="/images/save.png"/></button>
            </li>
            <li>
                <button id="closeEditPopup"><img src="/images/close.png"/></button>
            </li>
        </ul>

    </div>
</div>
<div id="toggle_container">
    <label for="flip-mini">Translates</label>
    <select name="flip-mini" id="flip-mini" data-role="slider" data-mini="true">
        <option value="on">On</option>
        <option value="off">Off</option>
    </select>
</div>
<div style="overflow-x: scroll">
    <table id="words" class="tablesorter">
        <thead>
        <tr style="background-color: #000000;color: whitesmoke;height: 35px;">
            <th>English</th>
            <th>Russian</th>
            <th>Armenian</th>

        </tr>
        </thead>
        <?php foreach ($words as $key => $value) { ?>
            <tr id="<?php echo $value['id'] ?>">
                <td class="english"><?php echo $value['word'] ?></td>
                <td class="russian"><?php echo $value['in_russian'] ?></td>
                <td class="armenian"><?php echo $value['in_armenian'] ?></td>

            </tr>
        <?php } ?>

    </table>
</div>