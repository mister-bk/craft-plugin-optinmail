<?php
namespace misterbk\optInMail\records;

use craft\db\ActiveRecord;
use yii\db\ActiveQuery;
use misterbk\optInMail\models\FieldModel;

class FieldRecord extends ActiveRecord
{

    const TABLENAME = '{{%optinmail_fields}}';

    /**
     * @return string
     */
    public static function tableName()
    {
        return self::TABLENAME;
    }

    protected function defineAttributes()
    {
        return array(
           'name' => array(AttributeType::String, 'unique' => true),
           'formHandle' => array(AttributeType::String)
        );
    }

//    public function defineRelations()
//    {
//        return array(
//            'submissionfields' => array(static::HAS_MANY, 'SubmissionFieldRecord', 'fieldId'),
//        );
//    }

    public function setField(FieldModel $field)
    {
        $this->name = $field->name;
        $this->formHandle = $field->formHandle;
    }

    public function getFieldModel()
    {
        $result = new FieldModel();
        $result->name = $this->name;
        $result->formHandle = $this->formHandle;
        $result->uid = $this->uid;
        $result->id = $this->id;
        return $result;
    }
}
