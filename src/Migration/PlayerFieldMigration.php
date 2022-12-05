<?php

namespace HeimrichHannot\NewsBundle\Migration;

use Contao\CoreBundle\Migration\MigrationInterface;
use Contao\CoreBundle\Migration\MigrationResult;
use Doctrine\DBAL\Connection;

class PlayerFieldMigration implements MigrationInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getName(): string
    {
        return 'News Bundle Player Field Migration';
    }

    public function shouldRun(): bool
    {
        if (!in_array('player', array_keys($this->connection->getSchemaManager()->listTableColumns('tl_news')))) {
            return false;
        }

        $news = $this->connection->executeQuery("SELECT id FROM tl_news WHERE player='internal' OR player='external'");
        if ($news->rowCount() > 0) {
            return true;
        }

        return false;
    }

    public function run(): MigrationResult
    {
        $news = $this->connection->executeQuery("SELECT id,player FROM tl_news WHERE player='internal' OR player='external'");

        $updated = 0;


        if ($news->rowCount() > 1) {
            $stmt = $this->connection->prepare("UPDATE tl_news SET player=? WHERE id=?");

            while ($article = $news->fetchAssociative()) {
                switch ($article['player']) {
                    case 'internal':
                        $updated += $stmt->executeStatement(['internalplayer', $article['id']]);
                        break;
                    case 'external':
                        $updated += $stmt->executeStatement(['externalplayer', $article['id']]);
                }
            }
        }

        return new MigrationResult(true, "Finished News Bundle Player Field Migration. Updated $updated news entries.");

    }
}