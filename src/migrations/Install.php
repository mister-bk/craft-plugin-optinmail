<?php

namespace misterbk\optInMail\migrations;

use craft\db\Migration;

/**
 * Install migration.
 */
class Install extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {

        foreach ($this->getTableData() as $data) {
            $this->createTable($data['table'], $data['fields']);
        }

        $this->addForeignKey(
            'FK_submission_submissionfield',
            '{{%optinmail_submissionfields}}',
            'submission',
            '{{%optinmail_submissions}}',
            'id'
        );

        $this->addForeignKey(
            'FK_field_submissionfield',
            '{{%optinmail_submissionfields}}',
            'field',
            '{{%optinmail_fields}}',
            'id'
        );

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "Deinstallation migration cannot be reverted.\n";

        $this->dropForeignKey('FK_field_submissionfield', '{{%optinmail_submissionfields}}');
        $this->dropForeignKey('FK_submission_submissionfield', '{{%optinmail_submissionfields}}');

        foreach ($this->getTableData() as $data) {
            $this->dropTableIfExists($data['table']);
        }
        return true;
    }

    /**
     * @return array
     */
    private function getTableData(): array
    {
        return [
            [
                'table'  => '{{%optinmail_fields}}',
                'fields' => [
                    'id' => $this->primaryKey(),
                    'name' => $this->string(255),
                    'formHandle' => $this->string(255),
                    'uid' => $this->string(255),
                    'dateCreated' => $this->dateTime(),
                    'dateUpdated' => $this->dateTime(),
                ],
            ],
            [
                'table'  => '{{%optinmail_submissionfields}}',
                'fields' => [
                    'id' => $this->primaryKey(),
                    'value' => $this->text(),
                    'submission' => $this->integer(),
                    'field' => $this->integer(),
                    'uid' => $this->string(255),
                    'dateCreated' => $this->dateTime(),
                    'dateUpdated' => $this->dateTime(),
                ],
            ],
            [
                'table'  => '{{%optinmail_submissions}}',
                'fields' => [
                    'id' => $this->primaryKey(),
                    'acceptDate' => $this->dateTime(),
                    'optInToken' => $this->string(255),
                    'recipient' => $this->string(255),
                    'uid' => $this->string(255),
                    'dateCreated' => $this->dateTime(),
                    'dateUpdated' => $this->dateTime(),
                ],
            ],
        ];
    }
}
