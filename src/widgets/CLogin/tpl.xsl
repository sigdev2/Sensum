<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template name="clogin">
        <xsl:if test="users">
            <div id="authorization">
                <script type="text/javascript">
                    var url_sep = '<xsl:value-of select="/root/siteInfo/url_sep" />';
                    var hasUser = false;<xsl:if test="/root/users/user/email">
                    hasUser = true;
                    var userEmail = '<xsl:value-of select="/root/users/user/email" />';</xsl:if>
                    var userRole = '<xsl:value-of select="/root/users/user/role" />';
                    var userNikname = '<xsl:value-of select="/root/users/user/nikname" />';
                </script>
                <style type="text/css">
                    .ui-dialog .ui-state-error { padding: .3em; }
                    #register_validate_tips_box, #user_login_error_box { border: 1px solid transparent; padding: 0.3em; }
                </style>
                
                <script type="text/javascript" src="./js/md5.js"></script>
                <script type="text/javascript" src="./widgets/CLogin/js.js"></script>
                
                <div id="authorization_status_box">
                    <strong id="logined_user_email_box"></strong><br />
                    <xsl:text>Здравствуйте, </xsl:text><strong id="logined_user_nikname_box"></strong> (<strong id="logined_user_role_box"></strong>)!
                </div>
                
                <div id="dialog_register_form" title="Регистрация пользователя">
                    <div id="register_validate_tips_box"></div>

                    <form>
                        <fieldset>
                            <label for="register_nikname">Ф.И.О.</label>
                            <input type="text" name="register_nikname" id="register_nikname" value="" class="text ui-widget-content ui-corner-all" />
                            <label for="register_email">Email</label>
                            <input type="text" name="register_email" id="register_email" value="" class="text ui-widget-content ui-corner-all" />
                            <label for="register_password">Пароль</label>
                            <input type="password" name="register_password" id="register_password" value="" class="text ui-widget-content ui-corner-all" />
                            <label for="register_ppassword">Подтверждение пароля</label>
                            <input type="password" name="register_ppassword" id="register_ppassword" value="" class="text ui-widget-content ui-corner-all" />
                            
                            <label for="user_register_captcha">Введите символы</label>
                            <img src="" alt="User register captcha" id="user_register_captcha"/>
                            <a id="update_register_captcha_by_user">Обновить</a><br />
                            <input name="user_register_captcha_text" id="user_register_captcha_text" value="" type="text" class="text ui-widget-content ui-corner-all" />
                            <br />
                            
                        </fieldset>
                    </form>
                </div>
                
                <div id="dialog_register_success" title="Регистрация прошла успешно">
                </div>

                <div id="user_login_box">
                    <form>
                        <fieldset>
                            <legend>Авторизация</legend>
                            <label for="input_login_email">Email</label><input name="loginEmail" id="input_login_email" value="" type="text" class="text ui-widget-content ui-corner-all" />
                            <label for="input_login_pass">Пароль</label><input name="pass" id="input_login_pass" value="" type="password" class="text ui-widget-content ui-corner-all" />
                            
                            <div>
                                <div id="user_login_error_box"></div>
                                <div id="user_login_captcha_box">
                                    <label for="user_login_captcha">Введите символы</label>
                                    <img src="" alt="User login captcha" id="user_login_captcha"/>
                                    <a id="update_login_captcha_by_user">Обновить</a><br />
                                    <input name="user_login_captcha_text" id="user_login_captcha_text" value="" type="text" class="text ui-widget-content ui-corner-all" />
                                </div>
                            </div>
                            
                            <a id="login_authorization">Войти</a>
                            <a id="create_new_user">Регистрация</a>
                        </fieldset>
                    </form>
                </div>
                
                <div id="user_logout_box">
                    <form>
                        <fieldset>
                            <legend>Вы авторизированы!</legend>
                            <a id="logout_authorization">Выйти</a>
                        </fieldset>
                    </form>
                    <xsl:if test="online">
                        <div>
                            <b><xsl:value-of select="count"/></b>
                            <xsl:for-each select="online/user">
                                <div> {nikname} </div>
                            </xsl:for-each>
                        </div>
                    </xsl:if>
                </div>
            </div>
        </xsl:if>
    </xsl:template>
</xsl:stylesheet>