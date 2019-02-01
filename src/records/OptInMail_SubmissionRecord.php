<?php
namespace misterbk\optInMail;

class OptInMail_SubmissionRecord extends BaseRecord
{

    public function getTableName()
    {
        return 'optinmail_submissions';
    }

    protected function defineAttributes()
    {
        return array(
           'acceptDate' => AttributeType::DateTime,
           'optInToken' => AttributeType::String,
           'recipient' => AttributeType::Email,
        );
    }

    public function defineRelations()
    {
        return array(
            'submissionfields' => array(static::HAS_MANY, 'OptInMail_SubmissionFieldRecord', 'submissionId'),
        );
    }

    public function setSubmission(OptInMail_SubmissionModel $submissionModel)
    {
        $this->optInToken = uniqid() . uniqid();
        $this->recipient = $submissionModel->recipient;
        $this->save();

        foreach ($submissionModel->fields as $field) {
            $tmp = new OptInMail_SubmissionFieldRecord();
            $tmp->value = $field->value;
            $tmp->fieldId = $field->id;
            $tmp->submissionId = $this->id;
            $tmp->save();
        }
    }

    public function getValuesArray()
    {
        $result = array();
        foreach ($this->submissionfields as $f) {
            $result[$f->field->name] = $f->value;
        }
        return $result;
    }

}