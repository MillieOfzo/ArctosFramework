<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-6">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><span data-i18n="[html]user.edit.title"> Edit user</span> <small></small></h5>
                </div>
                <div class="ibox-content">
                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label" for="first-name"><span data-i18n="[html]user.edit.input.1">User name</span><font color="red">*</font></label>
                                <input type="text" class="form-control" name="user_name" value="<?= $obj['response']['user_name'];?>" data-i18n="[placeholder]placeholders.input" >
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label" for="first-name"><span data-i18n="[html]user.edit.input.2">User last name</span><font color="red">*</font></label>
                                <input type="text" class="form-control" name="user_last_name" value="<?= $obj['response']['user_last_name'];?>" data-i18n="[placeholder]placeholders.input"   >
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label" for="first-name"><span data-i18n="[html]user.edit.input.3">User email</span><font color="red">*</font></label>
                                <input type="text" class="form-control" name="user_email" value="<?= $obj['response']['user_email'];?>" data-i18n="[placeholder]placeholders.input" >
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<?php
foreach($arr_js as $js){
    echo '<script src="'.$js.'"></script>';
}
?>
