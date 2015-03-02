<script type="text/javascript">
    sessionStorage.clear(); // sessionStorage.removeItem('');
    $.removeCookie('parametros');
</script>
<?php
    Yii::$app->getResponse()->redirect(Yii::$app->getHomeUrl());
?>