<div id="editBackground" data-dom-cache="false">

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
                <button id="update"><img src="/images/save.png"/></button>
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
    <div>
        <p>Yout have <b><?php echo count($words)?></b> words</p>
    </div>
    <table id="words" class="tablesorter">
        <thead>
        <tr style="background-color: #000000;color: whitesmoke;height: 35px;">
            <th>English</th>
            <th>Russian</th>
            <th>Armenian</th>

        </tr>
        </thead>
        <tbody>
        <?php foreach ($words as $key => $value) { ?>
            <tr id="<?php echo $value['id'] ?>">
                <td class="english"><?php echo $value['word'] ?></td>
                <td class="russian"><?php echo $value['in_russian'] ?></td>
                <td class="armenian"><?php echo $value['in_armenian'] ?></td>

            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>