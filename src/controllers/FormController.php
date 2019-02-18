<?php

namespace misterbk\optInMail\controllers;

use craft\web\Controller as Controller;
use misterbk\optInMail\models\OptInMail_SubmissionModel;
use Craft;
use misterbk\OptInMail\OptInMailPlugin as Plugin;

class FormController extends Controller {

    protected $allowAnonymous = true;
    public $enableCsrfValidation = false;

    public function actionSaveFormData()
    {
        $this->requirePostRequest();

        $post = \Craft::$app->getRequest()->post();
        if (!$post) {
            throw new Exception('Post was empty');
            return;
        }

        /* Maybe we should make the email input name configurable? Allows more flexibility. */
        if (empty($post['email'])) {
            throw new \Exception('No email field found in post');
            return;
        }

        $submission = new OptInMail_SubmissionModel();
        $submission->acceptDate = null;
        $submission->optInToken = uniqid() . uniqid();
        $submission->recipient = $post['email'];

        if (!$submission->validate()) {
            throw new \Exception($submission->getErrors());
        } else {
            $error_msg = Plugin::getInstance()->optInFormHandle->handlePost($submission, $post);
            return $this->asJson(array('success' => null === $error_msg, 'error_msg' => $error_msg));
        }
    }

    public function actionAcceptOptIn()
    {
        $token = \Craft::$app->getRequest()->get('optInToken');
        $error_msg = Plugin::getInstance()->optInFormHandle->verifyToken($token);

        $settings = Plugin::getInstance()->getSettings();

        return $this->renderTemplate($settings->success_page_template_path,
                [
                'success' => $error_msg === null,
                'error_msg' => $error_msg
                ]
            );
    }
}

