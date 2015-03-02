<?php
use yii\helpers\Url;

$this->registerJs('
        sessionStorage.clear();
        sessionStorage.removeItem("fancytree-1-active");
        sessionStorage.removeItem("fancytree-1-expanded");
        sessionStorage.removeItem("fancytree-1-focus");

        var cookies = $.cookie();

        for(var cookie in cookies) {
            $.removeCookie(cookie);
        }

        $.removeCookie("parametros", { path: "/" });

        location.href = "'.Url::toRoute('site/index', true).'"
    ');
