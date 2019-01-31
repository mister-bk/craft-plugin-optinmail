<?php
namespace Craft;

class OptInMail_SubmissionModel extends BaseModel
{

    protected function defineAttributes()
    {
        return array(
           'acceptDate' => AttributeType::DateTime,
           'fields' => AttributeType::Mixed,
           'optInToken' => AttributeType::String,
           'recipient' => AttributeType::Email,
        );
    }
}