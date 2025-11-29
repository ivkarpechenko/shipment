<?php

declare(strict_types=1);

namespace App\Application\Directory\Command;

use App\Application\CommandBus;
use App\Application\CommandHandler;
use App\Domain\Directory\Dto\OkatoOktmoDto;
use Exception;
use InvalidArgumentException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Psr\Log\LoggerInterface;

readonly class ImportOkatoOktmoCommandHandler implements CommandHandler
{
    public function __construct(
        private LoggerInterface $logger,
        private CommandBus $commandBus,
    ) {
    }

    public function __invoke(ImportOkatoOktmoCommand $command): void
    {
        $filePath = $command->filepath;

        if (!file_exists($filePath)) {
            throw new InvalidArgumentException("Файл не найден: {$filePath}");
        }

        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();

        foreach ($worksheet->getRowIterator(2) as $row) {
            $rowData = [];
            foreach ($row->getCellIterator() as $cell) {
                $rowData[] = trim((string) $cell->getValue());
                if (count($rowData) >= 3) {
                    break;
                }
            }

            if (count($rowData) === 3) {
                try {
                    $dto = new OkatoOktmoDto(
                        okato: $this->specialCharactersRemove($rowData[0]),
                        oktmo: $this->normalizeOktmo($rowData[1]),
                        location: $rowData[2]
                    );

                    $this->commandBus->dispatch(new CreateOkatoOktmoCommand($dto));
                } catch (Exception $e) {
                    $this->logger->error(sprintf(
                        'Ошибка при сохранении записи OKATO %s: %s',
                        $rowData[0],
                        $e->getMessage()
                    ));
                }
            }
        }
    }

    private function specialCharactersRemove(string $value): string
    {
        return preg_replace('/[\pZ\pC]+/u', '', $value);
    }

    private function normalizeOktmo(string $oktmo): string
    {
        $oktmo = $this->specialCharactersRemove($oktmo);

        if (strlen($oktmo) < 8) {
            return str_pad($oktmo, 8, '0', STR_PAD_LEFT);
        }

        if (strlen($oktmo) > 8 && strlen($oktmo) < 11) {
            return str_pad($oktmo, 11, '0', STR_PAD_LEFT);
        }

        return $oktmo;
    }
}
