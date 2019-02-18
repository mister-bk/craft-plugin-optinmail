<?php

namespace misterbk\optInMail\models;

use craft\base\Model;

class OptInMail_SubmissionModel extends Model
{
    public $acceptDate = null;
    public $fields = null;
    public $optInToken = null;
    public $recipient = null;
}