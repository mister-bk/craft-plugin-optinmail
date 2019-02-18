<?php

namespace misterbk\optInMail\records;

use craft\db\ActiveRecord;
use misterbk\optInMail\models\OptInMail_SubmissionFieldModel;
use yii\db\ActiveQuery;
use misterbk\optInMail\models\OptInMail_SubmissionModel;
use misterbk\optInMail\records\OptInMail_FieldRecord;

class OptInMail_SubmissionRecord extends ActiveRecord
{
    const TABLENAME = '{{%optinmail_submissions}}';

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

    public function getFields()
    {
        return $this->hasMany(OptInMail_FieldRecord::className(), ['id' => 'field'])
            ->viaTable('{{%optinmail_submissionfields}}', ['submission' => 'id']);
    }

    public function getSubmissionFields() {
        return $this->hasMany(OptInMail_SubmissionFieldRecord::className(), ['submission' => 'id']);
    }

    public function setSubmission(OptInMail_SubmissionModel $submissionModel)
    {
        $this->optInToken = uniqid() . uniqid();
        $this->recipient = $submissionModel->recipient;
        $this->save();

        foreach ($submissionModel->fields as $field) {
            $tmp = new OptInMail_SubmissionFieldRecord();
            $tmp->value = $field->value;
            $tmp->field = $field->id;
            $tmp->submission = $this->id;
            $tmp->save();
        }
    }

    public function getValuesArray()
    {
        $result = array();
        foreach ($this->submissionFields as $f) {
            $result[$f->getField()->one()->name] = $f->value;
        }
        return $result;
    }

}