<?php
namespace misterbk\optInMail\services;

use yii\base\Component;
//use misterbk\optInMail\models\OptInMail_SubmissionFieldModel;
use misterbk\optInMail\models\OptInMail_SubmissionModel;
use misterbk\optInMail\models\OptInMail_FieldModel;
use misterbk\optInMail\records\OptInMail_SubmissionRecord;
use misterbk\optInMail\records\OptInMail_FieldRecord;
use misterbk\OptInMail\OptInMailPlugin as Plugin;
use craft\db\Query;
use craft\mail\Message;
use craft\helpers\UrlHelper;
use craft\web\View;

class HandleFormService extends Component//BaseApplicationComponent
{
    const FORM_HANDLE_FIELD_NAME = 'optInFormHandle';


    public function fieldExists(String $name, $formHandle)
    {
        $query = (new Query())
            ->select('id')
            ->from(['{{%optinmail_fields}}'])
            ->where(array(
                'name' => $name,
                'formHandle' => $formHandle))
            ->orderBy('id ASC')
            ->limit(1)
            ->all();
        return (count($query) !== 0);
    }

    public function populateFields($postArray)
    {
        assert(is_array($postArray));
        $formHandle = null;
        if (key_exists(self::FORM_HANDLE_FIELD_NAME, $postArray)) {
            $formHandle = $postArray[self::FORM_HANDLE_FIELD_NAME];
        }

        $result = array();
        foreach ($postArray as $key => $value) {
            if (!$this->fieldExists(trim($key), $formHandle)) {
                $field = new OptInMail_FieldModel();
                $field->name = trim($key);
                $field->value = trim($value);
                $field->formHandle = $formHandle;
                if ($field->validate()) {
                    assert(in_array($key, Plugin::getInstance()->settings->qualified_fieldnames), 'unqualified name found in post: "' . $key . '"');
                    //assert(in_array($key, \Craft::$app->config->get('qualified_fieldnames', 'optinmail'), 'unqualified name found in post: "' . $key . '"'));
                    $db_entry = new OptInMail_FieldRecord();
                    $db_entry->setField($field);
                    $db_entry->save();
                    $field->id = $db_entry->id;
                } else {
                    throw new Exception($field->getErrors());
                }
            } else {
                $field = $this->getField(trim($key));
                $field->value = trim($value);
            }
            $result[] = $field;
        }

        return $result;
    }

    private function getField(String $name)
    {
        $tmp = OptInMail_FieldRecord::findOne(array('name' => $name));
        $result = $tmp->getFieldModel();
        assert(!is_null($result), 'Requested field, not known to db. Please check Plugin Settings');
        return $result;
    }

    private function getFieldModel(array $fieldArray)
    {
        $result = new OptInMail_FieldModel();
        $result->name = $fieldArray['name'];
        $result->id = $fieldArray['id'];
        return $result;
    }

    public function handlePost(OptInMail_SubmissionModel $submission, array $post)
    {
        $settings = Plugin::getInstance()->getSettings();
        $error_msg = null;
        if ($settings->opt_in_mail_template_path === 'path/to/template.twig' || $settings->opt_in_mail_template_path === null ||
            $settings->opt_in_confirmation_mail_template_path === 'path/to/template.twig' || $settings->opt_in_confirmation_mail_template_path === null ||
            $settings->success_page_template_path === 'path/to/template.twig' ||  $settings->success_page_template_path === null)
		{
			throw new \Exception('Please make sure paths to all three template files are set correctly in plugin settings');
		}
        $submission->fields = $this->populateFields($post);

        if (!$submission->validate()) {
            $error_msg = $submission->getErrors();
            return $error_msg;
        }

        try {
            $submissionEntry = $this->saveSubmission($submission);
        } catch (Exception $e) {
            $error_msg = $e->getMessage();
        }
        if (!$settings->send_opt_in || $error_msg !== null) return $error_msg;

        try {
            $this->sendOptInMail($submissionEntry);
        } catch (Exception $e) {
            $error_msg = $e->getMessage();
        }

        return $error_msg;
    }

    private function saveSubmission(OptInMail_SubmissionModel $submission)
    {
        $result = new OptInMail_SubmissionRecord();
        $result->setSubmission($submission);
        $result->dateCreated = new \DateTime();
        return $result;
    }

    private function sendOptInMail(OptInMail_SubmissionRecord $submission)
    {
        $settings = Plugin::getInstance()->getSettings();
        $mailSettings = \Craft::$app->systemSettings->getSettings('email');
        $email = new Message();
        $email->setFrom([$mailSettings['fromEmail'] => $mailSettings['fromName']]);
        $email->setTo($submission->recipient);
        $email->setSubject($settings->subject_opt_in_mail);
        $link = UrlHelper::url('actions/opt-in-mail/form/accept-opt-in', array('optInToken' => $submission->optInToken));
        $html = \Craft::$app->view->renderTemplate($settings->opt_in_mail_template_path,
            [
            'optInData' => $submission->getValuesArray(),
            'optInLink' => $link
            ]
        );
        $email->setHtmlBody($html);

        \Craft::$app->mailer->send($email);
    }

    public function verifyToken(String $token) {
        $error_msg = null;

        $submissionRecord = OptInMail_SubmissionRecord::findOne(array('optInToken' => $token));

        if (null === $submissionRecord) {
            $error_msg = 'No submission found with the optInToken: "' . $token . '"';
        }

        if ($error_msg === null) {
            if (null !== $submissionRecord->acceptDate) {
                $error_msg = 'This submission was already accepted on: "' . $submissionRecord->acceptDate . '"';
            }
        }

        if ($error_msg === null) {
            $hourAgo = new \DateTime('-1 hour');
            if ($hourAgo < new \DateTime($submissionRecord->dateCreated)) {
                $success = $this->sendAcceptMails($submissionRecord);
                $submissionRecord->acceptDate = new \DateTime();
                $submissionRecord->save();
            } else {
                $error_msg = 'The submission is to old to be accepted now.';
            }
        }

        return $error_msg;
    }

    private function sendAcceptMails(OptInMail_SubmissionRecord $submission) {
        $settings = Plugin::getInstance()->getSettings();
        $mailSettings = \Craft::$app->systemSettings->getSettings('email');
        $email4User = new Message();
        $email4User->setFrom([$mailSettings['fromEmail'] => $mailSettings['fromName']]);
        $email4User->setTo($submission->recipient);
        $email4User->setSubject($settings->subject_success_mail);
        $html = \Craft::$app->view->renderTemplate($settings->opt_in_confirmation_mail_template_path,
            [
            'optInData' => $submission->getValuesArray(),
            ]
        );
        $email4User->setHtmlBody($html);

        $recipientList = explode(',', $settings->opt_in_success_recepient);

        $email4Owner = new Message();
        $email4Owner->setSubject($settings->subject_success_mail);
        $html = \Craft::$app->view->renderTemplate($settings->opt_in_confirmation_mail_template_path,
            [
            'optInData' => $submission->getValuesArray(),
            ]
        );
        $email4Owner->setHtmlBody($html);

        $success = true;
        try {
            \Craft::$app->mailer->send($email4User);

            foreach ($recipientList as $recipient) {
                if (!empty($recipient)) {
                    $email4Owner->setTo($recipient);
                    \Craft::$app->mailer->send($email4Owner);
                }
            }
        } catch (Exception $e) {
            $success = false;
            craft()->log($e->getMessage());
        }

        if (!$success) {
            craft()->log($e->getMessage());
            return false;
        }

        return true;
    }
}
