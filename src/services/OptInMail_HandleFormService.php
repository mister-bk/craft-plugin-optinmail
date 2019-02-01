<?php
namespace misterbk\optInMail;

class OptInMail_HandleFormService extends BaseApplicationComponent
{
    const FORM_HANDLE_FIELD_NAME = 'optInFormHandle';


    public function fieldExists(String $name, $formHandle)
    {
        $query = craft()->db->createCommand()
            ->select('id')
            ->from('optinmail_fields')
            ->where(array(
                'name' => $name,
                'formHandle' => $formHandle))
            ->order('id ASC')
            ->limit(1)
            ->queryAll();
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
                    assert(in_array($key, Craft::$app->config->optinmail->qualified_fieldnames), 'unqualified name found in post: "' . $key . '"');
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
        $tmp = OptInMail_FieldRecord::model()->findByAttributes(array('name' => $name));
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
        $settings = craft()->plugins->getPlugin('optinmail')->getSettings();
        $error_msg = null;
        if ($settings->opt_in_mail_template_path === 'path/to/template.twig' ||
            $settings->opt_in_confirmation_mail_template_path === 'path/to/template.twig' ||
            $settings->success_page_template_path === 'path/to/template.twig')
		{
			throw new Exception('Please make sure paths to all three template files are set correctly in plugin settings');
		}
        $submission->fields = $this->populateFields($post);
        if (!$submission->validate()) {
            $error_msg = $submission->getErrors;
            return;
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
        return $result;
    }

    private function sendOptInMail(OptInMail_SubmissionRecord $submission)
    {
        $settings = craft()->plugins->getPlugin('optinmail')->getSettings();
        $email = new EmailModel();
        $email->toEmail = $submission->recipient;
        $email->subject = $settings->subject_opt_in_mail;
        $link = UrlHelper::getActionUrl('optInMail/form/acceptOptIn', array('optInToken' => $submission->optInToken));
        $html = craft()->view->render($settings->opt_in_mail_template_path,
            [
            'optInData' => $submission->getValuesArray(),
            'optInLink' => $link
            ]
        );
        $email->htmlBody = $html;

        craft()->email->sendEmail($email);
    }

    public function verifyToken(String $token) {
        $error_msg = null;

        $submissionRecord = OptInMail_SubmissionRecord::model()->findByAttributes(array('optInToken' => $token));

        if (null === $submissionRecord) {
            $error_msg = 'No submission found with the optInToken: "' . $token . '"';
        }

        if ($error_msg === null) {
            if (null !== $submissionRecord->acceptDate) {
                $error_msg = 'This submission was already accepted on: "' . $submissionRecord->acceptDate . '"';
            }
        }

        if ($error_msg === null) {
            $hourAgo = new DateTime('-1 hour');
            if ($hourAgo < $submissionRecord->dateCreated) {
                $success = $this->sendAcceptMails($submissionRecord);
                $submissionRecord->acceptDate = new DateTime();
                $submissionRecord->save();
            } else {
                $error_msg = 'The submission is to old to be accepted now.';
            }
        }

        return $error_msg;
    }

    private function sendAcceptMails(OptInMail_SubmissionRecord $submission) {
        $settings = craft()->plugins->getPlugin('optinmail')->getSettings();
        $email4User = new EmailModel();
        $email4User->toEmail = $submission->recipient;
        $email4User->subject = $settings->subject_success_mail;
        $html = craft()->view->render($settings->opt_in_confirmation_mail_template_path,
            [
            'optInData' => $submission->getValuesArray(),
            ]
        );
        $email4User->htmlBody = $html;

        $recipientList = explode(',', $settings->opt_in_success_recepient);

        $email4Owner = new EmailModel();
        $email4Owner->subject = $settings->subject_success_mail;
        $html = craft()->view->render($settings->opt_in_confirmation_mail_template_path,
            [
            'optInData' => $submission->getValuesArray(),
            ]
        );
        $email4Owner->htmlBody = $html;

        $success = true;
        try {
            craft()->email->sendEmail($email4User);

            foreach ($recipientList as $recipient) {
                if (!empty($recipient)) {
                    $email4Owner->toEmail = $recipient;
                    craft()->email->sendEmail($email4Owner);
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
