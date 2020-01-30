<?php
if (!is_admin()) {
    die();
}
?><div class="wrap">
    <h2>کپچای من لاگین</h2>
    <form method="post" action="options.php">
        <?php
            echo settings_fields( 'login_pcaptcha' );
        ?>
        <p>
         برای ایجاد کپچای جدید <a href="https://manlogin.com/panel/#/developers/captcha" target="_blank">اینجا</a> کلیک کنید.
        </p>
        <table class="form-table form-v2">

            <tr valign="top">
                <th scope="row"><label for="id_login_pcaptcha_uid">شناسه یکتا: </span>
                    </label></th>
                <td><input type="text" id="id_login_pcaptcha_uid" name="login_pcaptcha_uid"
                        value="<?php echo get_option('login_pcaptcha_uid'); ?>" size="30" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="id_login_pcaptcha_key">کلید عمومی: </span>
                    </label></th>
                <td><input type="text" id="id_login_pcaptcha_key" name="login_pcaptcha_key"
                        value="<?php echo get_option('login_pcaptcha_key'); ?>" size="70" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="id_login_pcaptcha_secret">کلید خصوصی: </span>
                    </label></th>
                <td><input type="text" id="id_login_pcaptcha_secret" name="login_pcaptcha_secret"
                        value="<?php echo get_option('login_pcaptcha_secret'); ?>" size="70" /></td>
            </tr>
        </table>
        <p>
            <input type="submit" name="submit" id="submit" class="button button-primary"
                value="ذخیره تغییرات">
            <button name="reset" id="reset" class="button">
                حذف شناسه و کلید ها و غیر فعال کردن کپچا
            </button>
        </p>
    </form>
    <?php if(strlen(get_option('login_pcaptcha_key')) > 0 && strlen(get_option('login_pcaptcha_secret')) > 0): ?>
        <hr>
        <h3>نمونه</h3>
        <?php LoginPCaptcha::pcaptcha_form(); ?>
    <?php endif; ?>
</div>
<script>
    (function ($) {
        $('#reset').on('click', function (e) {
            e.preventDefault();
            $('#id_login_pcaptcha_key').val('');
            $('#id_login_pcaptcha_uid').val('');
            $('#id_login_pcaptcha_secret').val('');
            $('#id_login_pcaptcha_whitelist').val('');
            $('#submit').trigger('click');
        });
    })(jQuery);
</script>
<style>
    #submit+#reset {
        margin-left: 1em;
    }
</style>