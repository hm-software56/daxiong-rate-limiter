# Hm Rate Limiter for Yii2

This package provides a custom rate limiter implementation for Yii2 applications.

## 1.Installation

You can install this package via Composer:

```bash
composer require hm/daxiong-rate-limiter:dev-main
```

## 2. Config add this code in config>main.php  components

```bash
'hmrateLimiter' => [
	'class' => DaxiongRateLimiter::class,
	'rateLimit' => 500,    // optional Set a new rate limit (number of requests)
	'timePeriod' => 300,   //optional Set a new time period (in seconds)
],
```

## 3. Call use In controller function behaviors()

```bash
public function behaviors()
{
	return [
		'rateLimiter' => [
			'class' => \yii\filters\RateLimiter::class,
			'user' =>Yii::$app->hmrateLimiter,

		],
		'verbs' => [
			'class' => VerbFilter::class,
			'actions' => [
				'logout' => ['post'],
			],
		],
	];
} 
```
## 4. Create layout file name 'layout_rate_limiter.php' in views/layouts/ then copy the below code and paste inside

```bash
<?php

/** @var yii\web\View $this */
/** @var string $content */
use yii\helpers\Html;
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>" class="h-100">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <?php $this->registerCsrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body style="background:#BEBEBE">
    <?php $this->beginBody() ?>
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main role="main">
                <div class="container">
                    <?= $content ?>
                </div>
            </main>
        </div>
    </div>
    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage();

```

## 5. Create view file name 'rate_limiter.php' in views/site/ then copy the below code and paste inside
  
 ```bash
<style>
    * {
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
    }

    body {
        padding: 0;
        margin: 0;
    }

    #notfound {
        position: relative;
        height: 90vh;
    }

    #notfound .notfound {
        position: absolute;
        left: 50%;
        top: 50%;
        -webkit-transform: translate(-50%, -50%);
        -ms-transform: translate(-50%, -50%);
        transform: translate(-50%, -50%);
    }

    .notfound {
        max-width: 650px;
        width: 100%;
        padding-left: 160px;
        line-height: 1.1;
    }

    .notfound .notfound-404 {
        position: absolute;
        left: 0;
        top: 0;
        display: inline-block;
        width: 140px;
        height: 140px;
        background-image: url('https://raw.githubusercontent.com/hm-software56/daxiong-rate-limiter/main/block_ip.png');
        background-size: cover;
    }

    .notfound .notfound-404:before {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        -webkit-transform: scale(2.4);
        -ms-transform: scale(2.4);
        transform: scale(2.4);
        border-radius: 50%;
        background-color: #f2f5f8;
        z-index: -1;
    }

    .notfound h1 {
        font-family: 'Nunito', sans-serif,'Phetsarath OT';
        font-size: 65px;
        font-weight: 700;
        margin-top: 0px;
        margin-bottom: 10px;
        color: #ff0000;
        text-transform: uppercase;
    }

    .notfound h2 {
        font-family: 'Nunito', sans-serif,'Phetsarath OT';
        font-size: 21px;
        font-weight: 400;
        margin: 0;
        text-transform: uppercase;
        color: #151723;
    }

    .notfound p {
        font-family: 'Nunito', sans-serif,'Phetsarath OT';
        color: black;
        font-weight: 400;
    }

    .notfound a {
        font-family: 'Nunito', sans-serif,'Phetsarath OT';
        display: inline-block;
        font-weight: 700;
        border-radius: 40px;
        text-decoration: none;
        color: #388dbc;
    }
</style>
<?php
$time = Yii::$app->hmrateLimiter->timePeriod;
$time_last = time() - Yii::$app->session->get('last_check');
$time = $time - $time_last;
$smh = '';
if ($time < 60) {
    $smh = $time . ' ' . Yii::t('app', "seconds");
} elseif ($time < 3600) { // Less than 1 hour
    $minutes = floor($time / 60);
    $seconds = $time % 60;
    //$smh = $minutes . " minutes and " . $seconds . " seconds";
    $smh = $minutes . ' ' . Yii::t('app', "minutes");
} else {
    $hours = floor($time / 3600);
    /*$minutes = floor(($time % 3600) / 60);
    $seconds = $time % 60;
    $smh = $hours . " hours, " . $minutes . " minutes and " . $seconds . " seconds";*/
    $smh = $hours . ' ' . Yii::t('app', "hours");
}
?>
<div id="notfound">
    <div class="notfound">
        <div class="notfound-404"></div>
        <div style="background: #f2f5f8" class="p-2 rounded">
            <h1>403</h1>
            <h2><?= Yii::t('app', 'Your network connection insecure') ?></h2>
            <p class="pt-2"><?= Yii::t('app', 'We have denied your connection to our system.!, Please wait') . ' ' . $smh . ', ' . Yii::t('app', 'or contact administrator') ?> </p>
        </div>
    </div>
</div>
```