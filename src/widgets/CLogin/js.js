/* UTF-8
   Copyright 2010-2018 SigDev

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License. */

$(function() {
    // register form
    var register_nikname = $("#register_nikname")
        register_email = $("#register_email"),
        register_password = $("#register_password"),
        register_ppassword = $("#register_ppassword"),
        register_minPassLen = 6,
        register_maxPassLen = 80,
        register_minNiknameLen = 3,
        register_maxNiknameLen = 50,
        register_minEmailLen = 6,
        register_maxEmailLen = 80;
    
    // login form
    var input_login_email = $("#input_login_email"),
        input_login_pass = $("#input_login_pass");
    
    // register cptcha
    var user_register_captcha_text = $("#user_register_captcha_text"),
        user_register_captcha = $("#user_register_captcha"),
        user_register_captcha_id = "user_register_captcha",
        user_register_captcha_try_count = 0,
        user_register_captcha_len = 5;
    
    // login cptcha
    var user_login_captcha_text = $("#user_login_captcha_text"),
        user_login_captcha = $("#user_login_captcha"),
        user_login_captcha_id = "user_login_captcha",
        user_login_captcha_try_count = 0,
        user_login_captcha_len = 5;
        user_filed_logins_captcha_show = false;
    
    // register dialog elements
    var	allRegisterDialogFields = $([]).add(register_nikname).add(register_email).add(register_password).add(register_ppassword).add(user_register_captcha_text);
    
    // login form elements
    var	allLoginFormFields = $([]).add(input_login_pass).add(input_login_email).add(user_login_captcha_text);
    
    // ajax uri
    var captchauri = "/captcha",
        usersuri = "/users";
    
    // buttons vars
    var create_new_user = $('#create_new_user'),
        login_authorization = $("#login_authorization");
        logout_authorization = $("#logout_authorization")
    
    // dialog vars
    var dialog_register_success = $("#dialog_register_success"),
        dialog_register_form = $("#dialog_register_form");

    // user box's
    var user_login_box = $("#user_login_box"),
        user_logout_box = $("#user_logout_box"),
        user_login_captcha_box = $("#user_login_captcha_box"),
        user_login_error_box = $("#user_login_error_box"),
        logined_user_role_box = $("#logined_user_role_box"),
        logined_user_nikname_box = $("#logined_user_nikname_box"),
        logined_user_email_box = $("#logined_user_email_box"),
        register_validate_tips_box = $("#register_validate_tips_box");

    // functions
    function updateTips(o, t) {
        o
        .text(t)
        .addClass('ui-state-error');
        setTimeout(function() {
            o.removeClass('ui-state-error', 1500);
        }, 500);
    }
    
    function setError(o) {

        o.addClass('ui-state-error');

    }

    function checkLength(o,n,min,max) {

        if ( o.val().length > max || o.val().length < min ) {
            setError(o);
            if (n != false)
                updateTips(register_validate_tips_box, "Length of " + n + " must be between "+min+" and "+max+".");
            return false;
        } else {
            return true;
        }

    }
    
    function checkCapLen(o,len, tip)
    {
        if ( o.val().length > len || o.val().length < len ) {
            setError(o);
            if (tip)
                updateTips(register_validate_tips_box, "Length of captcha be equal to "+len+".");
            return false;
        } else {
            return true;
        }
    }

    function checkRegexp(o,regexp,n) {

        if ( !( regexp.test( o.val() ) ) ) {
            setError(o);
            if (n != false)
                updateTips(register_validate_tips_box, n);
            return false;
        } else {
            return true;
        }

    }
    
    function checkPpass(o,o2,n) {

        if ( o.val() != o2.val() ) {
            setError(o2);
            if (n != false)
                updateTips(register_validate_tips_box, n);
            return false;
        } else {
            return true;
        }

    }
    
    function checkPassword(o, n, min, max)
    {
       if (checkLength(o, n, min, max))
       {
            if (n != false)
                n += " field only allow : a-z A-Z а-я А-Я 0-9";
            
            return checkRegexp(o, /^([a-zA-Zа-яА-Я0-9])+$/, n);
       }
       
       return false;
    }
    
    function checkEmail(o, n, min, max)
    {
        if (checkLength(o, n, min, max))
        {
            if (n != false)
                n = "Неверный почтовый ящик";

            // From jquery.validate.js (by joern), contributed by Scott Gonzalez: http://projects.scottsplayground.com/email_address_validation/
            return checkRegexp(o,/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, n);
        }
        
        return false;
    }
    
    function updateCaptcha(o, base_id) {
         o.attr("src", captchauri + url_sep + "id=" + base_id + "&t=" + escape(new Date()));
    }
    
    function logout()
    {
        user_logout_box.hide();
        user_login_captcha_box.hide();
        user_login_box.show();
        login_authorization.button("disable");
        
        $.post(usersuri, {"act": "failed_login_count"}, function(enable) {
            login_authorization.button("enable");
            user_filed_logins_captcha_show = ($("true", enable).length == 0);
            if (user_filed_logins_captcha_show)
            {
                updateCaptcha(user_login_captcha, user_login_captcha_id);
                user_login_captcha_box.show();
            }
        });
    }
    
    
    // Должен ли никнейм быть уникальным ?
    // предлагать варианты никнеймов ечли они заняты
    // dialogs
    dialog_register_form.dialog({
        autoOpen: false,
        height: 530,
        width: 450,
        modal: true,
        //resizable: false,
        closeOnEscape: true,
        buttons: {
            'Зарегистрироваться': function() {
                $(this).button("disable");
                var bValid = true;
                allRegisterDialogFields.removeClass('ui-state-error');

                bValid = bValid && checkEmail(register_email, "email", register_minEmailLen, register_maxEmailLen);
                bValid = bValid && checkLength(register_nikname, "nikname", register_minNiknameLen, register_maxNiknameLen);
                bValid = bValid && checkPassword(register_password, "password", register_minPassLen, register_maxPassLen);
                bValid = bValid && checkCapLen(user_register_captcha_text, user_register_captcha_text, true);
                bValid = bValid && checkPpass(register_password, register_ppassword, "Пароли несовпадают.");

                if (bValid) {
                    updateTips(register_validate_tips_box, "Идёт обработка...");
                    jQuery.post(usersuri, {'act': "exist", 'loginEmail': register_email.val()}, function(exist) {
                        if ($("noexist", exist).length == 0)
                        {
                            setError(register_email);
                            updateTips(register_validate_tips_box, 'Такой пользователь уже зарегистрирован.');
                            $(this).button("enable");
                        }
                        else
                        {
                            jQuery.post(captchauri, {'id': user_register_captcha_id, 'captcha' : md5(user_register_captcha_text.val()), 'act': "valid"}, function(right) {
                                if ($("true", right).length != 0)
                                {
                                    jQuery.post(usersuri, {'act': "reg", 'id': user_register_captcha_id, 'captcha' : md5(user_register_captcha_text.val()) , 'loginEmail': register_email.val(), 'pass' : register_password.val(), 'nikname' : register_nikname.val() }, function(success) {
                                        if ($("true", success).length != 0)
                                        {
                                            jQuery.post(captchauri, {'id': user_register_captcha_id, "act":"unset"}, function(data) {});
                                            dialog_register_form.dialog('close');
                                            dialog_register_success.dialog('open');
                                        }
                                        else
                                        {
                                            updateTips(register_validate_tips_box, "Регистрация неудалась.");
                                        }
                                        
                                        $(this).button("enable");
                                    });
                                }
                                else
                                {
                                    $(this).button("enable");
                                    user_register_captcha_try_count += 1;
                                    setError(user_register_captcha_text);
                                    updateTips(register_validate_tips_box, "Неверно введена каптча. Попытка №" + user_register_captcha_try_count);
                                    if (((user_register_captcha_try_count + 1) % 5) == 0)
                                          updateCaptcha(user_register_captcha, user_register_captcha_id);
                                }
                            });
                        }
                    });
                }
                else
                {
                    $(this).button("enable");
                }
            },
            'Отмена': function() {
                dialog_register_form.dialog('close');
            }
        },
        close: function() {
            create_new_user.button("enable");
            allRegisterDialogFields.val('').removeClass('ui-state-error');
        },
        open: function() {
            updateCaptcha(user_register_captcha, user_register_captcha_id);
            user_register_captcha_try_count = 0;
            updateTips(register_validate_tips_box, "Все поля обязательны для заполнения");
        }
    });
    
    dialog_register_success.dialog({
        autoOpen: false,
        closeOnEscape: true,
        resizable: false,
        height: 0,
        width: 450,
        modal: true,
        buttons: { 'Ok': function() {$(this).dialog('close');}}
        });
    
    // change events
    register_email.change(function()
    {
        if (checkEmail(register_email, false, register_minEmailLen, register_maxEmailLen))
        {
            register_validate_tips_box.text("Все поля обязательны для заполнения");
            register_email.removeClass('ui-state-error');
            jQuery.post(usersuri, {'act': "exist", 'loginEmail': register_email.val()}, function(exist) {
                if ($("noexist", exist).length == 0)
                    setError(register_email);
                else
                    register_email.removeClass('ui-state-error');
            });
        }
    });
    
    register_nikname.change(function()
    {
        checkLength(register_nikname, "nikname", register_minNiknameLen, register_maxNiknameLen);
    });
    
    register_password.change(function()
    {
        if (checkPassword(register_password, false, register_minPassLen, register_maxPassLen))
            register_password.removeClass('ui-state-error');
    });
    
    register_ppassword.change(function()
    {
        if (checkPpass(register_password, register_ppassword, false))
            register_ppassword.removeClass('ui-state-error');
    });
    
    // buttons
    create_new_user
        .button()
        .click(function() {
            dialog_register_form.dialog('open');
            $(this).button("disable");
        });
    
    $('#update_register_captcha_by_user')
        .button()
        .click(function() { 
            $(this).button("disable");
            updateCaptcha(user_register_captcha, user_register_captcha_id);
            $(this).button("enable");
            });
    
    $('#update_login_captcha_by_user')
        .button()
        .click(function() { 
            $(this).button("disable");
            updateCaptcha(user_login_captcha, user_login_captcha_id);
            $(this).button("enable");
            });

    logout_authorization
        .button()
        .click(function() {
            logout_authorization.button("disable");
            jQuery.post(usersuri, {'act': "logout"}, function(right)
            {
                if ($("true", right).length != 0)
                {
                    window.location.reload();
                    
                    logout();
                }
                else
                {
                    logout_authorization.button("enable");
                }
            });
        });

    login_authorization
        .button()
        .click(function() {
            login_authorization.button("disable");
            allLoginFormFields.removeClass('ui-state-error');
            if (user_filed_logins_captcha_show == false)
                jQuery.post(usersuri, {'act': "login", 'loginEmail': input_login_email.val(), 'pass' : input_login_pass.val()}, function(data) {
                    if ($("false", data).length == 0)
                    {
                        window.location.reload();
                        
                        logined_user_role_box.text($("role", data).text());
                        logined_user_email_box.text($("email", data).text());
                        logined_user_nikname_box.text($("nikname", data).text());
                        user_login_error_box.text("");
                        allLoginFormFields.val('');
                        login_authorization.button("enable");
                        user_logout_box.show();
                        user_login_box.hide();
                        logout_authorization.button("enable");
                    }
                    else
                    {
                        $.post(usersuri, {"act": "failed_login_count"}, function(enable) {
                            setError(input_login_email);
                            setError(input_login_pass);
                            login_authorization.button("enable");
                            user_filed_logins_captcha_show = ($("true", enable).length == 0);
                            if ($("true", enable).length == 0)
                            {
                                updateTips(user_login_error_box, "Превышен лимит неверных попыток входа. Введите каптчу:");
                                user_login_captcha_box.show();
                                updateCaptcha(user_login_captcha, user_login_captcha_id);
                            }
                            else
                            {
                                updateTips(user_login_error_box, "Неверные имя пользователя и пароль.");
                            }   
                        });
                    }
                });
            else
            {
                jQuery.post(captchauri, {'id': user_login_captcha_id, 'captcha' : md5(user_login_captcha_text.val()), 'act': "valid"}, function(right) {
                    if ($("true", right).length != 0)
                    {
                        jQuery.post(usersuri, {'act': "login", 'id': user_login_captcha_id, 'captcha' : md5(user_login_captcha_text.val()) , 'loginEmail': input_login_email.val(), 'pass' : input_login_pass.val()}, function(data) {
                            login_authorization.button("enable");
                            if ($("false", data).length == 0)
                            {
                                window.location.reload();
                                
                                jQuery.post(captchauri, {'id': user_login_captcha_id, "act":"unset"}, function(data) {});
                                logined_user_role_box.text($("role", data).text());
                                logined_user_email_box.text($("email", data).text());
                                logined_user_nikname_box.text($("nikname", data).text());
                                user_login_error_box.text("");
                                allLoginFormFields.val('');
                                user_filed_logins_captcha_show = false;
                                user_logout_box.show();
                                user_login_box.hide();
                                user_login_captcha_box.hide();
                                logout_authorization.button("enable");
                            }
                            else
                            {
                                setError(user_login_captcha_text);
                                setError(input_login_email);
                                setError(input_login_pass);
                                updateTips(user_login_error_box, "Неверные имя пользователя и пароль.");
                            }
                        });
                    }
                    else
                    {
                        login_authorization.button("enable");
                        user_login_captcha_try_count += 1;
                        setError(user_login_captcha_text);
                        updateTips(user_login_error_box, "Неверно введена каптча. Попытка №" + user_login_captcha_try_count);
                        if (((user_login_captcha_try_count + 1) % 5) == 0)
                            updateCaptcha(user_login_captcha, user_login_captcha_id);
                    }
                });
            }
                
            login_authorization.button("enable");
         });
     
    // hide box     
    logined_user_role_box.text(userRole);
    logined_user_nikname_box.text(userNikname);
    if (hasUser != false)
    {
        user_login_box.hide();
        logined_user_email_box.text(userEmail);
    }
    else
    {
        logout();
    }
});