let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Web Url : https://laravel-mix.com
 |--------------------------------------------------------------------------
 */
mix
    .browserSync({
        // 这里替换地址
        proxy : 'http://weiran-v1.duoli.com/',
        files : [
            "public/assets/**/*.js",
            "public/assets/**/*.css",
            "modules/**/src/request/**/*.php",
            "modules/**/resources/views/**/*.blade.php",
            "modules/**/resources/js/**/*.js"
        ]
    })
    .options({
        processCssUrls : false
    })
    .disableNotifications()
    /* 开发使用[便于文件加载]
     * ---------------------------------------- */
    // develop
    .less(
        'weiran/mgr-page/resources/less/mgr-page.less',
        'public/assets/libs/boot/style.css'
    )
    .scripts([
            'weiran/mgr-page/resources/libs/weiran/util.js',
            'weiran/mgr-page/resources/libs/weiran/cp.js',
            'weiran/mgr-page/resources/libs/weiran/mgr-page/cp.js'
        ],
        'public/assets/libs/boot/weiran.mgr.min.js'
    )
    .scripts([
            'weiran/mgr-page/resources/libs/boot/wangeditor@5.1.js',
        ],
        'public/assets/libs/boot/wangeditor@5.1.js'
    )
    .scripts([
            'weiran/mgr-page/resources/libs/jquery/2.2.4/jquery.min.js',
            'weiran/mgr-page/resources/libs/jquery/form/jquery.form.js',
            'weiran/mgr-page/resources/libs/jquery/pjax/jquery.pjax.js',
            'weiran/mgr-page/resources/libs/jquery/poshytip/jquery.poshytip.js',
            'weiran/mgr-page/resources/libs/jquery/validation/jquery.validation.js',
            'weiran/mgr-page/resources/libs/jquery/drag-arrange/drag-arrange.js',
            'weiran/mgr-page/resources/libs/tom-select/tom-select.complete.min.js',
            'weiran/mgr-page/resources/libs/clipboard/clipboard.min.js'
        ],
        'public/assets/libs/boot/vendor.min.js'
    )
    .copyDirectory('weiran/mgr-page/resources/font/', 'public/assets/font/')
    .copyDirectory('weiran/mgr-page/resources/images/', 'public/assets/images/')
    .copyDirectory('weiran/mgr-page/resources/libs/jquery/', 'public/assets/libs/jquery/')
    .copyDirectory('weiran/mgr-page/resources/libs/easy-web/', 'public/assets/libs/easy-web')
    .copyDirectory('weiran/mgr-page/resources/libs/layui/', 'public/assets/libs/layui')
    .copyDirectory('weiran/mgr-page/resources/libs/vue/', 'public/assets/libs/vue')
    .copyDirectory('weiran/mgr-page/resources/libs/underscore/', 'public/assets/libs/underscore');