<?php

namespace MyApp\Component;

use Phalcon\Di\Injectable;
use Phalcon\Translate\Adapter\NativeArray;
use Phalcon\Translate\InterpolatorFactory;
use Phalcon\Translate\TranslateFactory;

session_start();

class Locale extends Injectable
{
    /**
     * @return NativeArray
     */
    public function getTranslator(): NativeArray
    {
        $di = $this->getDI();
        // Ask browser what is the best language
        if (!$_SESSION['lang']) {
            $_SESSION['lang'] = 'en';
        }
        $language = $_SESSION['lang'];
        $messages = [];

        $translationFile = APP_PATH . '/messages/' . $language . '.php';

        if ($di->get('cache')->has('my-Key') && $di->get('cache')->get('la/lang') == $language) {
            $messages = $di->get('cache')->get('my/-K/my-Key');
            $interpolator = new InterpolatorFactory();
            $factory      = new TranslateFactory($interpolator);
            return $factory->newInstance(
                'array',
                [
                    'content' => $messages,
                ]
            );
        } else {
            if (true !== file_exists($translationFile)) {
                $translationFile = APP_PATH . '/messages/' . $language . '.php';
            }
            
            include $translationFile;
            
            $interpolator = new InterpolatorFactory();
            $factory      = new TranslateFactory($interpolator);

            $di->get('cache')->set('my-Key', $messages);
            $di->get('cache')->set('lang', $language);

            return $factory->newInstance(
                'array',
                [
                    'content' => $messages,
                ]
            );
        }

        }
}
