<?php

namespace misterbk\optInMail\records;

use craft\db\ActiveRecord;
use yii\db\ActiveQuery;
use misterbk\optInMail\models\OptInMail_SubmissionModel;
use misterbk\optInMail\records\OptInMail_FieldRecord;
use misterbk\optInMail\records\OptInMail_SubmissionRecord;

class OptInMail_SubmissionFieldRecord extends ActiveRecord
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
        return $this->hasOne(OptInMail_FieldRecord::className(), ['id' => 'field']);
    }

    public function getSubmission() {
        return $this->hasOne(OptInMail_SubmissionRecord::className(), ['id' => 'submission']);
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
