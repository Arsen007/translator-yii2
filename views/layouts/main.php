<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <link rel="shortcut icon" href="/images/favicon.png" />
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>

<?php $this->beginBody() ?>
    <div class="wrap">
        <?php
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => [
                    ['label' => '', 'url' => ['/site/index'],'linkOptions' => ['style' => 'background-image:url(/images/home-icon.png)']],
                    Yii::$app->user->isGuest ? ['label' => '','url' => ['/site/signup'],'linkOptions' => ['style' => 'background-image:url(/images/register.png)']]:'',
                    !Yii::$app->user->isGuest ? ['label' => '', 'url' => ['/translate/add-word'],'linkOptions' => ['style' => 'background-image:url(/images/add-word.png)']]:'',
                    !Yii::$app->user->isGuest ? ['label' => '', 'url' => ['/translate/words'],'linkOptions' => ['style' => 'background-image:url(/images/word-list.png)']]:'',
                    Yii::$app->user->isGuest ? ['label' => '', 'url' => ['/site/login'],'linkOptions' => ['style' => 'background-image:url(/images/login.png)']] : ['label' => '','url' => ['/site/logout'],'linkOptions' => ['data-method' => 'post','style' => 'background-image:url(/images/logout.png)']],
                ],
            ]);
        ?>

        <div class="container">
                <?= $content ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p class="pull-left">&copy; Arsen Sargsyan<?= date('Y') ?></p>
            <p class="pull-right"><?= Html::a('arsen-sargsyan.info','http://arsen-sargsyan.info') ?></p>
        </div>
    </footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
