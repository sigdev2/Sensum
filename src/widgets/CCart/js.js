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
    // sending order cptcha form
    var sending_order_email = $("#sending_order_email"),
        sending_order_fio = $("#sending_order_fio"),
        sending_order_phone = $("#sending_order_phone"),
        sending_order_mess_addr = $("#sending_order_mess"),
        minOrder_EmailLen = 6,
        maxOrder_EmailLen = 80,
        minOrder_FioLen = 3,
        maxOrder_FioLen = 100;
        minOrder_PhoneLen = 2,
        maxOrder_PhoneLen = 50;
        minOrder_AddrLen = 11,
        maxOrder_AddrLen = 255;
    
    // send order cptcha
    var sending_order_captcha_text = $("#sending_order_captcha_text"),
        sending_order_captcha = $("#sending_order_captcha"),
        sending_order_captcha_id = "sending_order_captcha",
        sending_order_captcha_try_count = 0,
        sending_order_captcha_len = 5;
    
    // sending order dialog elements
    var	allSendingOrderFields = $([]).add(sending_order_email).add(sending_order_fio).add(sending_order_phone).add(sending_order_mess_addr).add(sending_order_captcha_text);
    
    // ajax uri
    var captchauri = "/captcha",
        adminmail = "/adminmail";
    
    // buttons vars
    var send_products = $("#send_products");
    
    // dialog vars
    var dialog_sending_order_success = $("#dialog_sending_order_success"),
        dialog_sending_order_success_without_email = $("#dialog_sending_order_success_without_email"),
        dialog_sending_order = $("#dialog_sending_order");

    // user box's
    var sending_order_validate_tips_box = $("#sending_order_validate_tips_box"),
        cart_products_box = $("#cart_products_box");
    
    // functions
    function updateOrderTips(t) {
        sending_order_validate_tips_box
            .text(t)
            .addClass('ui-state-error');
        setTimeout(function() {
            sending_order_validate_tips_box.removeClass('ui-state-error', 1500);
        }, 500);
    }
    
    function setError(o) {

        o.addClass('ui-state-error');
        setTimeout(function() {
            o.removeClass('ui-state-error', 1500);
        }, 2000);

    }

    function checkLength(o,n,min,max) {

        if ( o.val().length > max || o.val().length < min ) {
            setError(o);
            if (n != false)
                updateOrderTips("Длинна " + n + " должна быть между "+min+" и "+max+" символов.");
            return false;
        } else {
            return true;
        }

    }
    
    function checkCapLen(o,len, tip)
    {
        if ( o.val().length != len ) {
            setError(o);
            if (tip)
                updateOrderTips("Код картинки должен состоять из "+len+" символов.");
            return false;
        } else {
            return true;
        }
    }

    function checkRegexp(o,regexp,n) {

        if ( !( regexp.test( o.val() ) ) ) {
            setError(o);
            if (n != false)
                updateOrderTips(n);
            return false;
        } else {
            return true;
        }

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
    
    // accordion
    cart_products_box.hide();

    $("#cart_header_box").click(function(){
        cart_products_box.slideToggle("slow");
     });
    
    // dialogs
    dialog_sending_order.dialog({
        autoOpen: false,
        height: 585,
        width: 545,
        modal: true,
        //resizable: false,
        closeOnEscape: true,
        buttons: {
            'Отправить': function() {
                $(this).button("disable");
                var valid = true;
                valid = valid && checkEmail(sending_order_email, "email", minOrder_EmailLen,maxOrder_EmailLen);
                valid = valid && checkLength(sending_order_fio, "ФИО", minOrder_FioLen,maxOrder_FioLen);
                valid = valid && checkLength(sending_order_phone, "номер телефона", minOrder_PhoneLen,maxOrder_PhoneLen);
                valid = valid && checkLength(sending_order_mess_addr, "адрес", minOrder_AddrLen,maxOrder_AddrLen);
                valid = valid && checkCapLen(sending_order_captcha_text, sending_order_captcha_len, true);
                
                if (valid) {
                    updateOrderTips("Проверка каптчи...");
                    
                    jQuery.post(captchauri, {'id': sending_order_captcha_id, 'captcha' : md5(sending_order_captcha_text.val()), 'act': "valid"}, function(right) {
                        if ($("true", right).length != 0)
                        {
                            var order = "Интернет магазин.\n\n";
                            order += "Данное письмо сформировано почтовым сервисом интернет магазина.\n\n";
                            order += "К Вам поступил заказ от "+sending_order_fio.val()+".\n";
                            order += "Телефон: "+sending_order_phone.val()+";\n";
                            order += "Адрес: "+sending_order_mess_addr.val()+";\n\n";
                            order += "Список заказанных пользователем товаров:\n"
                            
                            var counter = 1;
                            var me = simpleCart;
                            
                            var productsList = "";
                            
                            if (me.quantity > 0)
                            {
                                for( var current in me.items )
                                {
                                    var item = me.items[current];
                                    var product = counter+". ";
                                    for ( var property in item )
                                    {
                                        switch (property)
                                        {
                                            case "name" :
                                            {
                                                var reg = /^<a href="(.+)"\>(.+)<\/a>$/i;
                                                var nameTag = reg.exec(item.name);
                                                
                                                product += "Наименование: " + nameTag[2] + " ; Ссылка: http://" + gDomain + nameTag[1] + " ; "; break;
                                            }
                                            case "price" : product += "Цена: " + item.price + me.currencySymbol() + " ; "; break;
                                            case "size" : product += "Размер: " + item.size + "; "; break;
                                            case "quantity" : product += "Количество: " + Number(item.quantity) + "шт. ; "; break;
                                            case "total" : product += "Стоймость: " + item.total + me.currencySymbol() + " ; "; break;
                                        }
                                    }
                                    
                                    product += "\n";
                                    productsList += product;
                                    
                                    counter++;
                                }
                                
                                order += productsList+"\n";
                                order += "Всего на сумму "+me.finalTotal+me.currencySymbol()+"\n\n";
                                order += "Обратный адрес: "+sending_order_email.val()+"\n\n";
                                order += "----------------\n http://" + gDomain;
                                
                                updateOrderTips("Оформление заказа ...");

                                jQuery.post(adminmail, {'id': sending_order_captcha_id, 'captcha' : md5(sending_order_captcha_text.val()),
                                                        "email" : sending_order_email.val(),
                                                        "text" : order}, function(success) {
                                    
                                    if ($("true", success).length != 0)
                                    {
                                        updateOrderTips("Отправка уведомления...");
                                        
                                        var forUser = "Уведомление о заказе.\n\n";
                                        forUser += "Данное письмо сформировано почтовым сервисом интернет магазина.\n\n";
                                        forUser += "Вы сделали заказ на следующие товары:\n";
                                        forUser += productsList + "\n";
                                        forUser += "Всего на сумму " + me.finalTotal+me.currencySymbol() + "\n\n";
                                        forUser += "----------------\n http://" + gDomain;
                                        
                                        jQuery.post(adminmail, {'id': sending_order_captcha_id, 'captcha' : md5(sending_order_captcha_text.val()),
                                                        "to" : sending_order_email.val(),
                                                        "text" : forUser}, function(success) {
                                                        
                                            if ($("true", success).length != 0)
                                            {
                                                updateOrderTips("Готово");
                                                simpleCart.empty();
                                                dialog_sending_order.dialog('close');
                                                dialog_sending_order_success.dialog('open');
                                            }
                                            else if ($("error", success).length != 0)
                                            {
                                                updateOrderTips($("error", success).text());
                                            }
                                            else
                                            {
                                                updateOrderTips("Готово");
                                                simpleCart.empty();
                                                dialog_sending_order.dialog('close');
                                                dialog_sending_order_success_without_email.dialog('open');
                                            }
                                            
                                            jQuery.post(captchauri, {'id': sending_order_captcha_id, "act":"unset"}, function(data) {});
                                            updateCaptcha(sending_order_captcha, sending_order_captcha_id);
                                        });
                                    }
                                    else if ($("error", success).length != 0)
                                    {
                                        updateOrderTips($("error", success).text());
                                    }
                                    else
                                    {
                                        updateOrderTips("Не удалось отправить заказ.");
                                    }
                                    
                                    $(this).button("enable");
                                });
                            }
                            else
                            {
                                updateOrderTips("Вы ничего не заказали.");
                                $(this).button("enable");
                            }
                        }
                        else
                        {
                            $(this).button("enable");
                            sending_order_captcha_try_count += 1;
                            setError(sending_order_captcha_text);
                            updateOrderTips("Неверно введена каптча. Попытка №" + sending_order_captcha_try_count);
                            if (((sending_order_captcha_try_count + 1) % 5) == 0)
                                  updateCaptcha(sending_order_captcha, sending_order_captcha_id);
                        }
                    });
                }
                else
                {
                    $(this).button("enable");
                }
            },
            'Отмена': function() {
                dialog_sending_order.dialog('close');
            }
        },
        close: function() {
            allSendingOrderFields.val('').removeClass('ui-state-error');
            send_products.button("enable");
        },
        open: function() {
            sending_order_captcha_try_count = 0;
            updateOrderTips("Заполните форму и нажмите отправить.");
        }
    });
    
    dialog_sending_order_success.dialog({
        autoOpen: false,
        closeOnEscape: true,
        width: 450,
        modal: true,
        buttons: { 'Ok': function() {$(this).dialog('close');}}
        });
        
    dialog_sending_order_success_without_email.dialog({
        autoOpen: false,
        closeOnEscape: true,
        width: 450,
        modal: true,
        buttons: { 'Ok': function() {$(this).dialog('close');}}
        });
    
    // buttons
    send_products
        .button()
        .click(function() {
            dialog_sending_order.dialog('open');
            $(this).button("disable");
        });
    
    $('#update_sending_order_captcha_by_user')
        .button()
        .click(function() { 
            $(this).button("disable");
            updateCaptcha(sending_order_captcha, sending_order_captcha_id);
            $(this).button("enable");
            });
    
    $("#clear_cart").button();
});