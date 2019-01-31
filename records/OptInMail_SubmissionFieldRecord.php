<?php
namespace Craft;

class OptInMail_SubmissionFieldRecord extends BaseRecord
{

    public function getTableName()
    {
        return 'optinmail_submissionfields';
    }

    protected function defineAttributes()
    {
        return array(
           'value' => array(AttributeType::String)
        );
    }

    public function defineRelations()
    {
        return array(
        'submission' => array(static::BELONGS_TO, 'OptInMail_SubmissionRecord'/*, 'submission_id'*/),
        'field' => array(static::BELONGS_TO, 'OptInMail_FieldRecord'/*, 'field_id'*/),
        );
    }
}
