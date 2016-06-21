<?php

use Phinx\Migration\AbstractMigration;

class FormsJsonColumns extends AbstractMigration
{
    public function up()
    {
        $this->execute("UPDATE form SET definition = '[]' WHERE definition = '' OR definition IS NULL");
        $this->table('form')
            ->changeColumn('definition', \Phinx\Db\Adapter\AdapterInterface::PHINX_TYPE_JSON, ['null' => false])
            ->save();


        $this->execute("UPDATE submission SET `extra` = '{}' WHERE `extra` = '' OR extra IS NULL");
        $this->table('submission')
            ->changeColumn('extra', \Phinx\Db\Adapter\AdapterInterface::PHINX_TYPE_JSON, ['null' => false])
            ->save();
    }
}
