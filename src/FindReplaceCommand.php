<?php namespace Spline;

use ZipArchive;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FindReplaceCommand extends Command
{

    public function configure()
    {
        $this->setName('findReplace')
            ->setDescription('Find and replace none tajik characters.')
            ->addArgument('file', InputArgument::REQUIRED, 'The file to be processed');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        if (! class_exists('ZipArchive')) {
            throw new RuntimeException('The Zip PHP extension is not installed. Please install it and try again.');
        }

        $docxFile = getcwd().'/'.$input->getArgument('file');
        $this->verifyFileExist($docxFile);

        $zip = new ZipArchive();
        if (! $zip->open($docxFile)) {
            throw new RuntimeException('Can not open file.');
        }

        $documentXml = $zip->getFromName('word/document.xml');
        $documentXml = str_replace(
            array_keys($this->getCharacters()), array_values($this->getCharacters()
        ), $documentXml);

        $zip->deleteName('word/document.xml');
        $zip->addFromString('word/document.xml', $documentXml);
        $zip->close();

        $message = "The file {$input->getArgument('file')} was successfully processed";
        $output->writeln("<info>{$message}</info>");
    }

    /**
     * @param $docxFile
     * @return void
     */
    protected function verifyFileExist($docxFile)
    {
        if (! is_file($docxFile)) {
            throw new RuntimeException('File does not exist!');
        }
    }

    /**
     * @return array
     */
    protected function getCharacters()
    {
        return [
            'њ' => 'ҳ', 'Њ' => 'Ҳ',
            'љ' => 'ҷ', 'Љ' => 'Ҷ',
            'ќ' => 'қ', 'Ќ' => 'Қ',
            'ў' => 'ӯ', 'Ў' => 'Ӯ',
            'ѓ' => 'ғ', 'Ѓ' => 'Ғ',
            'ї' => 'ӣ',
        ];
    }
}