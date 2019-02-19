<?php

namespace misterbk\optInMail\records;

use craft\db\ActiveRecord;
use yii\db\ActiveQuery;

class SubmissionFieldRecord extends ActiveRecord
{
    const TABLENAME = '{{%optinmail_submissionfields}}';

    /**
     * @return string
     */
    public static function tableName()
    {
        return self::TABLENAME;
    }

    public function getField() {
        return $this->hasOne(FieldRecord::className(), ['id' => 'field']);
    }

    public function getSubmission() {
        return $this->hasOne(SubmissionRecord::className(), ['id' => 'submission']);
    }

    protected function defineAttributes()
    {
        return array(
           'value' => array(AttributeType::String)
        );
    }

//    public function defineRelations()
//    {
//        return array(
//        'submission' => array(static::BELONGS_TO, 'SubmissionRecord'/*, 'submission_id'*/),
//        'field' => array(static::BELONGS_TO, 'FieldRecord'/*, 'field_id'*/),
//        );
//    }
}
