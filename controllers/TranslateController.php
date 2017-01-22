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
use Stichoza\GoogleTranslate\TranslateClient;
use Stichoza\GoogleTranslate\Tokens\GoogleTokenGenerator;

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
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36');
//        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $output = curl_exec($ch);
        return $output;
    }

    public function actionTranslate()
    {
//        $tr = new TranslateClient(); // Default is from 'auto' to 'en'
//        $tr->setSource('en'); // Translate from English
//        $tr->setTarget('hy'); // Translate to Georgian
//        echo '<pre>';print_r($tr->translate('Goodbye'));die;
        $translatedWords = array();
        if (Yii::$app->request->post('word')) {
            $word = Yii::$app->request->post('word');
            $languages = [
                [
                    'lang' => 'ru',
                    'regular' => '[а-я]{2,50}'
                ],
                [
                    'lang' => 'hy',
                    'regular' => '[ա-ֆԱ-Ֆ]{4,30}+'
                ]
            ];
            $word = urlencode($word);
            foreach ($languages as $key => $value) {
                $GoogleTokenGenerator = new GoogleTokenGenerator();
                $token = $GoogleTokenGenerator->generateToken('en',$languages[$key]['lang'],$word);
                $url = 'https://translate.google.am/translate_a/single?client=t&sl=en&tl=' . $languages[$key]['lang'] . '&hl=en&dt=at&dt=bd&dt=ex&dt=ld&dt=md&dt=qca&dt=rw&dt=rm&dt=ss&dt=t&ie=UTF-8&oe=UTF-8&rom=1&ssel=0&tsel=0&kc=1&tk='.$token.'&q='.$word;
                $name_en = $this->curl($url);
                preg_match_all('/('.$value['regular'].')/ui',$name_en,$words_matches);
                $cleanWords = $words_matches[1];
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
            curl_setopt($ch, CURLOPT_POSTFIELDS, "text='.$word.'"); // add POST fields
//            curl_setopt($ch, CURLOPT_SSLVERSION, 3);
//            curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'SSLv3');
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $result = curl_exec($ch); // run the whole process
//            echo '<pre>';print_r(curl_error($ch));die;
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
            $model->setAttributes(array_merge([
                'date' => date("Y-m-d H:i:s"),
                'teach_priority' => 1,
                'userID' => Yii::$app->user->id,
            ],Yii::$app->request->post()));
            $words = $model->save();
        }
        return $this->render('add', ['words' => 44]);
    }


    public function actionUpdateWord()
    {
        $model = Words::findOne(['id' => Yii::$app->request->post('wordID')]);
        $model -> setAttributes([
            'in_russian' => Yii::$app->request->post('russian'),
            'in_armenian' => Yii::$app->request->post('armenian'),
            'word' => Yii::$app->request->post('english'),
        ]);

        if($model->validate()){
            $words = $model->update();
        }
    }


    public function actionDeleteWord()
    {
        if(Yii::$app->request->post('deleteAjax') && Yii::$app->request->post('wordID')){
            $model = new Words();
            $words = $model ->deleteAll(['id' => (int)Yii::$app->request->post('wordID')]);
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