<?php
namespace misterbk\optInMail;

class OptInMail_FieldRecord extends BaseRecord
{

    public function getTableName()
    {
        return 'optinmail_fields';
    }

    protected function defineAttributes()
    {
        return array(
           'name' => array(AttributeType::String, 'unique' => true),
           'formHandle' => array(AttributeType::String)
        );
    }

    public function defineRelations()
    {
        return array(
            'submissionfields' => array(static::HAS_MANY, 'OptInMail_SubmissionFieldRecord', 'fieldId'),
        );
    }

    public function setField(OptInMail_FieldModel $field)
    {
        $this->name = $field->name;
        $this->formHandle = $field->formHandle;
    }

    public function getFieldModel()
    {
        $result = new OptInMail_FieldModel();
        $result->name = $this->name;
        $result->id = $this->id;
        return $result;
    }
}
