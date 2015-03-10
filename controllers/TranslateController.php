<?php
/**
 * Created by PhpStorm.
 * User: Arsen Sargsyan
 * Date: 2015-03-09
 * Time: 1:50 PM
 */

namespace app\controllers;


use yii\web\Controller;
use Yii;
use app\models\Words;

class TranslateController extends Controller
{
    /**
     * CURL call method
     */
    private function curl($url, $params = array(), $is_coockie_set = false)
    {

        if (!$is_coockie_set) {
            /* STEP 1. let’s create a cookie file */
            $ckfile = tempnam("/tmp", "CURLCOOKIE");

            /* STEP 2. visit the homepage to set the cookie properly */
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_COOKIEJAR, $ckfile);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $output = curl_exec($ch);
        }

        $str = '';
        $str_arr = array();
        foreach ($params as $key => $value) {
            $str_arr[] = urlencode($key) . "=" . urlencode($value);
        }
        if (!empty($str_arr))
            $str = '?' . implode('&', $str_arr);

        /* STEP 3. visit cookiepage.php */

        $Url = $url . $str;

        $ch = curl_init($Url);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $ckfile);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $output = curl_exec($ch);
        return $output;
    }

    public function actionTranslate()
    {
        $translatedWords = array();
        if (Yii::$app->request->getQueryParam('word')) {
            $word = Yii::$app->request->getQueryParam('word');
            $languages = [
                [
                    'lang' => 'ru',
                    'regular' => '/[а-я]|[А-Я]/'
                ],
                [
                    'lang' => 'hy',
                    'regular' => '/[ա-ֆ]|[Ա-Ֆ]/'
                ]
            ];
            $word = urlencode($word);
            foreach ($languages as $key => $value) {

                $url = 'http://translate.google.com/translate_a/t?client=t&text=' . $word . '&hl=en&sl=en&tl=' . $languages[$key]['lang'] . '&oe=UTF-8&multires=1&otf=2&pc=1&ssel=0&tsel=0&sc=1';
                $name_en = $this->curl($url);
                $cleanWords = [];
                $all_words_array = explode(',', str_replace(',,', ',', preg_replace('/\[|\]|[\"\"]/', '', $name_en)));
                foreach ($all_words_array as $key2 => $value2) {
                    if (preg_match($languages[$key]['regular'], $value2) == 1) {
                        $cleanWords[] = $value2;
                    }
                }
                $translatedWords[$languages[$key]['lang']] = array_unique($cleanWords);
            }
            echo json_encode($translatedWords);
        } else {
            echo 'false';
        }

    }

    public function actionAutocomplete()
    {
        $word = Yii::$app->request->getQueryParam('word') ? Yii::$app->request->getQueryParam('word') : '';
        if($word != ''){
            $url = "https://www.lpology.com/code/spellcheck/spell-pub.php";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$url); // set url to post to
            curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
            curl_setopt($ch, CURLOPT_REFERER, 'https://www.lpology.com/code/spellcheck/');
            curl_setopt($ch, CURLOPT_FAILONERROR, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects
            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable
            curl_setopt($ch, CURLOPT_TIMEOUT, 3); // times out after 4s
            curl_setopt($ch, CURLOPT_POST, 1); // set POST method
            curl_setopt($ch, CURLOPT_POSTFIELDS, "text='.$word.'&X-Requested-With=XMLHttpRequest"); // add POST fields
            curl_setopt($ch, CURLOPT_SSLVERSION,3);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $result = curl_exec($ch); // run the whole process
            curl_close($ch);
            $results = array();
            $decoded = json_decode($result);
            if($decoded ->errors == 1){
                $results = $decoded->words->$word;
            }else{
//                $results['fetched'] = false;
            }
        }else{
//            $results['fetched'] = false;
        }

        echo json_encode($results);
    }


    public function actionAddWord()
    {
        if (Yii::$app->request->post('addAjax')) {
            $model = new Words();
            $model->attributes = [
                'english' => $_POST['english'],
                'date' => date("Y-m-d H:i:s"),
                'priority' => 1,
                'userID' => Yii::$app->user->id,
                'russian' => $_POST['russian'],
                'armenian' => $_POST['armenian'],
            ];
            $words = $model->addWord();
        }
        return $this->render('add', ['words' => 44]);
    }


    public function actionUpdateWord()
    {
        $model = new Words();
        $model ->setAttributes(Yii::$app->request->getQueryParams());
        if($model->validate()){
            $words = $model ->updateWord();
        }
    }


    public function actionDeleteWord()
    {
        if(Yii::$app->request->getQueryParam('deleteAjax') && Yii::$app->request->getQueryParam('wordID')){
            $model = new Words();
            $model ->attributes = array(
                'wordID' => $_REQUEST['wordID']
            );
            $words = $model ->deleteWord();
            echo json_encode($words);
        }
    }


    public function actionWords()
    {
        $model = new Words();
        $words = $model ->getUserWords();
        return $this->render('words',['words'=>$words]);
    }


    public function actionGetWord()
    {
        $model = new Words();
        $wordID = $_POST['id'];
        $model ->attributes = array(
                'wordID' => $wordID,
            );

        $wordResult = $model ->getWord();
        echo json_encode($wordResult);
    }

}