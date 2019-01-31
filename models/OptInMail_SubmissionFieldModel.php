<?php
namespace Craft;

class OptInMail_SubmissionFieldModel extends BaseModel
{

    protected function defineAttributes()
    {
        return array(
           'submission_id' => array(AttributeType::Number),
           'field_id' => array(AttributeType::Number),
           'value' => array(AttributeType::String)
        );
    }
}
