/*NO Working */
/*jQuery(document).ready(function() {
    // カスタムコントロール内のチェックボックスが変更されたときに実行される処理
    jQuery('.customize-control-checkbox-multiple input[type="checkbox"]').on(
        'change',
        function() {
            // コンソールにメッセージを出力
            console.log('Checkbox value changed');

            // 変更されたチェックボックスの値を取得して、カンマ区切りの文字列に変換
            var checkbox_values = jQuery(this).parents('.customize-control').find('input[type="checkbox"]:checked').map(
                function() {
                    return this.value;
                }
            ).get().join(',');

            // 隠しフィールドにチェックボックスの値を設定し、変更イベントをトリガー
            jQuery(this).parents('#customize-control-dess_home_cats').find('input[type="hidden"]').val(checkbox_values).trigger('change');
        }
    );
});*/





jQuery( window ).load( function() {





    jQuery( '.customize-control-checkbox-multiple input[type="checkbox"]' ).on(


        'change',


        function() {


            console.log('clicked');


            checkbox_values = jQuery( this ).parents( '.customize-control' ).find( 'input[type="checkbox"]:checked' ).map(


                function() {


                    return this.value;


                }


            ).get().join( ',' );





            jQuery( this ).parents( '#customize-control-dess_home_cats' ).find( 'input[type="hidden"]' ).val( checkbox_values ).trigger( 'change' );


        }


    );





} );

// ロゴのサイズの調整
(function($) {
    $(document).ready(function() {
        // wp.customizeが存在するかを確認
        if (typeof wp !== 'undefined' && wp.customize) {
            // ロゴの幅を調整
            wp.customize('logo_width', function(value) {
                value.bind(function(newVal) {
                    $('.logo img').css('width', newVal + 'px');
                });
            });

            // ロゴの高さを調整
            wp.customize('logo_height', function(value) {
                value.bind(function(newVal) {
                    $('.logo img').css('height', newVal + 'px');
                });
            });
        }
    });
})(jQuery);


