<?php

namespace misterbk\optInMail\models;

use craft\base\Model;


class SubmissionFieldModel extends Model
{

    public $submission_id = null;
    public $field_id = null;
    public $value = null;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['send_opt_in'], 'boolean'],
        ];
    }
}
