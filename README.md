# Hm Rate Limiter for Yii2

This package provides a custom rate limiter implementation for Yii2 applications.

## 1.Installation

You can install this package via Composer:

```bash
composer require hm/daxiong-rate-limiter:dev-main
```

## 2. Config add this code in config>main.php  inside components

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