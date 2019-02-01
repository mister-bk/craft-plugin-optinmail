<?php

namespace misterbk\optInMail;

class OptInMail_FormController extends BaseController {

    protected $allowAnonymous = true;

    public function actionSaveFormData()
    {
        $this->requirePostRequest();

        $post = craft()->getRequest()->getPost();
        if (!$post) {
            throw new Exception('Post was empty');
            return;
        }

        /* Maybe we should make the email input name configurable? Allows more flexibility. */
        if (empty($post['email'])) {
            throw new Exception('No email field found in post');
            return;
        }

        $submission = new OptInMail_SubmissionModel();
        $submission->acceptDate = null;
        $submission->optInToken = uniqid() . uniqid();
        $submission->recipient = $post['email'];

        if (!$submission->validate()) {
            throw new Exception($submission->getErrors());
        } else {
            $error_msg = craft()->optInMail_handleForm->handlePost($submission, $post);
            $this->returnJson(array('success' => null === $error_msg, 'error_msg' => $error_msg));
        }
    }

    public function actionAcceptOptIn()
    {
        $token = craft()->request->getQuery('optInToken');
        $error_msg = craft()->optInMail_handleForm->verifyToken($token);

        $settings = craft()->plugins->getPlugin('optinmail')->getSettings();

        return $this->renderTemplate($settings->success_page_template_path,
                [
                'success' => $error_msg === null,
                'error_msg' => $error_msg
                ]
            );
    }
}
