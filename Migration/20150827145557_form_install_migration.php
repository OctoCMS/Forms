<?php

use \Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Migration\AbstractMigration;

class FormInstallMigration extends AbstractMigration
{
    public function up()
    {
        // Create tables:
        $this->createForm();
        $this->createSubmission();

        // Add foreign keys:
        $table = $this->table('submission');

        if (!$table->hasForeignKey('form_id')) {
            $table->addForeignKey('form_id', 'form', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE']);
            $table->save();
        }

        if (!$table->hasForeignKey('contact_id')) {
            $table->addForeignKey('contact_id', 'contact', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE']);
            $table->save();
        }
    }

    protected function createForm()
    {
        $table = $this->table('form', ['id' => false, 'primary_key' => ['id']]);

        if (!$this->hasTable('form')) {
            $table->addColumn('id', 'integer', ['signed' => false, 'null' => false]);
            $table->create();
        }

        if (!$table->hasColumn('title')) {
            $table->addColumn('title', 'string', ['limit' => 250, 'null' => false]);
        }

        if (!$table->hasColumn('recipients')) {
            $table->addColumn('recipients', 'text', ['null' => true, 'default' => null]);
        }

        if (!$table->hasColumn('definition')) {
            $table->addColumn('definition', 'text', ['limit' => MysqlAdapter::TEXT_MEDIUM, 'null' => false]);
        }

        if (!$table->hasColumn('thankyou_message')) {
            $table->addColumn('thankyou_message', 'text', ['limit' => MysqlAdapter::TEXT_MEDIUM, 'null' => true, 'default' => null]);
        }

        $table->save();

        $table->changeColumn('title', 'string', ['limit' => 250, 'null' => false]);
        $table->changeColumn('recipients', 'text', ['null' => true, 'default' => null]);
        $table->changeColumn('definition', 'text', ['limit' => MysqlAdapter::TEXT_MEDIUM, 'null' => false]);
        $table->changeColumn('thankyou_message', 'text', ['limit' => MysqlAdapter::TEXT_MEDIUM, 'null' => true, 'default' => null]);

        $table->save();
    }

    protected function createSubmission()
    {
        $table = $this->table('submission', ['id' => false, 'primary_key' => ['id']]);

        if (!$this->hasTable('submission')) {
            $table->addColumn('id', 'integer', ['signed' => false, 'null' => false]);
            $table->create();
        }

        if (!$table->hasColumn('form_id')) {
            $table->addColumn('form_id', 'integer', ['signed' => false, 'null' => false]);
        }

        if (!$table->hasColumn('created_date')) {
            $table->addColumn('created_date', 'datetime', ['null' => false]);
        }

        if (!$table->hasColumn('contact_id')) {
            $table->addColumn('contact_id', 'integer', ['signed' => false, 'null' => false]);
        }

        if (!$table->hasColumn('extra')) {
            $table->addColumn('extra', 'text', ['limit' => MysqlAdapter::TEXT_MEDIUM, 'null' => true, 'default' => null]);
        }

        if (!$table->hasColumn('message')) {
            $table->addColumn('message', 'text', ['limit' => MysqlAdapter::TEXT_MEDIUM, 'null' => true, 'default' => null]);
        }

        $table->save();

        $table->changeColumn('form_id', 'integer', ['signed' => false, 'null' => false]);
        $table->changeColumn('created_date', 'datetime', ['null' => false]);
        $table->changeColumn('contact_id', 'integer', ['signed' => false, 'null' => false]);
        $table->changeColumn('extra', 'text', ['limit' => MysqlAdapter::TEXT_MEDIUM, 'null' => true, 'default' => null]);
        $table->changeColumn('message', 'text', ['limit' => MysqlAdapter::TEXT_MEDIUM, 'null' => true, 'default' => null]);

        $table->save();
    }
}
