<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template name="ccart">
        <script type="text/javascript" src="./widgets/CCart/simpleCart.js"></script>
        <script type="text/javascript" src="./widgets/CCart/js.js"></script>
        <script type="text/javascript">
            
            simpleCart.currency = RUR;
            //simpleCart.taxRate  = 0.08;
            simpleCart.taxRate  = 0.00;
        //	simpleCart.shippingFlatRate = 5.25;
        //    simpleCart.shippingQuantityRate = 1.00;
            simpleCart.shippingQuantityRate = 0.00;
        /*	CartItem.prototype.shipping = function(){
                if( this.size ){
                    switch( this.size.toLowerCase() ){
                        case 'small':
                            return this.quantity * 10.00;
                        case 'medium':
                            return this.quantity * 12.00;
                        case 'large':
                            return this.quantity * 15.00;
                        case 'bull':
                            return 45.00;
                        default:
                            return this.quantity * 5.00;
                    }
                }
            };
        */
            
            simpleCart.cartHeaders = ["Name_имя" , "Size_Размер", "Price_Цена" , "decrement_убавить" , "Quantity_количество", "increment_добавить", "remove_удалить", "total_Всего" ];
            
        </script>

        <div id="cart">
            <div id="dialog_sending_order" title="Оформление и отправка заказа">
                <div id="sending_order_validate_tips_box"></div>
                <form>
                    <label for="sending_order_fio">ФИО:</label>
                    <input id="sending_order_fio" type="text" name="fio" size="100" class="text ui-widget-content ui-corner-all"/>
                    <label for="sending_order_phone">Телефон:</label>
                    <input id="sending_order_phone" type="text" name="phone" size="50" class="text ui-widget-content ui-corner-all"/>
                    <label for="sending_order_mess">Адрес:</label>
                    <textarea id="sending_order_mess" rows="2" name="adres" cols="50" class="text ui-widget-content ui-corner-all"></textarea>
                    <label for="sending_order_email">Ваш e-mail:</label>
                    <input id="sending_order_email" type="text" name="email" size="80" class="text ui-widget-content ui-corner-all"/>
                </form>
                
                <label for="sending_order_captcha">Введите символы</label>
                <img src="/captcha?id=sending_order_captcha" alt="Sending_order captcha" id="sending_order_captcha"/>
                <a id="update_sending_order_captcha_by_user">Обновить</a><br />
                <input name="sending_order_captcha_text" id="sending_order_captcha_text" value="" type="text" class="text ui-widget-content ui-corner-all" />
                <br />
            </div>
            
            <div id="dialog_sending_order_success" title="Заказ успешно отправлен.">
            На указанный Вами E-Mail было отправлено уведомление со списком заказанных товаров. Корзина готова для новых покупок.
            </div>
            
            <div id="dialog_sending_order_success_without_email" title="Заказ успешно отправлен.">
            К сожалению, не удалось отправить уведомление на Ваш контактный E-Mail. Но заказ был успешно оформлен. Корзина готова для новых покупок.
            </div>

            <div id="cart_products_box">
                <table class="simpleCart_items" cellpadding="0" cellspacing="0">
                </table>
                <!--<hr />
                SubTotal: <span class="simpleCart_total"></span> <br />
                Tax: <span class="simpleCart_taxCost"></span> <br />
                Shipping: <span class="simpleCart_shippingCost"></span> <br />-->
                <hr />
                Всего : <span class="simpleCart_finalTotal"></span>
                <hr />
                <a id="send_products" class="">Заказать</a><a id="clear_cart" class="simpleCart_empty">Очистить корзину</a>
            </div>
            <div id="cart_header_box">
                Корзина: <span class="simpleCart_total"></span> (<span class="simpleCart_quantity"></span> товаров)
            </div>
        </div>
    </xsl:template>
</xsl:stylesheet>