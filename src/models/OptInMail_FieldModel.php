<?php
namespace misterbk\optInMail;

class OptInMail_FieldModel extends BaseModel
{

    protected function defineAttributes()
    {
        return array(
           'id' => AttributeType::Number,
           'name' => AttributeType::String,
           'formHandle' => AttributeType::String,
           'value' => AttributeType::String
        );
    }
}
