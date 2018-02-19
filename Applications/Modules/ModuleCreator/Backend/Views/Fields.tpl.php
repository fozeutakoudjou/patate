<div class="table">
    <img src="../Themes/backend/backend_images/bg-th-left.gif" width="8" height="7" alt="" class="left" />
    <img src="../Themes/backend/backend_images/bg-th-right.gif" width="7" height="7" alt="" class="right" />

    <table class="listing" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th class="first">Champs</th>
                <th width="120">Type</th>
                <th class="last">Requis</th>
                <th class="last">Visible</th>
                <th width="150">valeur Prise</th>
                <th class="last">Valeur Affichée</th>
            </tr>
        </thead>
        <tbody>
    <?php foreach($fields as $field):  ?>
        <tr>
            <td ><?php echo $field->COLUMN_NAME; ?></td>
            <td class="first style1">
                <select name="type_<?php echo  $field->COLUMN_NAME; ?>" >
                        <?php
                            if(isset($refFields[$field->COLUMN_NAME]))
                                echo '<option value="Select">Select</option>
                                    <option value="Radio">Radio</option>
                                    <option value="Checkbox">Checkbox</option>';
                            else 
                                echo '<option value="Text">Text</option>
                                      <option value="Textarea">Textarea</option>
                                      <option value="File">File</option>
                                      <option value="Password">Password</option>
                                      <option value="CheckBox">CheckBox</option>
                                      <option value="Radio">Radio</option>
                                      <option value="Select">Select</option>
                                      <option value="Email">Email</option>';
                            
                     ?>
                </select></td>
            
            <td class="first">
                <select name="requis_<?php echo  $field->COLUMN_NAME; ?>">
                    <option value="true">Oui</option>
                    <option value="false">Non</option>
                </select>
            </td>
            <td class="first">
                <select name="visible_<?php echo  $field->COLUMN_NAME; ?>">
                    <option value="1">Oui</option>
                    <option value="0">Non</option>
                </select>
            </td>
            <td>
                 <?php
                    if(isset($refFields[$field->COLUMN_NAME]))
                        echo $refFields[$field->COLUMN_NAME]['ref_column'].' ('.$refFields[$field->COLUMN_NAME]['ref_table'].')';
                 ?>
            </td>
            <td>
                <?php
                    if(isset($refFields[$field->COLUMN_NAME])){
                       
                 ?>
                        <select name="view_<?php echo  $field->COLUMN_NAME; ?>">
                            <?php  
                                foreach ($refFields[$field->COLUMN_NAME]['ref_fields'] as $value){
                            ?>
                                    <option value="<?php echo $value->COLUMN_NAME ?>"><?php echo $value->COLUMN_NAME; ?></option>
                            <?php } ?>
                        </select>
                    <?php } ?>
            </td>
            </tr>
    <?php endforeach;?>
        </tbody>
    </table>
</div>


