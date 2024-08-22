<?php

namespace hm\daxiongratelimiter;

use yii\filters\RateLimitInterface;
use Yii;

class DaxiongRateLimiter implements RateLimitInterface
{
    public $rateLimit=100; // requests
    public $timePeriod = 30; // seconds

    private $identifier;

    public function __construct()
    {
        // Use a unique identifier, such as an IP address or API key
        $this->identifier = Yii::$app->request->userIP;
    }

    public function getRateLimit($request, $action)
    {
        return [$this->rateLimit, $this->timePeriod];
    }

    public function loadAllowance($request, $action)
    {
        $record = (new \yii\db\Query())
            ->select(['allowance', 'last_check'])
            ->from('rate_limit')
            ->where(['identifier' => $this->identifier])
            ->one();

        if ($record === false) {
            return [$this->rateLimit, time()];
        }

        $allowance = $record['allowance'];
        $lastCheck = $record['last_check'];
        Yii::$app->session->set('last_check', $lastCheck); //use in view
        $currentTimestamp = time();

        // Calculate elapsed time
        $elapsedTime = $currentTimestamp - $lastCheck;

        if ($elapsedTime > $this->timePeriod) {
            // Reset allowance if the time period has passed
            $allowance = $this->rateLimit;
        } else {
            if ($allowance == 0) {
                Yii::$app->controller->layout = 'blank';
                echo Yii::$app->controller->render('/site/rate_limiter');
                exit();
            }
        }

        return [$allowance, $lastCheck];
    }

    public function saveAllowance($request, $action, $allowance, $timestamp)
    {
        $exists = (new \yii\db\Query())
            ->select(['id'])
            ->from('rate_limit')
            ->where(['identifier' => $this->identifier])
            ->exists();

        if ($exists) {
            Yii::$app->db->createCommand()
                ->update('rate_limit', [
                    'allowance' => $allowance,
                    'last_check' => $timestamp,
                ], ['identifier' => $this->identifier])
                ->execute();
        } else {
            Yii::$app->db->createCommand()
                ->insert('rate_limit', [
                    'identifier' => $this->identifier,
                    'allowance' => $allowance,
                    'last_check' => $timestamp,
                ])
                ->execute();
        }
    }
}
